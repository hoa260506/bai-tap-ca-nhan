
<?php

require_once __DIR__ . '/../Core/xac_thuc.php';

// Thu hồi token ghi nhớ trước khi xóa session.
thuHoiGhiNho();

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $thamSo = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $thamSo['path'],
        'domain' => $thamSo['domain'],
        'secure' => $thamSo['secure'],
        'httponly' => $thamSo['httponly'],
        'samesite' => 'Lax',
    ]);
}

session_destroy();

header('Location: /Nhom-4-LTW/public/dang_nhap.php');
exit;
