
<?php
$errors = [];
$data = [
    'ten' => '',
    'email' => '',
    'tieu_de' => '',
    'noi_dung' => ''
];
$thanhCong = false;

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $key => $value) {
        $data[$key] = trim($_POST[$key] ?? '');
    }

    if ($data['ten'] === '') {
        $errors['ten'] = 'Vui long nhap ho ten.';
    }

    if ($data['email'] === '') {
        $errors['email'] = 'Vui long nhap email.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email khong dung dinh dang.';
    }

    if ($data['tieu_de'] === '') {
        $errors['tieu_de'] = 'Vui long nhap tieu de.';
    }

    if ($data['noi_dung'] === '') {
        $errors['noi_dung'] = 'Vui long nhap noi dung.';
    }

    if (empty($errors)) {
        $thanhCong = true;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bai 3.8 - Form lien he</title>
</head>
<body>
    <h2>Form lien he</h2>

    <?php if ($thanhCong): ?>
        <p>Gui thong tin thanh cong!</p>
        <p>Cam on <?= e($data['ten']) ?> da lien he.</p>
    <?php else: ?>
        <form method="POST">
            <label>Ho ten:</label><br>
            <input name="ten" value="<?= e($data['ten']) ?>">
            <p><?= e($errors['ten'] ?? '') ?></p>

            <label>Email:</label><br>
            <input name="email" value="<?= e($data['email']) ?>">
            <p><?= e($errors['email'] ?? '') ?></p>

            <label>Tieu de:</label><br>
            <input name="tieu_de" value="<?= e($data['tieu_de']) ?>">
            <p><?= e($errors['tieu_de'] ?? '') ?></p>

            <label>Noi dung:</label><br>
            <textarea name="noi_dung" rows="5" cols="40"><?= e($data['noi_dung']) ?></textarea>
            <p><?= e($errors['noi_dung'] ?? '') ?></p>

            <label>Anh dai dien (khong bat buoc):</label><br>
            <input type="file" disabled>
            <p>Phan upload anh duoc thuc hanh rieng o bai 3.6.</p>

            <button type="submit">Gui lien he</button>
        </form>
    <?php endif; ?>
</body>
</html>
