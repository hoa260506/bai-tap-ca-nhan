<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kiểm tra điểm</title>
</head>
<body>
    <h2>KIỂM TRA ĐIỂM </h2>

    <form method="post">
        <label>Nhập điểm từ 0 - 10:</label>
        <input type="number" name="diem"
               min="0" max="10" step="any"
               required>
        <button type="submit">Kiểm tra</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $diem = $_POST["diem"];

        if (is_numeric($diem) && $diem >= 0 && $diem <= 10) {
            echo "<p>Điểm $diem hợp lệ!</p>";
        } else {
            echo "<p>Điểm không hợp lệ! Vui lòng nhập từ 0 đến 10.</p>";
        }
    }
    ?>
</body>
</html>