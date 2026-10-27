
<?php

require_once __DIR__ . '/../Core/ket_noi_csdl.php';
require_once __DIR__ . '/../Core/xac_thuc.php';

class SuKienController
{
    /**
     * Lay danh sach su kien theo quyen nguoi dung.
     */

/**
 * Lay danh sach su kien theo quyen nguoi dung.
 */
public function danhSach()
{
    global $pdo;

    yeuCauDangNhap();
    $nguoiDung = nguoiDungHienTai();

    // Tu dong ket thuc su kien cong khai khi da qua thoi gian ket thuc.
    $pdo->prepare("
        UPDATE su_kien
        SET trang_thai = 'ket_thuc'
        WHERE trang_thai = 'cong_khai'
          AND thoi_gian_ket_thuc IS NOT NULL
          AND thoi_gian_ket_thuc <= NOW()
    ")->execute();

    // Dem so nguoi dang ky hop le va so nguoi da tham gia.
    $joinThongKe = "
        LEFT JOIN (
            SELECT
                su_kien_id,
                COUNT(
                    CASE
                        WHEN trang_thai IN ('da_dang_ky', 'da_tham_gia')
                        THEN 1
                    END
                ) AS so_nguoi_dang_ky,
                COUNT(
                    CASE
                        WHEN trang_thai = 'da_tham_gia'
                        THEN 1
                    END
                ) AS so_nguoi_da_tham_gia
            FROM dang_ky
            GROUP BY su_kien_id
        ) tk ON tk.su_kien_id = s.id
    ";

    $cotThongKe = ",
        COALESCE(tk.so_nguoi_dang_ky, 0) AS so_nguoi_dang_ky,
        COALESCE(tk.so_nguoi_da_tham_gia, 0) AS so_nguoi_da_tham_gia,
        GREATEST(
            s.so_luong_toi_da - COALESCE(tk.so_nguoi_dang_ky, 0),
            0
        ) AS so_cho_con_lai
    ";

    if ($nguoiDung['vai_tro'] === 'ban_to_chuc') {
        $sql = "
            SELECT
                s.id,
                s.ten_su_kien,
                s.dia_diem,
                s.thoi_gian_bat_dau,
                s.so_luong_toi_da,
                s.trang_thai
                $cotThongKe
            FROM su_kien s
            INNER JOIN cau_lac_bo clb
                ON s.cau_lac_bo_id = clb.id
            $joinThongKe
            WHERE clb.nguoi_quan_ly_id = :nguoi_dung_id
            ORDER BY s.ngay_tao DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nguoi_dung_id' => (int) $nguoiDung['id']
        ]);
    } elseif ($nguoiDung['vai_tro'] === 'admin') {
        $sql = "
            SELECT
                s.id,
                s.ten_su_kien,
                s.dia_diem,
                s.thoi_gian_bat_dau,
                s.so_luong_toi_da,
                s.trang_thai
                $cotThongKe
            FROM su_kien s
            $joinThongKe
            ORDER BY s.ngay_tao DESC
        ";

        $stmt = $pdo->query($sql);
    } else {
        $sql = "
            SELECT
                s.id,
                s.ten_su_kien,
                s.dia_diem,
                s.thoi_gian_bat_dau,
                s.so_luong_toi_da,
                s.trang_thai
                $cotThongKe
            FROM su_kien s
            $joinThongKe
            WHERE s.trang_thai = 'cong_khai'
            ORDER BY s.ngay_tao DESC
        ";

        $stmt = $pdo->query($sql);
    }

    $suKienList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require __DIR__ . '/../Views/ban_to_chuc/danh_sach_su_kien.php';
}


private function docNgayGio(string $giaTri): ?DateTime
{
    $ngayGio = DateTime::createFromFormat('!Y-m-d\TH:i', $giaTri);
    $loi = DateTime::getLastErrors();

    if (
        $ngayGio === false ||
        ($loi !== false && (
            $loi['warning_count'] > 0 ||
            $loi['error_count'] > 0
        ))
    ) {
        return null;
    }

    return $ngayGio;
}

    /**
     * Them su kien moi.
     */


public function them()
{
    global $pdo;

    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    // Kiểm tra CSRF token.
kiemTraCsrfToken();

    $nguoiDung = nguoiDungHienTai();

    if (!$nguoiDung) {
        yeuCauDangNhap();
    }

    $nguoiTaoId = (int) $nguoiDung['id'];

    $tenSuKien = trim($_POST['ten_su_kien'] ?? '');
    $moTa = trim($_POST['mo_ta'] ?? '');
    $diaDiem = trim($_POST['dia_diem'] ?? '');
    $batDau = trim($_POST['thoi_gian_bat_dau'] ?? '');
    $ketThuc = trim($_POST['thoi_gian_ket_thuc'] ?? '');
    $hanDangKy = trim($_POST['han_dang_ky'] ?? '');

    $soLuong = filter_var(
        $_POST['so_luong_toi_da'] ?? null,
        FILTER_VALIDATE_INT
    );

    $trangThai = $_POST['trang_thai'] ?? 'nhap';

    if (
        $tenSuKien === '' ||
        $soLuong === false ||
        $soLuong === null ||
        $soLuong < 1 ||
        !in_array($trangThai, ['nhap', 'cong_khai'], true)
    ) {
        http_response_code(400);
        exit('Du lieu khong hop le. Vui long kiem tra lai.');
    }

    // Đọc thời gian bắt đầu.
    $batDauNgay = $this->docNgayGio($batDau);

    if ($batDauNgay === null) {
        http_response_code(400);
        exit('Thoi gian bat dau khong hop le.');
    }

    // Đọc và kiểm tra thời gian kết thúc.
    $ketThucNgay = null;

    if ($ketThuc !== '') {
        $ketThucNgay = $this->docNgayGio($ketThuc);

        if ($ketThucNgay === null || $ketThucNgay <= $batDauNgay) {
    http_response_code(400);
    exit('Thời gian kết thúc phải sau thời gian bắt đầu.');
}
    }

    // Kiểm tra hạn đăng ký.
    $hanDangKyNgay = null;

    if ($hanDangKy !== '') {
        $hanDangKyNgay = $this->docNgayGio($hanDangKy);

        if (
            $hanDangKyNgay === null ||
            $hanDangKyNgay > $batDauNgay
        ) {
            http_response_code(400);
            exit('Han dang ky phai truoc hoac bang thoi gian bat dau.');
        }
    }

    // Xác định câu lạc bộ được phép tạo sự kiện.
    if ($nguoiDung['vai_tro'] === 'admin') {
        $cauLacBoId = filter_var(
            $_POST['cau_lac_bo_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$cauLacBoId || $cauLacBoId < 1) {
            http_response_code(400);
            exit('Vui long chon cau lac bo.');
        }

        $stmt = $pdo->prepare(
            'SELECT id FROM cau_lac_bo WHERE id = :id'
        );
        $stmt->execute(['id' => $cauLacBoId]);

        if (!$stmt->fetchColumn()) {
            http_response_code(400);
            exit('Cau lac bo khong ton tai.');
        }
    } else {
        $stmt = $pdo->prepare(
            'SELECT id FROM cau_lac_bo
             WHERE nguoi_quan_ly_id = :nguoi_dung_id
             LIMIT 1'
        );

        $stmt->execute([
            'nguoi_dung_id' => $nguoiTaoId
        ]);

        $cauLacBoId = $stmt->fetchColumn();

        if (!$cauLacBoId) {
            http_response_code(403);
            exit('Ban chua duoc gan quan ly cau lac bo.');
        }
    }

    $sql = "
        INSERT INTO su_kien (
            cau_lac_bo_id,
            ten_su_kien,
            mo_ta,
            dia_diem,
            thoi_gian_bat_dau,
            thoi_gian_ket_thuc,
            han_dang_ky,
            so_luong_toi_da,
            trang_thai,
            nguoi_tao_id
        ) VALUES (
            :cau_lac_bo_id,
            :ten_su_kien,
            :mo_ta,
            :dia_diem,
            :bat_dau,
            :ket_thuc,
            :han_dang_ky,
            :so_luong,
            :trang_thai,
            :nguoi_tao_id
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'cau_lac_bo_id' => (int) $cauLacBoId,
        'ten_su_kien' => $tenSuKien,
        'mo_ta' => $moTa !== '' ? $moTa : null,
        'dia_diem' => $diaDiem !== '' ? $diaDiem : null,
        'bat_dau' => $batDauNgay->format('Y-m-d H:i:s'),
        'ket_thuc' => $ketThucNgay !== null
            ? $ketThucNgay->format('Y-m-d H:i:s')
            : null,
        'han_dang_ky' => $hanDangKyNgay !== null
            ? $hanDangKyNgay->format('Y-m-d H:i:s')
            : null,
        'so_luong' => $soLuong,
        'trang_thai' => $trangThai,
        'nguoi_tao_id' => $nguoiTaoId
    ]);

    header('Location: /Nhom-4-LTW/public/index.php?trang=danh_sach');
    exit;
}



    /**
     * Kiem tra su kien co thuoc quyen quan ly cua nguoi dung hay khong.
     */
    private function laySuKienDuocPhep(int $id): ?array
    {
        global $pdo;

        $nguoiDung = nguoiDungHienTai();

        if ($nguoiDung['vai_tro'] === 'admin') {
            $stmt = $pdo->prepare(
                'SELECT * FROM su_kien WHERE id = :id LIMIT 1'
            );
            $stmt->execute(['id' => $id]);
        } else {
            $stmt = $pdo->prepare("
                SELECT s.*
                FROM su_kien s
                INNER JOIN cau_lac_bo clb
                    ON s.cau_lac_bo_id = clb.id
                WHERE s.id = :id
                  AND clb.nguoi_quan_ly_id = :nguoi_dung_id
                LIMIT 1
            ");
            $stmt->execute([
                'id' => $id,
                'nguoi_dung_id' => (int) $nguoiDung['id']
            ]);
        }

        $suKien = $stmt->fetch(PDO::FETCH_ASSOC);

        return $suKien ?: null;
    }

    /**
     * Hien thi bieu mau sua su kien.
     */

public function sua()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('Ma su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen sua.');
    }

    // Không cho sửa sự kiện đã hủy hoặc đã kết thúc.
    if (in_array($suKien['trang_thai'], ['da_huy', 'ket_thuc'], true)) {
        header('Location: /Nhom-4-LTW/public/index.php?trang=danh_sach');
        exit;
    }

    require __DIR__ . '/../Views/ban_to_chuc/sua_su_kien.php';
}

    /**
     * Luu thay doi su kien.
     */


public function capNhat()
{
    global $pdo;

    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    // Kiểm tra CSRF nếu ứng dụng đã có hàm này.
    if (function_exists('kiemTraCsrfToken')) {
        kiemTraCsrfToken();
    }

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('Ma su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen sua.');
    }

    // Không cho sửa sự kiện đã hủy hoặc đã kết thúc.
    if (in_array($suKien['trang_thai'], ['da_huy', 'ket_thuc'], true)) {
        http_response_code(403);
        exit('Khong the sua su kien da huy hoac da ket thuc.');
    }

    $tenSuKien = trim($_POST['ten_su_kien'] ?? '');
    $moTa = trim($_POST['mo_ta'] ?? '');
    $diaDiem = trim($_POST['dia_diem'] ?? '');
    $batDau = trim($_POST['thoi_gian_bat_dau'] ?? '');
    $ketThuc = trim($_POST['thoi_gian_ket_thuc'] ?? '');
    $hanDangKy = trim($_POST['han_dang_ky'] ?? '');

    $soLuong = filter_var(
        $_POST['so_luong_toi_da'] ?? null,
        FILTER_VALIDATE_INT
    );

    $trangThaiMoi = $_POST['trang_thai'] ?? '';

    if (
        $tenSuKien === '' ||
        $soLuong === false ||
        $soLuong === null ||
        $soLuong < 1 ||
        !in_array($trangThaiMoi, ['nhap', 'cong_khai'], true)
    ) {
        http_response_code(400);
        exit('Du lieu khong hop le. Vui long kiem tra lai.');
    }

    // Đọc thời gian bắt đầu.
    $batDauNgay = $this->docNgayGio($batDau);

    if ($batDauNgay === null) {
        http_response_code(400);
        exit('Thoi gian bat dau khong hop le.');
    }

    // Đọc và kiểm tra thời gian kết thúc.
    $ketThucNgay = null;

    if ($ketThuc !== '') {
        $ketThucNgay = $this->docNgayGio($ketThuc);

        if (
            $ketThucNgay === null ||
            $ketThucNgay <= $batDauNgay
        ) {
            http_response_code(400);
            exit('Thoi gian ket thuc phai sau thoi gian bat dau.');
        }
    }

    // Kiểm tra hạn đăng ký.
    $hanDangKyNgay = null;

    if ($hanDangKy !== '') {
        $hanDangKyNgay = $this->docNgayGio($hanDangKy);

        if (
            $hanDangKyNgay === null ||
            $hanDangKyNgay > $batDauNgay
        ) {
            http_response_code(400);
            exit('Han dang ky phai truoc hoac bang thoi gian bat dau.');
        }
    }

    $sql = "
        UPDATE su_kien
        SET ten_su_kien = :ten_su_kien,
            mo_ta = :mo_ta,
            dia_diem = :dia_diem,
            thoi_gian_bat_dau = :bat_dau,
            thoi_gian_ket_thuc = :ket_thuc,
            han_dang_ky = :han_dang_ky,
            so_luong_toi_da = :so_luong,
            trang_thai = :trang_thai
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'ten_su_kien' => $tenSuKien,
        'mo_ta' => $moTa !== '' ? $moTa : null,
        'dia_diem' => $diaDiem !== '' ? $diaDiem : null,
        'bat_dau' => $batDauNgay->format('Y-m-d H:i:s'),
        'ket_thuc' => $ketThucNgay !== null
            ? $ketThucNgay->format('Y-m-d H:i:s')
            : null,
        'han_dang_ky' => $hanDangKyNgay !== null
            ? $hanDangKyNgay->format('Y-m-d H:i:s')
            : null,
        'so_luong' => $soLuong,
        'trang_thai' => $trangThaiMoi,
        'id' => (int) $id
    ]);

    header('Location: /Nhom-4-LTW/public/index.php?trang=danh_sach');
    exit;
}



    /**
     * Xoa su kien.
     */

public function xoa()
{
    global $pdo;

    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    // Kiểm tra CSRF trước khi xử lý thao tác xóa.
    kiemTraCsrfToken();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('Ma su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

if (!$suKien) {
    http_response_code(404);
    exit('Khong tim thay su kien hoac ban khong co quyen xoa.');
}

// Kiểm tra sự kiện đã có người đăng ký hay chưa.
$stmt = $pdo->prepare(
    'SELECT COUNT(*) FROM dang_ky WHERE su_kien_id = :id'
);

$stmt->execute(['id' => (int) $id]);

$soDangKy = (int) $stmt->fetchColumn();

if ($soDangKy > 0) {
    $_SESSION['thong_bao_loi'] =
        'Không thể xóa sự kiện đã có người đăng ký. Bạn có thể hủy sự kiện thay thế.';

    header('Location: /Nhom-4-LTW/public/index.php?trang=danh_sach');
    exit;
}

// Chỉ xóa nếu sự kiện chưa có bản ghi đăng ký.
$stmt = $pdo->prepare(
    'DELETE FROM su_kien WHERE id = :id'
);

$stmt->execute(['id' => (int) $id]);

header('Location: /Nhom-4-LTW/public/index.php?trang=danh_sach');
exit;
}


    /**
     * Huy su kien.
     */

public function huy()
{
    global $pdo;

    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    // Kiểm tra CSRF trước khi xử lý thao tác hủy.
    kiemTraCsrfToken();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen thao tac.');
    }

    if (in_array($suKien['trang_thai'], ['da_huy', 'ket_thuc'], true)) {
        header('Location: /Nhom-4-LTW/public/index.php?trang=danh_sach');
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE su_kien
        SET trang_thai = 'da_huy'
        WHERE id = :id
    ");
    $stmt->execute(['id' => (int) $id]);

    header('Location: /Nhom-4-LTW/public/index.php?trang=danh_sach');
    exit;
}


/**
 * Hien thi danh sach nguoi dang ky cua mot su kien.
 */
public function danhSachDangKy()
{
    global $pdo;

    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('Ma su kien khong hop le.');
    }

    // Kiem tra su kien co thuoc quyen quan ly cua nguoi dung hay khong.
    $suKien = $this->laySuKienDuocPhep((int) $id);

if (!$suKien) {
    http_response_code(404);
    exit('Khong tim thay su kien hoac ban khong co quyen xem.');
}

$suKien['id'] = (int) $id;

    // Lay danh sach nguoi da dang ky, khong hien nguoi da huy dang ky.
    $sql = "
        SELECT
            dk.id AS dang_ky_id,
            nd.ho_ten,
            nd.email,
            dk.thoi_gian_dang_ky,
            dk.trang_thai
        FROM dang_ky dk
        INNER JOIN nguoi_dung nd
            ON dk.nguoi_dung_id = nd.id
        WHERE dk.su_kien_id = :su_kien_id
          AND dk.trang_thai IN ('da_dang_ky', 'da_tham_gia')
        ORDER BY dk.thoi_gian_dang_ky DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['su_kien_id' => (int) $id]);

    $dangKyList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require __DIR__ . '/../Views/ban_to_chuc/danh_sach_dang_ky.php';
}


/**
 * Diem danh nguoi da dang ky su kien.
 */
public function diemDanh()
{
    global $pdo;

    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    // Chi chap nhan yeu cau POST.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    // Kiem tra CSRF token.
    kiemTraCsrfToken();

    $dangKyId = filter_input(
        INPUT_POST,
        'dang_ky_id',
        FILTER_VALIDATE_INT
    );

    $suKienId = filter_input(
        INPUT_POST,
        'su_kien_id',
        FILTER_VALIDATE_INT
    );

    if (!$dangKyId || $dangKyId < 1 || !$suKienId || $suKienId < 1) {
        http_response_code(400);
        exit('Thong tin diem danh khong hop le.');
    }

    // Kiem tra nguoi dung co quyen quan ly su kien hay khong.
    $suKien = $this->laySuKienDuocPhep((int) $suKienId);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    // Chi diem danh nguoi da dang ky va thuoc dung su kien.
    $sql = "
        UPDATE dang_ky
        SET trang_thai = 'da_tham_gia'
        WHERE id = :dang_ky_id
          AND su_kien_id = :su_kien_id
          AND trang_thai = 'da_dang_ky'
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'dang_ky_id' => (int) $dangKyId,
        'su_kien_id' => (int) $suKienId
    ]);

    // Quay lai danh sach nguoi dang ky cua su kien.
    header(
        'Location: /Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky&id='
        . (int) $suKienId
    );
    exit;
}


/**
 * Hien thi chi tiet su kien cho Ban to chuc va Admin.
 */
public function chiTiet()
{
    global $pdo;

    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('Ma su kien khong hop le.');
    }

    // Chi xem su kien duoc phep quan ly.
    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen xem.');
    }

    // Lay ten cau lac bo.
    $stmt = $pdo->prepare("
        SELECT ten_cau_lac_bo
        FROM cau_lac_bo
        WHERE id = ?
    ");
    $stmt->execute([$suKien['cau_lac_bo_id']]);
    $suKien['ten_cau_lac_bo'] = $stmt->fetchColumn() ?: 'Chua cap nhat';

    // Thong ke nguoi dang ky va nguoi da tham gia.
    $stmt = $pdo->prepare("
    SELECT
        COUNT(CASE
            WHEN trang_thai IN ('da_dang_ky', 'da_tham_gia')
            THEN 1
        END) AS so_nguoi_dang_ky,

        COUNT(CASE
            WHEN trang_thai = 'da_tham_gia'
            THEN 1
        END) AS so_nguoi_da_tham_gia,

        COUNT(CASE
            WHEN trang_thai = 'da_huy'
            THEN 1
        END) AS so_nguoi_huy

    FROM dang_ky
    WHERE su_kien_id = ?
");
$stmt->execute([(int) $id]);
$thongKe = $stmt->fetch(PDO::FETCH_ASSOC);

$suKien['so_nguoi_dang_ky'] =
    (int) ($thongKe['so_nguoi_dang_ky'] ?? 0);

$suKien['so_nguoi_da_tham_gia'] =
    (int) ($thongKe['so_nguoi_da_tham_gia'] ?? 0);

$suKien['so_nguoi_huy'] =
    (int) ($thongKe['so_nguoi_huy'] ?? 0);

$suKien['so_cho_con_lai'] = max(
    0,
    (int) $suKien['so_luong_toi_da']
    - $suKien['so_nguoi_dang_ky']
);

    require __DIR__ . '/../Views/ban_to_chuc/chi_tiet_su_kien.php';
}

}