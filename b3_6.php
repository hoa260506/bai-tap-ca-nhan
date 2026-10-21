
<?php
$thongBao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['anh']) || $_FILES['anh']['error'] !== UPLOAD_ERR_OK) {
        $thongBao = 'Vui long chon anh hop le de tai len.';
    } else {
        $file = $_FILES['anh'];
        $dungLuongToiDa = 2 * 1024 * 1024;

        $cacDinhDang = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        // Kiem tra dung luong
        if ($file['size'] > $dungLuongToiDa) {
            $thongBao = 'Anh khong duoc lon hon 2 MB.';
        } else {
            // Kiem tra MIME type tu noi dung file
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);

            if (!isset($cacDinhDang[$mime])) {
                $thongBao = 'Chi chap nhan anh JPG, PNG hoac WEBP.';
            } else {
                $thuMuc = __DIR__ . '/uploads';

                if (!is_dir($thuMuc)) {
                    mkdir($thuMuc, 0755, true);
                }

                // Tao ten moi, khong dung ten goc cua nguoi dung
                $tenMoi = bin2hex(random_bytes(16))
                    . '.' . $cacDinhDang[$mime];

                $duongDan = $thuMuc . '/' . $tenMoi;

                if (move_uploaded_file($file['tmp_name'], $duongDan)) {
                    $thongBao = 'Tai anh len thanh cong. Ten file: ' . $tenMoi;
                } else {
                    $thongBao = 'Khong the luu file.';
                }
            }
        }
    }
}

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bai 3.6 - Upload an toan</title>
</head>
<body>
    <h2>Bai 3.6 - Upload anh dai dien</h2>

    <form method="POST" enctype="multipart/form-data">
        <label>Chon anh (JPG, PNG, WEBP; toi da 2 MB):</label>
        <input type="file" name="anh" accept=".jpg,.jpeg,.png,.webp" required>
        <button type="submit">Tai anh len</button>
    </form>

    <?php if ($thongBao !== ''): ?>
        <p><?= e($thongBao) ?></p>
    <?php endif; ?>
</body>
</html>
