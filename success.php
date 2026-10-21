
<?php
session_start();

$sinhVien = $_SESSION['sinh_vien'] ?? null;
unset($_SESSION['sinh_vien']);

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dang ky thanh cong</title>
</head>
<body>
    <h2>Dang ky thanh cong</h2>

    <?php if ($sinhVien): ?>
        <p>Ho ten: <?= e($sinhVien['ten']) ?></p>
        <p>Email: <?= e($sinhVien['email']) ?></p>
        <p>Ngay sinh: <?= e($sinhVien['ngay_sinh']) ?></p>
    <?php else: ?>
        <p>Chua co thong tin dang ky.</p>
    <?php endif; ?>

    <a href="b3_7.php">Quay lai form</a>
</body>
</html>
