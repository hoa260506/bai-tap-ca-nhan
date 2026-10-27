
<?php
$tuKhoa = $_GET["keyword"] ?? "";
$thongBao = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tenSinhVien = trim($_POST["name"] ?? "");

    if ($tenSinhVien === "") {
        $thongBao = "Vui long nhap ten sinh vien.";
    } else {
        $thongBao = "Da nhan du lieu cua: " . $tenSinhVien;
    }
}
?>

<h2>Bai 2.6: GET va POST</h2>

<h3>Tim kiem bang GET</h3>
<form method="get">
    <label>Ten can tim:</label>
    <input type="text" name="keyword"
           value="<?= htmlspecialchars($tuKhoa, ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit">Tim kiem</button>
</form>

<?php if ($tuKhoa !== ""): ?>
    <p>Tu khoa tim kiem:
        <?= htmlspecialchars($tuKhoa, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

<hr>

<h3>Gui du lieu bang POST</h3>
<form method="post">
    <label>Ten sinh vien:</label>
    <input type="text" name="name">
    <button type="submit">Gui du lieu</button>
</form>

<?php if ($thongBao !== ""): ?>
    <p><?= htmlspecialchars($thongBao, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
