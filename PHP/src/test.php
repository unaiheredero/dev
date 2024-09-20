<?php
// JSON datuak definitu
$data = [
    ['Izena' => 'Ane', 'Adina' => 25, 'Hiriburua' => 'Bilbao'],
    ['Izena' => 'Iñaki', 'Adina' => 30, 'Hiriburua' => 'Donostia'],
    ['Izena' => 'Marta', 'Adina' => 22, 'Hiriburua' => 'Gasteiz'],
];

// JSON-a inprimatu
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>JSON Datuak</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            margin-top: 20px;
        }
        .data {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>JSON Datuak</h1>
    <div class="container" id="data-container">
        <?php
        // JSON-a PHP-n irakurri eta irudia sortu
        foreach ($data as $item) {
            echo '<div class="data">';
            echo 'Izena: ' . htmlspecialchars($item['Izena']) . ', ';
            echo 'Adina: ' . htmlspecialchars($item['Adina']) . ', ';
            echo 'Hiriburua: ' . htmlspecialchars($item['Hiriburua']);
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
