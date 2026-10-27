<?php
$thoiGianBatDau = !empty($suKien['thoi_gian_bat_dau'])
    ? date('Y-m-d\TH:i', strtotime($suKien['thoi_gian_bat_dau']))
    : '';

$thoiGianKetThuc = !empty($suKien['thoi_gian_ket_thuc'])
    ? date('Y-m-d\TH:i', strtotime($suKien['thoi_gian_ket_thuc']))
    : '';
$hanDangKy = !empty($suKien['han_dang_ky'])
    ? date('Y-m-d\TH:i', strtotime($suKien['han_dang_ky']))
    : '';

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sự kiện</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 30px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea, select {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            margin-top: 6px;
        }

        button, .back {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 16px;
            border: none;
            border-radius: 5px;
            background: #2457a7;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Sửa sự kiện</h1>

    <form
        action="/Nhom-4-LTW/public/index.php?trang=luu_sua_su_kien"
        method="POST" >
        <input type="hidden" name="csrf_token"
       value="<?= e(taoCsrfToken()) ?>">
    
        <input type="hidden" name="id" value="<?= (int) $suKien['id'] ?>">

        <label for="ten_su_kien">Tên sự kiện *</label>
        <input
            id="ten_su_kien"
            type="text"
            name="ten_su_kien"
            maxlength="200"
            required
            value="<?= e($suKien['ten_su_kien'] ?? '') ?>"
        >

        <label for="mo_ta">Mô tả</label>
        <textarea id="mo_ta" name="mo_ta" rows="4"><?= e($suKien['mo_ta'] ?? '') ?></textarea>

        <label for="dia_diem">Địa điểm</label>
        <input
            id="dia_diem"
            type="text"
            name="dia_diem"
            maxlength="255"
            value="<?= e($suKien['dia_diem'] ?? '') ?>"
        >

        <label for="thoi_gian_bat_dau">Thời gian bắt đầu *</label>
        <input
            id="thoi_gian_bat_dau"
            type="datetime-local"
            name="thoi_gian_bat_dau"
            required
            value="<?= e($thoiGianBatDau) ?>"
        >

        <label for="thoi_gian_ket_thuc">Thời gian kết thúc</label>
        <input
            id="thoi_gian_ket_thuc"
            type="datetime-local"
            name="thoi_gian_ket_thuc"
            value="<?= e($thoiGianKetThuc) ?>"
        >
        
        <label for="han_dang_ky">Hạn đăng ký</label>
        <input
            id="han_dang_ky"
            type="datetime-local"
            name="han_dang_ky"
            value="<?= e($hanDangKy) ?>"
        >


        <label for="so_luong_toi_da">Số lượng tối đa *</label>
        <input
            id="so_luong_toi_da"
            type="number"
            name="so_luong_toi_da"
            min="1"
            required
            value="<?= (int) ($suKien['so_luong_toi_da'] ?? 100) ?>"
        >

        
        <label for="trang_thai">Trạng thái *</label>
        <select id="trang_thai" name="trang_thai" required>
            <option value="nhap"
                <?= ($suKien['trang_thai'] ?? '') === 'nhap' ? 'selected' : '' ?>>
                Bản nháp
            </option>
            <option value="cong_khai"
                <?= ($suKien['trang_thai'] ?? '') === 'cong_khai' ? 'selected' : '' ?>>
                Công khai
            </option>
        </select>


        <button type="submit">Lưu thay đổi</button>
        <a class="back" href="/Nhom-4-LTW/public/index.php?trang=danh_sach">
            Quay lại
        </a>
    </form>
</div>
</body>
</html>