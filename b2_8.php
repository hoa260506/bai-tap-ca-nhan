
<?php
echo "<h2>Bai 2.8: Bang diem 3 sinh vien</h2>";

$students = [
    [
        "name" => "Vân Anh",
        "midterm" => 7,
        "final" => 8
    ],
    [
        "name" => "Khánh Chi",
        "midterm" => 4,
        "final" => 5
    ],
    [
        "name" => "Minh Dương",
        "midterm" => 8,
        "final" => 9
    ]
];

function calculateAverage(float $midterm, float $final): float
{
    return ($midterm + $final) / 2;
}

echo "<table style='border-collapse: collapse;' border='1' cellpadding='8'>";

echo "<tr>";
echo "<th style='border: 1px solid black;'>Ho ten</th>";
echo "<th style='border: 1px solid black;'>Giua ky</th>";
echo "<th style='border: 1px solid black;'>Cuoi ky</th>";
echo "<th style='border: 1px solid black;'>Trung binh</th>";
echo "<th style='border: 1px solid black;'>Ket qua</th>";
echo "</tr>";

foreach ($students as $student) {
    $average = calculateAverage(
        (float)$student["midterm"],
        (float)$student["final"]
    );

    $result = $average >= 5 ? "Dat" : "Chua dat";

    echo "<tr>";

    echo "<td style='border: 1px solid black;'>"
        . htmlspecialchars($student["name"], ENT_QUOTES, 'UTF-8')
        . "</td>";

    echo "<td style='border: 1px solid black;'>"
        . $student["midterm"]
        . "</td>";

    echo "<td style='border: 1px solid black;'>"
        . $student["final"]
        . "</td>";

    echo "<td style='border: 1px solid black;'>"
        . number_format($average, 2)
        . "</td>";

    echo "<td style='border: 1px solid black;'>"
        . $result
        . "</td>";

    echo "</tr>";
}

echo "</table>";
?>
