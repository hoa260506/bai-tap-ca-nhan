
<?php
echo "<h2>Bai 2.4: Mang va foreach</h2>";

$students = [
    ["name" => "Vân Anh", "score" => 8.5],
    ["name" => "Khánh Chi", "score" => 7],
    ["name" => "Minh Dương", "score" => 9]
];

echo "<table style='border-collapse: collapse;' border='1' cellpadding='8'>";

echo "<tr>";
echo "<th style='border: 1px solid black;'>Ho ten</th>";
echo "<th style='border: 1px solid black;'>Diem</th>";
echo "</tr>";

foreach ($students as $student) {
    echo "<tr>";

    echo "<td style='border: 1px solid black;'>"
        . htmlspecialchars($student["name"], ENT_QUOTES, 'UTF-8')
        . "</td>";

    echo "<td style='border: 1px solid black;'>"
        . $student["score"]
        . "</td>";

    echo "</tr>";
}

echo "</table>";
?>
