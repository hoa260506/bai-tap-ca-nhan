
<?php
$errors = [];

$data = [
    'ten' => '',
    'email' => '',
    'tieu_de' => '',
    'noi_dung' => ''
];

$thanhCong = false;

// Ma hoa du lieu truoc khi hien thi ra HTML
function e($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Dem so ky tu
function soKyTu($value) {
    return mb_strlen($value, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Doc va chuan hoa du lieu dau vao
    foreach ($data as $key => $value) {
        $input = $_POST[$key] ?? '';

        $data[$key] = is_string($input)
            ? trim($input)
            : '';
    }

    // 1. Kiem tra ho ten
    if ($data['ten'] === '') {
        $errors['ten'] = 'Vui long nhap ho ten.';
    } elseif (soKyTu($data['ten']) > 100) {
        $errors['ten'] = 'Ho ten khong duoc qua 100 ky tu.';
    }

    // 2. Kiem tra email
    if ($data['email'] === '') {
        $errors['email'] = 'Vui long nhap email.';
    } elseif (strlen($data['email']) > 254) {
        $errors['email'] = 'Email qua dai.';
    } elseif (!filter_var(
        $data['email'],
        FILTER_VALIDATE_EMAIL
    )) {
        $errors['email'] = 'Email khong dung dinh dang.';
    }

    // 3. Kiem tra tieu de
    if ($data['tieu_de'] === '') {
        $errors['tieu_de'] = 'Vui long nhap tieu de.';
    } elseif (soKyTu($data['tieu_de']) > 150) {
        $errors['tieu_de'] = 'Tieu de khong duoc qua 150 ky tu.';
    }

    // 4. Kiem tra noi dung
    if ($data['noi_dung'] === '') {
        $errors['noi_dung'] = 'Vui long nhap noi dung.';
    } elseif (soKyTu($data['noi_dung']) < 10) {
        $errors['noi_dung'] = 'Noi dung phai co it nhat 10 ky tu.';
    } elseif (soKyTu($data['noi_dung']) > 2000) {
        $errors['noi_dung'] = 'Noi dung khong duoc qua 2000 ky tu.';
    }

    // Neu khong co loi
    if (empty($errors)) {
        $thanhCong = true;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bai 3.9 - Form lien he an toan</title>
</head>
<body>

    <h2>Form lien he an toan</h2>

    <?php if ($thanhCong): ?>

        <h3>Gui lien he thanh cong!</h3>

        <p>Ho ten: <?= e($data['ten']) ?></p>
        <p>Email: <?= e($data['email']) ?></p>
        <p>Tieu de: <?= e($data['tieu_de']) ?></p>

        <p>Noi dung:</p>
        <p><?= nl2br(e($data['noi_dung'])) ?></p>

        <a href="b3_9.php">Quay lai form</a>

    <?php else: ?>

        <form method="POST">

            <label>Ho ten:</label><br>
            <input
                type="text"
                name="ten"
                maxlength="100"
                value="<?= e($data['ten']) ?>"
            >
            <p><?= e($errors['ten'] ?? '') ?></p>

            <label>Email:</label><br>
            <input
                type="text"
                name="email"
                maxlength="254"
                value="<?= e($data['email']) ?>"
            >
            <p><?= e($errors['email'] ?? '') ?></p>

            <label>Tieu de:</label><br>
            <input
                type="text"
                name="tieu_de"
                maxlength="150"
                value="<?= e($data['tieu_de']) ?>"
            >
            <p><?= e($errors['tieu_de'] ?? '') ?></p>

            <label>Noi dung lien he:</label><br>
            <textarea
                name="noi_dung"
                rows="6"
                cols="40"
            ><?= e($data['noi_dung']) ?></textarea>
            <p><?= e($errors['noi_dung'] ?? '') ?></p>

            <button type="submit">Gui lien he</button>

        </form>

    <?php endif; ?>

</body>
</html>
