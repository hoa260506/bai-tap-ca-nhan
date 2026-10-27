
<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_start();
}

require_once __DIR__ . '/ket_noi_csdl.php';

const COOKIE_GHI_NHO = 'ghi_nho_dang_nhap';
const THOI_HAN_GHI_NHO = 2592000; // 30 ngày

function datCookieGhiNho(string $giaTri, int $hetHan): void
{
    setcookie(COOKIE_GHI_NHO, $giaTri, [
        'expires' => $hetHan,
        'path' => '/Nhom-4-LTW/public/',
        'secure' => !empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function thuHoiGhiNho(): void
{
    global $pdo;

    $cookie = $_COOKIE[COOKIE_GHI_NHO] ?? '';

    if (preg_match('/^([a-f0-9]{32}):([a-f0-9]{64})$/', $cookie, $m)) {
        $stmt = $pdo->prepare(
            'DELETE FROM ghi_nho_dang_nhap WHERE selector = :selector'
        );
        $stmt->execute(['selector' => $m[1]]);
    }

    datCookieGhiNho('', time() - 3600);
    unset($_COOKIE[COOKIE_GHI_NHO]);
}

function taoGhiNhoDangNhap(int $nguoiDungId): void
{
    global $pdo;

    thuHoiGhiNho();

    $selector = bin2hex(random_bytes(16));
    $token = bin2hex(random_bytes(32));
    $hetHan = time() + THOI_HAN_GHI_NHO;

    $stmt = $pdo->prepare(
        'INSERT INTO ghi_nho_dang_nhap
            (nguoi_dung_id, selector, token_hash, het_han)
         VALUES (:nguoi_dung_id, :selector, :token_hash, :het_han)'
    );

    $stmt->execute([
        'nguoi_dung_id' => $nguoiDungId,
        'selector' => $selector,
        'token_hash' => hash('sha256', $token),
        'het_han' => date('Y-m-d H:i:s', $hetHan),
    ]);

    datCookieGhiNho($selector . ':' . $token, $hetHan);
}

function nguoiDungHienTai(): ?array
{
    global $pdo;

    if (isset($_SESSION['nguoi_dung'])) {
        return $_SESSION['nguoi_dung'];
    }

    $cookie = $_COOKIE[COOKIE_GHI_NHO] ?? '';

    if (!preg_match('/^([a-f0-9]{32}):([a-f0-9]{64})$/', $cookie, $m)) {
        return null;
    }

    $stmt = $pdo->prepare(
        'SELECT g.nguoi_dung_id, g.token_hash, g.het_han,
                n.id, n.ho_ten, n.email, n.vai_tro
         FROM ghi_nho_dang_nhap g
         JOIN nguoi_dung n ON n.id = g.nguoi_dung_id
         WHERE g.selector = :selector
         LIMIT 1'
    );

    $stmt->execute(['selector' => $m[1]]);
    $dong = $stmt->fetch();

    if (
        !$dong ||
        strtotime($dong['het_han']) <= time() ||
        !hash_equals($dong['token_hash'], hash('sha256', $m[2]))
    ) {
        thuHoiGhiNho();
        return null;
    }

    session_regenerate_id(true);

    $_SESSION['nguoi_dung'] = [
        'id' => $dong['id'],
        'ho_ten' => $dong['ho_ten'],
        'email' => $dong['email'],
        'vai_tro' => $dong['vai_tro'],
    ];

    // Xoay token sau khi khôi phục phiên đăng nhập.
    $tokenMoi = bin2hex(random_bytes(32));
    $hetHanMoi = time() + THOI_HAN_GHI_NHO;

    $capNhat = $pdo->prepare(
        'UPDATE ghi_nho_dang_nhap
         SET token_hash = :token_hash, het_han = :het_han
         WHERE selector = :selector'
    );

    $capNhat->execute([
        'token_hash' => hash('sha256', $tokenMoi),
        'het_han' => date('Y-m-d H:i:s', $hetHanMoi),
        'selector' => $m[1],
    ]);

    datCookieGhiNho($m[1] . ':' . $tokenMoi, $hetHanMoi);

    return $_SESSION['nguoi_dung'];
}

function yeuCauDangNhap(): void
{
    if (nguoiDungHienTai() === null) {
        header('Location: /Nhom-4-LTW/public/dang_nhap.php');
        exit;
    }
}

function yeuCauVaiTro(array $vaiTroChoPhep): void
{
    yeuCauDangNhap();

    $nguoiDung = nguoiDungHienTai();

    if (
        $nguoiDung === null ||
        !in_array($nguoiDung['vai_tro'] ?? '', $vaiTroChoPhep, true)
    ) {
        http_response_code(403);
        exit('Ban khong co quyen truy cap chuc nang nay.');
    }
}

/**
 * Tạo hoặc lấy CSRF token của phiên đăng nhập hiện tại.
 */
function taoCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Kiểm tra CSRF token gửi từ biểu mẫu POST.
 */
function kiemTraCsrfToken(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong duoc phep.');
    }

    $tokenGuiLen = $_POST['csrf_token'] ?? '';
    $tokenTrongSession = $_SESSION['csrf_token'] ?? '';

    if (
        !is_string($tokenGuiLen) ||
        !is_string($tokenTrongSession) ||
        $tokenTrongSession === '' ||
        !hash_equals($tokenTrongSession, $tokenGuiLen)
    ) {
        http_response_code(403);
        exit('Yeu cau khong hop le. Vui long tai lai trang va thu lai.');
    }
}
