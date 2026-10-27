
<?php
if (!isset($suKienList)) {
    $suKienList = [];
}

function eDanhSach($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}


function boDauTiengViet($text): string
{
    $text = mb_strtolower((string) $text, 'UTF-8');

    $coDau = [
        'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
        'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
        'ì','í','ị','ỉ','ĩ',
        'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
        'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
        'ỳ','ý','ỵ','ỷ','ỹ','đ'
    ];

    $khongDau = [
        'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
        'e','e','e','e','e','e','e','e','e','e','e',
        'i','i','i','i','i',
        'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
        'u','u','u','u','u','u','u','u','u','u','u',
        'y','y','y','y','y','d'
    ];

    return str_replace($coDau, $khongDau, $text);
}


$nhanTrangThai = [
    'nhap' => 'Bản nháp',
    'cong_khai' => 'Đang mở đăng ký',
    'da_huy' => 'Đã hủy',
    'ket_thuc' => 'Đã kết thúc'
];

$tuKhoa = trim($_GET['tu_khoa'] ?? '');
$locTrangThai = $_GET['loc_trang_thai'] ?? '';
$tuNgay = $_GET['tu_ngay'] ?? '';
$denNgay = $_GET['den_ngay'] ?? '';

$trangThaiHopLe = ['', 'nhap', 'cong_khai', 'da_huy', 'ket_thuc'];
if (!in_array($locTrangThai, $trangThaiHopLe, true)) {
    $locTrangThai = '';
}

$loiKhoangNgay = '';
$loiKhoangNgay = $loiKhoangNgay ?? '';

$kiemTraNgay = function ($ngay): bool {
    if ($ngay === '') {
        return true;
    }

    $date = DateTime::createFromFormat('!Y-m-d', $ngay);

    return $date !== false
        && $date->format('Y-m-d') === $ngay;
};

if (!$kiemTraNgay($tuNgay)) {
    $tuNgay = '';
}

if (!$kiemTraNgay($denNgay)) {
    $denNgay = '';
}

if (
    $tuNgay !== '' &&
    $denNgay !== '' &&
    $tuNgay > $denNgay
) {
    $loiKhoangNgay = 'Ngày bắt đầu lọc không được lớn hơn ngày kết thúc.';
}

$suKienList = array_values(array_filter(
    $suKienList,
    function ($suKien) use (
        $tuKhoa,
        $locTrangThai,
        $tuNgay,
        $denNgay,
        $loiKhoangNgay
    ) {
        $ten = (string) ($suKien['ten_su_kien'] ?? '');
        $trangThai = (string) ($suKien['trang_thai'] ?? '');
        $thoiGian = (string) ($suKien['thoi_gian_bat_dau'] ?? '');
        $ngayBatDau = substr($thoiGian, 0, 10);

        if (
            $tuKhoa !== '' &&
            strpos(
                boDauTiengViet($ten),
                boDauTiengViet($tuKhoa)
            ) === false
        ) {
            return false;
        }

        if (
            $locTrangThai !== '' &&
            $trangThai !== $locTrangThai
        ) {
            return false;
        }

        // Nếu khoảng ngày không hợp lệ, bỏ qua lọc ngày
        // để vẫn hiển thị danh sách sự kiện.
        if ($loiKhoangNgay !== '') {
            return true;
        }

        if ($tuNgay !== '' && $ngayBatDau < $tuNgay) {
            return false;
        }

        if ($denNgay !== '' && $ngayBatDau > $denNgay) {
            return false;
        }

        return true;
    }
));

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sự kiện</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
            background: #f4f6f9;
            color: #333;
        }

        h1 {
            color: #243b64;
            margin-top: 0;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }

        .btn {
            display: inline-block;
            padding: 9px 12px;
            background: #2457a7;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            white-space: nowrap;
        }

        .btn:hover {
            opacity: 0.88;
        }

        .delete-btn {
            background: #bd3030;
        }

        .view-btn {
            background: #16805d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #e9eef7;
            color: #243b64;
        }

        tbody tr:hover {
            background: #f8faff;
        }

        .empty {
            text-align: center;
            color: #666;
            padding: 24px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .actions form {
            margin: 0;
        }

        .back-link {
            display: inline-block;
            margin-top: 22px;
            color: #2457a7;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 700px) {
            body {
                padding: 12px;
            }

            .container {
                padding: 15px;
            }

            th, td {
                padding: 8px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <h1>Quản lý sự kiện</h1>

    <p>Danh sách các sự kiện do Ban tổ chức quản lý.</p>

    <a
        class="btn"
        href="/Nhom-4-LTW/public/index.php?trang=them_su_kien"
    >
        + Thêm sự kiện
    </a>
    
<form method="GET" action="/Nhom-4-LTW/public/index.php"
      style="margin-top:20px;padding:16px;background:#f4f6fb;border-radius:8px;">

    <input type="hidden" name="trang" value="danh_sach">

    <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:end;">

        <label>
            Tên sự kiện<br>
            <input type="text" name="tu_khoa"
                   value="<?= eDanhSach($tuKhoa) ?>"
                   placeholder="Nhập tên sự kiện..."
                   style="padding:9px;margin-top:5px;">
        </label>

        <label>
            Trạng thái<br>
            <select name="loc_trang_thai"
                    style="padding:9px;margin-top:5px;">
                <option value="">Tất cả trạng thái</option>
                <?php foreach ($nhanTrangThai as $ma => $nhan): ?>
                    <option value="<?= eDanhSach($ma) ?>"
                        <?= $locTrangThai === $ma ? 'selected' : '' ?>>
                        <?= eDanhSach($nhan) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Từ ngày bắt đầu<br>
            <input type="date" name="tu_ngay"
                   value="<?= eDanhSach($tuNgay) ?>"
                   style="padding:8px;margin-top:5px;">
        </label>

        <label>
            Đến ngày bắt đầu<br>
            <input type="date" name="den_ngay"
                   value="<?= eDanhSach($denNgay) ?>"
                   style="padding:8px;margin-top:5px;">
        </label>

        <button type="submit" class="btn">Tìm kiếm</button>

        <a class="btn"
           style="background:#64748b;"
           href="/Nhom-4-LTW/public/index.php?trang=danh_sach">
            Xóa bộ lọc
        </a>
    </div>
</form>

<?php if ($loiKhoangNgay !== ''): ?>
    <p style="color:#c62828;" role="alert">
        <?= eDanhSach($loiKhoangNgay) ?>
    </p>
<?php endif; ?>

<p style="color:#64748b;">
    Tìm thấy <strong><?= count($suKienList) ?></strong> sự kiện.
</p>


    <table>
        <thead>
        <tr>
            <th>STT</th>
            <th>Tên sự kiện</th>
            <th>Địa điểm</th>
            <th>Thời gian bắt đầu</th>
            <th>Số lượng tối đa</th>
            <th>Đã đăng ký</th>
            <th>Đã tham gia</th>
            <th>Chỗ còn lại</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
        </thead>

        <tbody>
        <?php if (empty($suKienList)): ?>
            <tr>
                <td colspan="10" class="empty">
                    Chưa có sự kiện nào.
                </td>
            </tr>
        <?php else: ?>

            <?php foreach ($suKienList as $index => $suKien): ?>
                <?php
                $trangThai = $suKien['trang_thai'] ?? '';
                $daKetThuc = in_array(
                    $trangThai,
                    ['da_huy', 'ket_thuc'],
                    true
                );
                ?>

                <tr>
                    <td><?= $index + 1 ?></td>

                    <td>
                        <?= eDanhSach($suKien['ten_su_kien'] ?? '') ?>
                    </td>

                    <td>
                        <?= eDanhSach($suKien['dia_diem'] ?? '') ?>
                    </td>

                    <td>
                        <?= eDanhSach($suKien['thoi_gian_bat_dau'] ?? '') ?>
                    </td>

                    <td>
                        <?= (int) ($suKien['so_luong_toi_da'] ?? 0) ?>
                    </td>

                    <td>
                        <?= (int) ($suKien['so_nguoi_dang_ky'] ?? 0) ?>
                    </td>

                    <td>
                        <?= (int) ($suKien['so_nguoi_da_tham_gia'] ?? 0) ?>
                    </td>

                    <td>
                        <?= (int) ($suKien['so_cho_con_lai'] ?? 0) ?>
                    </td>

                    <td>
                        <?= eDanhSach(
                            $nhanTrangThai[$trangThai] ?? $trangThai
                        ) ?>
                    </td>

                    <td>
                        <div class="actions">

                            <a
                                class="btn view-btn"
                                href="/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky&id=<?= (int) $suKien['id'] ?>"
                            >
                                Xem người đăng ký
                            </a>
                            
<a
    class="btn"
    style="background: #7c3aed;"
    href="/Nhom-4-LTW/public/index.php?trang=chi_tiet_su_kien&id=<?= (int) $suKien['id'] ?>"
>
    Chi tiết
</a>


                            <?php if (!$daKetThuc): ?>
                                <a
                                    class="btn"
                                    href="/Nhom-4-LTW/public/index.php?trang=sua_su_kien&id=<?= (int) $suKien['id'] ?>"
                                >
                                    Sửa
                                </a>

                                
<form
    action="/Nhom-4-LTW/public/index.php?trang=huy_su_kien"
    method="POST"
    onsubmit="return confirm('Bạn có chắc chắn muốn hủy sự kiện này?');"
>
    <input
        type="hidden"
        name="csrf_token"
        value="<?= eDanhSach(taoCsrfToken()) ?>"
    >

    <input
        type="hidden"
        name="id"
        value="<?= (int) $suKien['id'] ?>"
    >


                                    <button
                                        type="submit"
                                        class="btn"
                                        style="background: #d97706;"
                                    >
                                        Hủy
                                    </button>
                                </form>
                            <?php endif; ?>

                            
<form
    action="/Nhom-4-LTW/public/index.php?trang=xoa_su_kien"
    method="POST"
    onsubmit="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?');"
>
    <input
        type="hidden"
        name="csrf_token"
        value="<?= eDanhSach(taoCsrfToken()) ?>"
    >

    <input
        type="hidden"
        name="id"
        value="<?= (int) $suKien['id'] ?>"
    >


                                <button
                                    type="submit"
                                    class="btn delete-btn"
                                >
                                    Xóa
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>

        <?php endif; ?>
        </tbody>
    </table>

    <a
        class="back-link"
        href="/Nhom-4-LTW/public/index.php?trang=ban_to_chuc"
    >
        ← Quay lại trang ban tổ chức
    </a>

</div>
</body>
</html>
