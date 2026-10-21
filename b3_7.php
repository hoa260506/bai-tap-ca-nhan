
<?php
$errors = [];
$data = [
    'ten' => '',
    'email' => '',
    'ngay_sinh' => ''
];

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['ten'] = trim($_POST['ten'] ?? '');
    $data['email'] = trim($_POST['email'] ?? '');
    $data['ngay_sinh'] = trim($_POST['ngay_sinh'] ?? '');

    if ($data['ten'] === '') {
        $errors['ten'] = 'Vui long nhap ho ten.';
    }

    if ($data['email'] === '') {
        $errors['email'] = 'Vui long nhap email.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email khong dung dinh dang.';
    }

    if ($data['ngay_sinh'] === '') {
        $errors['ngay_sinh'] = 'Vui long chon ngay sinh.';
    } else {
        $ngay = DateTime::createFromFormat('!Y-m-d', $data['ngay_sinh']);

        if (!$ngay || $ngay->format('Y-m-d') !== $data['ngay_sinh']) {
            $errors['ngay_sinh'] = 'Ngay sinh khong hop le.';
        }
    }

    if (empty($errors)) {
        session_start();
        $_SESSION['sinh_vien'] = $data;

        header('Location: success.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bai 3.7 - Dang ky sinh vien</title>
</head>
<body>
    <h2>Dang ky sinh vien</h2>

    <form method="POST">
        <label>Ho ten:</label><br>
        <input type="text" name="ten" value="<?= e($data['ten']) ?>">
        <p><?= e($errors['ten'] ?? '') ?></p>

        <label>Email:</label><br>
        <input type="text" name="email" value="<?= e($data['email']) ?>">
        <p><?= e($errors['email'] ?? '') ?></p>

        <label>Ngay sinh:</label><br>
        <input type="date" name="ngay_sinh" value="<?= e($data['ngay_sinh']) ?>">
        <p><?= e($errors['ngay_sinh'] ?? '') ?></p>

        <button type="submit">Dang ky</button>
    </form>
</body>
</html>
