
<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

require_once __DIR__ . '/../app/Core/xac_thuc.php';

$thong_bao = '';
$thanh_cong = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma_nhap = trim($_POST['ma_xac_minh'] ?? '');
    $mat_khau_moi = $_POST['mat_khau_moi'] ?? '';
    $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'] ?? '';

    $du_lieu = $_SESSION['dat_lai_mat_khau'] ?? null;

    if (
        !is_array($du_lieu) ||
        !isset(
            $du_lieu['nguoi_dung_id'],
            $du_lieu['ma_hash'],
            $du_lieu['het_han'],
            $du_lieu['so_lan_thu']
        )
    ) {
        $thong_bao = 'Bạn chưa tạo mã xác minh hoặc phiên đã hết. Vui lòng thực hiện lại.';
    } elseif (time() > (int) $du_lieu['het_han']) {
        unset($_SESSION['dat_lai_mat_khau']);
        $thong_bao = 'Mã xác minh đã hết hạn. Vui lòng tạo mã mới.';
    } elseif ((int) $du_lieu['so_lan_thu'] >= 5) {
        unset($_SESSION['dat_lai_mat_khau']);
        $thong_bao = 'Bạn đã nhập sai quá số lần cho phép. Vui lòng tạo mã mới.';
    } elseif (
        $ma_nhap === '' ||
        !password_verify($ma_nhap, $du_lieu['ma_hash'])
    ) {
        $_SESSION['dat_lai_mat_khau']['so_lan_thu'] =
            (int) $du_lieu['so_lan_thu'] + 1;

        $thong_bao = 'Mã xác minh không đúng.';
    } elseif (strlen($mat_khau_moi) < 8) {
        $thong_bao = 'Mật khẩu mới phải có ít nhất 8 ký tự.';
    } elseif ($mat_khau_moi !== $xac_nhan_mat_khau) {
        $thong_bao = 'Mật khẩu xác nhận không khớp.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'UPDATE nguoi_dung
                 SET mat_khau = :mat_khau
                 WHERE id = :id
                   AND vai_tro IN (:thanh_vien, :ban_to_chuc)'
            );

            $stmt->execute([
                'mat_khau' => password_hash(
                    $mat_khau_moi,
                    PASSWORD_DEFAULT
                ),
                'id' => (int) $du_lieu['nguoi_dung_id'],
                'thanh_vien' => 'thanh_vien',
                'ban_to_chuc' => 'ban_to_chuc',
            ]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Không cập nhật được mật khẩu.');
            }

            // Thu hồi các cookie ghi nhớ đăng nhập của tài khoản.
            $xoaGhiNho = $pdo->prepare(
                'DELETE FROM ghi_nho_dang_nhap
                 WHERE nguoi_dung_id = :id'
            );
            $xoaGhiNho->execute([
                'id' => (int) $du_lieu['nguoi_dung_id'],
            ]);

            $pdo->commit();

            unset($_SESSION['dat_lai_mat_khau']);
            $thanh_cong = true;
            $thong_bao = 'Đổi mật khẩu thành công. Bạn có thể đăng nhập bằng mật khẩu mới.';
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log($e->getMessage());
            $thong_bao = 'Chưa thể đổi mật khẩu. Vui lòng thử lại.';
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
    <title>Xác minh và đổi mật khẩu</title>
    <link rel="stylesheet"
          href="/Nhom-4-LTW/public/assets/css/dang_nhap.css">
</head>
<body>
<main class="page">
    <section class="form-panel">
        <div class="login-box">
            <h1>Đặt lại mật khẩu</h1>

            <?php if ($thong_bao !== ''): ?>
                <p role="status"><?= e($thong_bao) ?></p>
            <?php endif; ?>

            <?php if (!$thanh_cong): ?>
                <form method="POST"
                      action="/Nhom-4-LTW/public/xac_minh_mat_khau.php">

                    <div class="field">
                        <label for="ma_xac_minh">Mã xác minh 6 chữ số</label>
                        <input
                            type="text"
                            id="ma_xac_minh"
                            name="ma_xac_minh"
                            inputmode="numeric"
                            pattern="[0-9]{6}"
                            maxlength="6"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="mat_khau_moi">Mật khẩu mới</label>
                        <input
                            type="password"
                            id="mat_khau_moi"
                            name="mat_khau_moi"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="xac_nhan_mat_khau">
                            Nhập lại mật khẩu mới
                        </label>
                        <input
                            type="password"
                            id="xac_nhan_mat_khau"
                            name="xac_nhan_mat_khau"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <button type="submit" class="submit-btn">
                        Xác minh và đổi mật khẩu
                    </button>
                </form>
            <?php else: ?>
                <p class="register">
                    <a href="/Nhom-4-LTW/public/dang_nhap.php">
                        Đến trang đăng nhập
                    </a>
                </p>
            <?php endif; ?>

            <p class="register">
                <a href="/Nhom-4-LTW/public/quen_mat_khau.php">
                    Quay lại trang quên mật khẩu
                </a>
            </p>
        </div>
    </section>
</main>
</body>
</html>
