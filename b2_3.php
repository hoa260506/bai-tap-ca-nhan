
<?php
echo "<h2>Bai 2.3: Phan loai diem</h2>";

$diem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diem = $_POST["diem"] ?? "";

    if ($diem === "" || !is_numeric($diem) || $diem < 0 || $diem > 10) {
        echo "Vui long nhap diem tu 0 den 10!";
    } else {
        echo "Diem: " . htmlspecialchars($diem) . "<br>";

        if ($diem >= 8) {
            echo "Xep loai: Gioi";
        } elseif ($diem >= 6.5) {
            echo "Xep loai: Kha";
        } elseif ($diem >= 5) {
            echo "Xep loai: Trung binh";
        } else {
            echo "Xep loai: Chua dat";
        }
    }
}
?>

<form method="POST">
    <label>Nhap diem (0 - 10):</label>
    <input type="number" name="diem" min="0" max="10"
           step="0.1"
           value="<?= htmlspecialchars((string)$diem) ?>">
    <button type="submit">Phan loai</button>
</form>
