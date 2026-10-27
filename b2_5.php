
<?php
echo "<h2>Bai 2.5: Ham tinh diem tong ket</h2>";

function finalScore(float $cc, float $mid, float $final): float
{
    return $cc * 0.10
         + $mid * 0.30
         + $final * 0.60;
}

$cc = 8;
$mid = 7;
$final = 9;

$ketQua = finalScore($cc, $mid, $final);

echo "Diem chuyen can: " . $cc . "<br>";
echo "Diem giua ky: " . $mid . "<br>";
echo "Diem cuoi ky: " . $final . "<br>";
echo "Diem tong ket: " . number_format($ketQua, 2);
?>
