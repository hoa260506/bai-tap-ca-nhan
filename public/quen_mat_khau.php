
<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

require_once __DIR__ . '/../app/Core/xac_thuc.php';

$thong_bao = '';
$email = '';
$ma_xac_minh_demo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $thong_bao = 'Vui lòng nhập địa chỉ email hợp lệ.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id, email, vai_tro
             FROM nguoi_dung
             WHERE email = :email
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $nguoi_dung = $stmt->fetch();

        if ($nguoi_dung &&
            in_array($nguoi_dung['vai_tro'], ['thanh_vien', 'ban_to_chuc'], true)) {

            $ma = (string) random_int(100000, 999999);

            $_SESSION['dat_lai_mat_khau'] = [
                'nguoi_dung_id' => (int) $nguoi_dung['id'],
                'email' => $nguoi_dung['email'],
                'ma_hash' => password_hash($ma, PASSWORD_DEFAULT),
                'het_han' => time() + 300,
                'so_lan_thu' => 0,
            ];

            $ma_xac_minh_demo = $ma;
            $thong_bao = 'Đã tạo mã demo. Mã có hiệu lực trong 5 phút.';
        } else {
            $thong_bao = 'Không tìm thấy tài khoản Thành viên hoặc Ban tổ chức phù hợp.';
        }
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu | Sự kiện sinh viên</title>
    <link rel="stylesheet"
          href="/Nhom-4-LTW/public/assets/css/dang_nhap.css">
</head>
<body>
<main class="page">
    <section class="form-panel">
        <div class="login-box">
            <h1>Quên mật khẩu</h1>

            <p>Nhập email tài khoản Thành viên hoặc Ban tổ chức.</p>

            <?php if ($thong_bao !== ''): ?>
                <p role="status"><?= e($thong_bao) ?></p>
            <?php endif; ?>

            <?php if ($ma_xac_minh_demo !== ''): ?>
                <div class="message success">
                    <strong>Mã xác minh dùng thử:</strong>
                    <span><?= e($ma_xac_minh_demo) ?></span>
                    <p>Chỉ dùng để thử nghiệm trên localhost.</p>
                </div>

                <a href="/Nhom-4-LTW/public/xac_minh_mat_khau.php">
                    Nhập mã xác minh
                </a>
            <?php endif; ?>

            <form method="POST"
                  action="/Nhom-4-LTW/public/quen_mat_khau.php">
                <div class="field">
                    <label for="email">Email đăng nhập</label>
                    <div class="input-wrap">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Nhập email của bạn"
                            value="<?= e($email) ?>"
                            autocomplete="email"
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Tạo mã demo
                </button>
            </form>

            <p class="register">
                <a href="/Nhom-4-LTW/public/dang_nhap.php">
                    Quay lại đăng nhập
                </a>
            </p>
        </div>
    </section>
</main>
</body>
</html>
