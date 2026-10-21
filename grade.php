
<?php
function isValidScore($score): bool
{
    return $score !== ""
        && is_numeric($score)
        && (float)$score >= 0
        && (float)$score <= 10;
}

function finalScore(float $cc, float $mid, float $final): float
{
    return $cc * 0.10
         + $mid * 0.30
         + $final * 0.60;
}

function classifyScore(float $score): string
{
    if ($score >= 8) {
        return "Gioi";
    } elseif ($score >= 6.5) {
        return "Kha";
    } elseif ($score >= 5) {
        return "Trung binh";
    }

    return "Chua dat";
}

$name = "";
$cc = "";
$mid = "";
$final = "";
$errors = [];
$result = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $cc = trim($_POST["cc"] ?? "");
    $mid = trim($_POST["mid"] ?? "");
    $final = trim($_POST["final"] ?? "");

    if ($name === "") {
        $errors["name"] = "Vui long nhap ho ten.";
    }

    if (!isValidScore($cc)) {
        $errors["cc"] = "Diem chuyen can phai tu 0 den 10.";
    }

    if (!isValidScore($mid)) {
        $errors["mid"] = "Diem giua ky phai tu 0 den 10.";
    }

    if (!isValidScore($final)) {
        $errors["final"] = "Diem cuoi ky phai tu 0 den 10.";
    }

    if (empty($errors)) {
        $score = finalScore(
            (float)$cc,
            (float)$mid,
            (float)$final
        );

        $result = [
            "score" => $score,
            "classification" => classifyScore($score)
        ];
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>May tinh diem hoc phan</title>
</head>
<body>
    <h1>Bai 2.9: May tinh diem hoc phan</h1>

    <form method="post">
        <p>
            <label>Ho ten:</label>
            <input type="text" name="name"
                value="<?= e($name) ?>">
            <?php if (isset($errors["name"])): ?>
                <span style="color:red">
                    <?= e($errors["name"]) ?>
                </span>
            <?php endif; ?>
        </p>

        <p>
            <label>Diem chuyen can:</label>
            <input type="number" name="cc" min="0" max="10"
                step="any" value="<?= e($cc) ?>">
            <?php if (isset($errors["cc"])): ?>
                <span style="color:red">
                    <?= e($errors["cc"]) ?>
                </span>
            <?php endif; ?>
        </p>

        <p>
            <label>Diem giua ky:</label>
            <input type="number" name="mid" min="0" max="10"
                step="any" value="<?= e($mid) ?>">
            <?php if (isset($errors["mid"])): ?>
                <span style="color:red">
                    <?= e($errors["mid"]) ?>
                </span>
            <?php endif; ?>
        </p>

        <p>
            <label>Diem cuoi ky:</label>
            <input type="number" name="final" min="0" max="10"
                step="any" value="<?= e($final) ?>">
            <?php if (isset($errors["final"])): ?>
                <span style="color:red">
                    <?= e($errors["final"]) ?>
                </span>
            <?php endif; ?>
        </p>

        <button type="submit">Tinh diem</button>
    </form>

    <?php if ($result !== null): ?>
        <h2>Ket qua</h2>
        <p>Ho ten: <?= e($name) ?></p>
        <p>Diem tong ket:
            <?= number_format($result["score"], 2) ?>
        </p>
        <p>Xep loai:
            <?= e($result["classification"]) ?>
        </p>
    <?php endif; ?>
</body>
</html>
