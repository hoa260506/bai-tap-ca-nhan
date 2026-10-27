<?php
session_start();
require_once __DIR__ . '/../app/Core/ket_noi_csdl.php';

$loi = '';
$ho_ten = '';
$email = '';
$so_dien_thoai = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $so_dien_thoai = trim($_POST['so_dien_thoai'] ?? '');
    $mat_khau = $_POST['mat_khau'] ?? '';
    $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'] ?? '';

    if ($ho_ten === '' || $email === '' || $so_dien_thoai === '' || $mat_khau === '' || $xac_nhan_mat_khau === '') {
        $loi = 'Vui lòng nhập đầy đủ thông tin.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loi = 'Địa chỉ email không hợp lệ.';
    } elseif ($mat_khau !== $xac_nhan_mat_khau) {
        $loi = 'Mật khẩu xác nhận không khớp.';
    } elseif (strlen($mat_khau) < 8) {
        $loi = 'Mật khẩu cần có ít nhất 8 ký tự.';
    } else {
        try {
            // Kiểm tra email đã được sử dụng chưa.
            $kiem_tra = $conn->prepare('SELECT id FROM nguoi_dung WHERE email = :email LIMIT 1');
            $kiem_tra->execute(['email' => $email]);

            if ($kiem_tra->fetch(PDO::FETCH_ASSOC)) {
                $loi = 'Email này đã được đăng ký.';
            } else {
                $mat_khau_ma_hoa = password_hash($mat_khau, PASSWORD_DEFAULT);

                // Tài khoản đăng ký mới mặc định là thành viên.
                $them = $conn->prepare(
                    'INSERT INTO nguoi_dung (ho_ten, email, so_dien_thoai, mat_khau, vai_tro)
                     VALUES (:ho_ten, :email, :so_dien_thoai, :mat_khau, :vai_tro)'
                );
                $them->execute([
                    'ho_ten' => $ho_ten,
                    'email' => $email,
                    'so_dien_thoai' => $so_dien_thoai,
                    'mat_khau' => $mat_khau_ma_hoa,
                    'vai_tro' => 'thanh_vien',
                ]);

                header('Location: dang_nhap.php?dang_ky=thanh_cong');
                exit;
            }
        } catch (PDOException $e) {
            // Lỗi chi tiết được ghi vào log máy chủ, không hiển thị cho người dùng.
            error_log('Lỗi đăng ký tài khoản: ' . $e->getMessage());
            $loi = 'Chưa thể tạo tài khoản. Vui lòng kiểm tra cấu trúc cơ sở dữ liệu rồi thử lại.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản | Sự kiện sinh viên</title>
    <link rel="stylesheet" href="/Nhom-4-LTW/public/assets/css/dang_ky.css">
</head>
<body>
<main class="register-shell">
    <section class="brand-panel" aria-label="Sự kiện sinh viên">
        <div class="brand-mark">
            <span class="brand-mark-icon" aria-hidden="true">✦</span>
            <span>Sự kiện sinh viên</span>
        </div>
        <div class="brand-content">
            <p class="eyebrow">CÙNG KẾT NỐI</p>
            <h1>Khám phá<br>trải nghiệm mới</h1>
            <svg class="illustration" viewBox="0 0 420 250" role="img" aria-label="Minh họa nhóm sinh viên cùng tham gia hoạt động">
                <ellipse cx="210" cy="224" rx="170" ry="13" fill="#c8ddcf" opacity=".55"/>
                <circle cx="80" cy="49" r="28" fill="#d1e3d4"/>
                <path d="M47 55c18-8 29-21 36-38 5 20 2 34-12 46M65 63c-13 2-22 10-28 21M82 58c13 0 23 6 30 16" fill="none" stroke="#8fb59a" stroke-width="3" stroke-linecap="round"/>
                <path d="M300 37c16-15 32-15 44-3-14 0-24 8-29 21M315 59c15-8 29-5 38 7-13-3-24 1-33 12" fill="none" stroke="#9bbda3" stroke-width="3" stroke-linecap="round"/>
                <!-- Sinh viên bên trái -->
                <path d="M78 117c-8 9-11 28-10 48l-5 39h19l10-35 7 35h19l-6-44c-1-22-7-36-18-43z" fill="#176c57"/>
                <circle cx="88" cy="92" r="17" fill="#d89c73"/>
                <path d="M71 91c-1-17 10-26 23-23 10 2 15 11 13 21l-7-8-18 1-7 9z" fill="#344a3e"/>
                <path d="M73 125l-17 35 9 6 22-27M104 126l16 30-8 6-20-27" fill="none" stroke="#d89c73" stroke-width="9" stroke-linecap="round"/>
                <rect x="77" y="128" width="22" height="28" rx="3" fill="#d4e5d6"/>
                <!-- Sinh viên giữa trái -->
                <circle cx="160" cy="79" r="18" fill="#bf805c"/>
                <path d="M142 78c-2-17 9-28 23-26 11 1 17 10 15 24l-8-9-20 2z" fill="#292f2a"/>
                <path d="M142 105c-9 11-12 31-10 51l-3 41h20l10-34 7 34h20l-6-47c-1-23-9-39-21-45z" fill="#e3eee4"/>
                <path d="M145 112l-20 34 8 6 24-26M177 112l20 32-8 7-24-26" fill="none" stroke="#bf805c" stroke-width="9" stroke-linecap="round"/>
                <path d="M148 196l-4 25h13l5-25M174 196l5 25h13l-4-25" fill="#314b42"/>
                <path d="M145 126l15 12 14-12" fill="none" stroke="#8bb49b" stroke-width="2"/>
                <!-- Sinh viên giữa phải -->
                <circle cx="238" cy="91" r="17" fill="#e1ad86"/>
                <path d="M220 90c-1-16 10-25 22-24 12 1 18 10 15 22l-8-8-19 1z" fill="#4c3a2d"/>
                <path d="M219 116c-9 10-12 29-10 47l-3 34h19l10-31 7 31h19l-5-42c-2-21-9-35-21-41z" fill="#2b8067"/>
                <path d="M221 124l-18 30 8 7 22-24M254 124l17 28-8 7-21-24" fill="none" stroke="#e1ad86" stroke-width="9" stroke-linecap="round"/>
                <path d="M225 194l-4 27h13l5-27M247 194l5 27h13l-4-27" fill="#344c42"/>
                <!-- Sinh viên bên phải -->
                <circle cx="319" cy="105" r="16" fill="#b97754"/>
                <path d="M302 105c-2-15 8-25 21-24 11 1 16 10 14 22l-7-8-19 1z" fill="#302e29"/>
                <path d="M302 128c-8 9-11 24-10 42l-4 33h18l9-28 7 28h18l-5-38c-1-19-8-31-19-37z" fill="#e6d6b6"/>
                <path d="M304 135l-17 27 8 6 20-22M333 135l15 25-8 6-19-21" fill="none" stroke="#b97754" stroke-width="8" stroke-linecap="round"/>
                <path d="M307 201l-4 20h12l5-20M327 201l5 20h12l-4-20" fill="#3e574a"/>
                <!-- cây nhỏ trang trí -->
                <path d="M370 214V91" stroke="#8db49a" stroke-width="3" stroke-linecap="round"/>
                <path d="M370 143c-27-1-37-16-35-31 22 2 34 12 35 31M370 122c22-4 31-18 27-30-18 5-27 15-27 30M370 174c-21-1-31-13-30-24 17 2 28 10 30 24" fill="#b9d2b9"/>
                <path d="M47 215h326" stroke="#b9d3c1" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <p class="brand-footer">GẶP GỠ · KẾT NỐI · TRƯỞNG THÀNH</p>
        </div>
    </section>

    <section class="form-panel">
        <div class="form-inner">
            <div class="form-heading">
                <p class="mini-label">Bắt đầu hành trình của bạn</p>
                <h2>Đăng ký tài khoản</h2>
            </div>

            <?php if ($loi !== ''): ?>
                <div class="alert" role="alert"><?= htmlspecialchars($loi, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="POST" action="" autocomplete="on">
                <div class="field">
                    <label for="ho_ten">Họ tên</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 20c.5-4 3-6 7-6s6.5 2 7 6"/></svg>
                        <input id="ho_ten" name="ho_ten" type="text" placeholder="Nhập họ và tên" value="<?= htmlspecialchars($ho_ten, ENT_QUOTES, 'UTF-8') ?>" autocomplete="name" required>
                    </div>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m4 7 8 6 8-6"/></svg>
                        <input id="email" name="email" type="email" placeholder="name@example.com" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" required>
                    </div>
                </div>
                <div class="field">
                    <label for="so_dien_thoai">Số điện thoại</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M10 18h4"/></svg>
                        <input id="so_dien_thoai" name="so_dien_thoai" type="tel" placeholder="Nhập số điện thoại" value="<?= htmlspecialchars($so_dien_thoai, ENT_QUOTES, 'UTF-8') ?>" autocomplete="tel" required>
                    </div>
                </div>
                <div class="field">
                    <label for="mat_khau">Mật khẩu</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/></svg>
                        <input id="mat_khau" name="mat_khau" type="password" placeholder="Tối thiểu 8 ký tự" autocomplete="new-password" minlength="8" required>
                    </div>
                </div>
                <div class="field">
                    <label for="xac_nhan_mat_khau">Xác nhận mật khẩu</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M9 15l2 2 4-4"/></svg>
                        <input id="xac_nhan_mat_khau" name="xac_nhan_mat_khau" type="password" placeholder="Nhập lại mật khẩu" autocomplete="new-password" required>
                    </div>
                </div>
                <button class="submit-btn" type="submit">Đăng ký</button>
            </form>

            <p class="login-link">Đã có tài khoản? <a href="dang_nhap.php">Đăng nhập ngay</a></p>
        </div>
    </section>
</main>
<script>
    const form = document.querySelector('form');
    form.addEventListener('submit', function (event) {
        const password = document.getElementById('mat_khau').value;
        const confirmPassword = document.getElementById('xac_nhan_mat_khau').value;
        if (password !== confirmPassword) {
            event.preventDefault();
            alert('Mật khẩu xác nhận không khớp. Vui lòng kiểm tra lại.');
            document.getElementById('xac_nhan_mat_khau').focus();
        }
    });
</script>
</body>
</html>
