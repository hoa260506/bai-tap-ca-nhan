<?php

require_once __DIR__ . '/../Core/ket_noi_csdl.php';
require_once __DIR__ . '/../Core/xac_thuc.php';
require_once __DIR__ . '/../Repositories/SuKienRepository.php';

class SuKienController
{
    private SuKienRepository $suKienRepository;

    public function __construct()
    {
        global $pdo;

        $this->suKienRepository = new SuKienRepository($pdo);
    }

    /**
     * Lay danh sach su kien theo quyen nguoi dung.
     */
    public function danhSach()
{
    yeuCauDangNhap();

    $nguoiDung = nguoiDungHienTai();

    $suKienList = $this->suKienRepository
        ->danhSachTheoVaiTro($nguoiDung);

    // Lay ten cau lac bo cua nguoi dang dang nhap
    $tenCauLacBo = 'Câu lạc bộ';

    if (($nguoiDung['vai_tro'] ?? '') === 'ban_to_chuc') {
        $idNguoiDung = (int) $nguoiDung['id'];

        $cauLacBoId = $this->suKienRepository
            ->layCauLacBoTheoNguoiQuanLy($idNguoiDung);

        if ($cauLacBoId !== null) {
            $tenCauLacBo = $this->suKienRepository
                ->tenCauLacBo((int) $cauLacBoId);
        }
    }

    require __DIR__ . '/../Views/ban_to_chuc/danh_sach_su_kien.php';
}
    /**
     * Chuyen chuoi ngay gio tu input datetime-local thanh DateTime.
     */
    private function docNgayGio(string $giaTri): ?DateTime
    {
        $ngayGio = DateTime::createFromFormat(
            '!Y-m-d\TH:i',
            $giaTri
        );

        $loi = DateTime::getLastErrors();

        if (
            $ngayGio === false ||
            (
                $loi !== false &&
                (
                    $loi['warning_count'] > 0 ||
                    $loi['error_count'] > 0
                )
            )
        ) {
            return null;
        }

        return $ngayGio;
    }

    /**
     * Lay su kien neu nguoi dung co quyen quan ly.
     */
    private function laySuKienDuocPhep(int $id): ?array
    {
        $nguoiDung = nguoiDungHienTai();

        return $this->suKienRepository
            ->laySuKienDuocPhep($id, $nguoiDung);
    }

/**
 * Them su kien moi.
 */
public function them()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    kiemTraCsrfToken();

    $nguoiDung = nguoiDungHienTai();

    if (!$nguoiDung) {
        yeuCauDangNhap();
        return;
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

    // Kiem tra thoi gian bat dau.
    $batDauNgay = $this->docNgayGio($batDau);

    if ($batDauNgay === null) {
        http_response_code(400);
        exit('Thoi gian bat dau khong hop le.');
    }

    // Kiem tra thoi gian ket thuc.
    $ketThucNgay = null;

    if ($ketThuc !== '') {
        $ketThucNgay = $this->docNgayGio($ketThuc);

        if (
            $ketThucNgay === null ||
            $ketThucNgay <= $batDauNgay
        ) {
            http_response_code(400);
            exit(
                'Thoi gian ket thuc phai sau thoi gian bat dau.'
            );
        }
    }

    // Kiem tra han dang ky.
    $hanDangKyNgay = null;

    if ($hanDangKy !== '') {
        $hanDangKyNgay = $this->docNgayGio($hanDangKy);

        if (
            $hanDangKyNgay === null ||
            $hanDangKyNgay > $batDauNgay
        ) {
            http_response_code(400);
            exit(
                'Han dang ky phai truoc hoac bang thoi gian bat dau.'
            );
        }
    }

    // Xac dinh cau lac bo.
    if ($nguoiDung['vai_tro'] === 'admin') {
        $cauLacBoId = filter_var(
            $_POST['cau_lac_bo_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $cauLacBoId === false ||
            $cauLacBoId === null ||
            $cauLacBoId < 1
        ) {
            http_response_code(400);
            exit('Vui long chon cau lac bo.');
        }

        if (
            !$this->suKienRepository
                ->cauLacBoTonTai((int) $cauLacBoId)
        ) {
            http_response_code(400);
            exit('Cau lac bo khong ton tai.');
        }
    } else {
        $cauLacBoId = $this->suKienRepository
            ->layCauLacBoTheoNguoiQuanLy($nguoiTaoId);

        if ($cauLacBoId === null) {
            http_response_code(403);
            exit('Ban chua duoc gan quan ly cau lac bo.');
        }
    }

    // Chuan bi du lieu gui sang Repository.
    $duLieu = [
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
        'so_luong' => (int) $soLuong,
        'trang_thai' => $trangThai,
        'nguoi_tao_id' => $nguoiTaoId
    ];

    $this->suKienRepository->them($duLieu);

    header(
        'Location: /Nhom-4-LTW/public/index.php?trang=danh_sach'
    );
    exit;
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
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    if (
        in_array(
            $suKien['trang_thai'],
            ['da_huy', 'ket_thuc'],
            true
        )
    ) {
        http_response_code(403);
        exit('Khong the sua su kien da huy hoac da ket thuc.');
    }

    $tenCauLacBo = $this->suKienRepository
        ->tenCauLacBo((int) $suKien['cau_lac_bo_id']);

    require __DIR__ . '/../Views/ban_to_chuc/sua_su_kien.php';
}

    public function capNhat()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    kiemTraCsrfToken();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    if (
        in_array(
            $suKien['trang_thai'],
            ['da_huy', 'ket_thuc'],
            true
        )
    ) {
        http_response_code(403);
        exit('Khong the cap nhat su kien da huy hoac da ket thuc.');
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

    $trangThai = $_POST['trang_thai'] ?? '';

    if (
        $tenSuKien === '' ||
        $soLuong === false ||
        $soLuong === null ||
        $soLuong < 1 ||
        !in_array($trangThai, ['nhap', 'cong_khai'], true)
    ) {
        http_response_code(400);
        exit('Du lieu cap nhat khong hop le.');
    }

    $batDauNgay = $this->docNgayGio($batDau);

    if ($batDauNgay === null) {
        http_response_code(400);
        exit('Thoi gian bat dau khong hop le.');
    }

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

    $hanDangKyNgay = null;

    if ($hanDangKy !== '') {
        $hanDangKyNgay = $this->docNgayGio($hanDangKy);

        if (
            $hanDangKyNgay === null ||
            $hanDangKyNgay > $batDauNgay
        ) {
            http_response_code(400);
            exit(
                'Han dang ky phai truoc hoac bang thoi gian bat dau.'
            );
        }
    }

    $duLieu = [
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
        'so_luong' => (int) $soLuong,
        'trang_thai' => $trangThai
    ];

    $this->suKienRepository->capNhat((int) $id, $duLieu);

    header(
        'Location: /Nhom-4-LTW/public/index.php?trang=danh_sach'
    );
    exit;
}

    /**
     * Xoa su kien.
     */

public function xoa()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    kiemTraCsrfToken();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    if ($this->suKienRepository->demDangKy((int) $id) > 0) {
        http_response_code(409);
        exit(
            'Su kien da co nguoi dang ky. Vui long huy su kien thay vi xoa.'
        );
    }

    $this->suKienRepository->xoa((int) $id);

    header(
        'Location: /Nhom-4-LTW/public/index.php?trang=danh_sach'
    );
    exit;
}

    /**
     * Huy su kien.
     */

/**
 * Huy su kien.
 */
public function huy()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    kiemTraCsrfToken();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    if (
        in_array(
            $suKien['trang_thai'],
            ['da_huy', 'ket_thuc'],
            true
        )
    ) {
        header(
            'Location: /Nhom-4-LTW/public/index.php?trang=danh_sach'
        );
        exit;
    }

    $this->suKienRepository->huy((int) $id);

    header(
        'Location: /Nhom-4-LTW/public/index.php?trang=danh_sach'
    );
    exit;
}


/**
 * Hien thi danh sach nguoi dang ky cua mot su kien.
 */
public function danhSachDangKy()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    $dangKyList = $this->suKienRepository
        ->danhSachDangKy((int) $id);

    $thongKe = $this->suKienRepository
        ->thongKeDangKy((int) $id);

    require __DIR__ . '/../Views/ban_to_chuc/danh_sach_dang_ky.php';
}

/**
 * Diem danh nguoi da dang ky su kien.
 */
public function diemDanh()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

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

    if (
        !$dangKyId || $dangKyId < 1 ||
        !$suKienId || $suKienId < 1
    ) {
        http_response_code(400);
        exit('ID dang ky hoac su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $suKienId);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    $thanhCong = $this->suKienRepository->diemDanh(
        (int) $dangKyId,
        (int) $suKienId
    );

    if (!$thanhCong) {
        http_response_code(404);
        exit('Khong tim thay dang ky hop le de diem danh.');
    }

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
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    $tenCauLacBo = $this->suKienRepository
        ->tenCauLacBo((int) $suKien['cau_lac_bo_id']);

    $thongKe = $this->suKienRepository
        ->thongKeDangKy((int) $id);

    require __DIR__ . '/../Views/ban_to_chuc/chi_tiet_su_kien.php';
}

}