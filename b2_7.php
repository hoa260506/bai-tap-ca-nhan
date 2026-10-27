
<?php
function money(float $value): string
{
    return number_format($value, 0, ',', '.') . ' đ';
}

$name = "";
$qty = "";
$price = "";
$total = 0;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $qtyInput = $_POST["qty"] ?? "";
    $priceInput = $_POST["price"] ?? "";

    if ($name === "") {
        $errors[] = "Vui long nhap ten tai lieu.";
    }

    if (filter_var($qtyInput, FILTER_VALIDATE_INT) === false
        || (int)$qtyInput < 1) {
        $errors[] = "So luong phai la so nguyen tu 1 tro len.";
    } else {
        $qty = (int)$qtyInput;
    }

    if ($priceInput === "" || !is_numeric($priceInput)
        || (float)$priceInput < 0) {
        $errors[] = "Don gia phai la so khong am.";
    } else {
        $price = (float)$priceInput;
    }

    if (empty($errors)) {
        $total = $qty * $price;
    }
}
?>

<h2>Bai 2.7: Hoa don hoc lieu</h2>

<form method="post">
    <p>
        <label>Ten tai lieu:</label>
        <input type="text" name="name"
            value="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>">
    </p>

    <p>
        <label>So luong:</label>
        <input type="number" name="qty" min="1"
            value="<?= htmlspecialchars((string)$qty, ENT_QUOTES, 'UTF-8') ?>">
    </p>

    <p>
        <label>Don gia:</label>
        <input type="number" name="price" min="0" step="any"
            value="<?= htmlspecialchars((string)$price, ENT_QUOTES, 'UTF-8') ?>">
    </p>

    <button type="submit">Tinh tien</button>
</form>

<?php foreach ($errors as $error): ?>
    <p style="color:red">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endforeach; ?>

<?php if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($errors)): ?>
    <h3>Ket qua</h3>
    <p>Ten tai lieu:
        <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
    </p>
    <p>Thanh tien: <?= money((float)$total) ?></p>
<?php endif; ?>
