<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once __DIR__ . '/../app/Core/xac_thuc.php';
yeuCauDangNhap();

require_once __DIR__ . '/../app/Controllers/SuKienController.php';

$trang = $_GET['trang'] ?? 'danh_sach';
$controller = new SuKienController();

if ($trang === 'dang_xuat') {
    require_once __DIR__ . '/../app/Auth/dang_xuat.php';
    exit;
}

if ($trang === 'admin') {
    yeuCauVaiTro(['admin']);
    require __DIR__ . '/../app/Views/admin/trang_chu.php';
    exit;
}

if ($trang === 'ban_to_chuc') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);
    require __DIR__ . '/../app/Views/ban_to_chuc/trang_chu.php';
    exit;
}

if ($trang === 'thong_bao') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);
    require __DIR__ . '/../app/Views/ban_to_chuc/thong_bao.php';
    exit;
}

if ($trang === 'thanh_vien') {
    yeuCauVaiTro(['thanh_vien']);
    require __DIR__ . '/../app/Views/thanh_vien/trang_chu.php';
    exit;
}

if ($trang === 'them_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);
    require __DIR__ . '/../app/Views/ban_to_chuc/them_su_kien.php';
    exit;
}

if ($trang === 'luu_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    $controller->them();
    exit;
}

if ($trang === 'sua_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);
    $controller->sua();
    exit;
}

if ($trang === 'chi_tiet_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);
    $controller->chiTiet();
    exit;
}

if ($trang === 'danh_sach_dang_ky') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);
    $controller->danhSachDangKy();
    exit;
}

if ($trang === 'diem_danh') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    $controller->diemDanh();
    exit;
}


if ($trang === 'luu_sua_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    $controller->capNhat();
    exit;
}

if ($trang === 'xoa_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    $controller->xoa();
    exit;
}

if ($trang === 'huy_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    $controller->huy();
    exit;
}


$controller->danhSach();
