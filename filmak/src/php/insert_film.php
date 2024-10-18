<?php
session_start();
require_once './db.php'; // Datu basearen konexiorako

$database = new Database('db', 'mydatabase', 'root', 'root'); // Datu baseko balioak egokitu
$dbConnection = $database->getConnection();

$message = "";

// Filma gehitzeko, borratzeko edo eguneratzeko funtzionalitatea
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filmName = trim($_POST['film_name']);
    $isanNumber = trim($_POST['isan_number']);
    $year = trim($_POST['year']);
    $rating = trim($_POST['rating']);

    // 1. Izena eta ISAN zenbakia oker badaude
    if (empty($filmName) && empty($isanNumber)) {
        $message = "Izena eta ISAN zenbakia bete behar dituzu.";
    } elseif (!empty($isanNumber) && (strlen($isanNumber) != 8 || !ctype_digit($isanNumber))) {
        $message = "ISAN zenbakiak 8 digitu izan behar ditu.";
    } else {
        // 2. ISAN ez badago zerrendan eta ISAN sartuta badago, erregistroa gorde
        $query = "SELECT * FROM peliculas WHERE isan_number = ?";
        $stmt = $dbConnection->prepare($query);
        $stmt->execute([$isanNumber]);
        $filmExists = $stmt->fetch();

        // 3. ISAN hutsik dagoen kasua eta izenaren arabera bilaketa
        if (empty($isanNumber) && !empty($filmName)) {
            $filmQuery = "SELECT * FROM peliculas WHERE UPPER(film_name) = UPPER(?)";
            $filmStmt = $dbConnection->prepare($filmQuery);
            $filmStmt->execute([$filmName]);
            $films = $filmStmt->fetchAll();

            if ($films) {
                $message = "Filmak izenarekin: '$filmName':<br>";
                foreach ($films as $film) {
                    $message .= "ISAN: {$film['isan_number']}, Urtea: {$film['year']}, Puntuazioa: {$film['rating']}<br>";
                }
            } else {
                $message = "Ez da filmik aurkitu izen horrekin.";
            }
        } elseif ($filmExists) {
            // 4. ISAN zerrendan badago, datuak eguneratu
            if (!empty($filmName) && !empty($year) && !empty($rating)) {
                $updateQuery = "UPDATE peliculas SET film_name = ?, year = ?, rating = ? WHERE isan_number = ?";
                $updateStmt = $dbConnection->prepare($updateQuery);

                if ($updateStmt->execute([$filmName, $year, $rating, $isanNumber])) {
                    $message = "Filma eguneratu da.";
                } else {
                    $message = "Errorea filmaren eguneratzean.";
                }
            }
        } else {
            // 5. Erregistro berri bat gehitu
            if (!empty($filmName) && !empty($year) && !empty($rating)) {
                $insertQuery = "INSERT INTO peliculas (film_name, isan_number, year, rating) VALUES (?, ?, ?, ?)";
                $insertStmt = $dbConnection->prepare($insertQuery);

                if ($insertStmt->execute([$filmName, $isanNumber, $year, $rating])) {
                    $message = "Filma gehitu da.";
                } else {
                    $message = "Errorea film berria gehitzean.";
                }
            } else {
                $message = "Eremu guztiak bete behar dituzu erregistro berri bat gehitzeko.";
            }
        }

        // 6. ISAN sartuta eta izena hutsik badago, filma ezabatu eta abisua
        if (!empty($isanNumber) && empty($filmName)) {
            if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == "1") {
                // Ezabatu filma
                $deleteQuery = "DELETE FROM peliculas WHERE isan_number = ?";
                $deleteStmt = $dbConnection->prepare($deleteQuery);
                if ($deleteStmt->execute([$isanNumber])) {
                    $message = "Filma ezabatu da ISAN zenbakiarekin: $isanNumber.";
                } else {
                    $message = "Errorea filmaren ezabaketan.";
                }
            } else {
                // Abisu mezua bidali ezabaketa berresteko
                $message = "Ziur zaude film hau ezabatu nahi duzula? <br>
                            <form method='post'>
                                <input type='hidden' name='isan_number' value='$isanNumber'>
                                <input type='hidden' name='confirm_delete' value='1'>
                                <button type='submit'>Bai, ezabatu</button>
                            </form>";
            }
        }
    }
}

// Filmak zerrendatu
$filmQuery = "SELECT * FROM peliculas";
$films = $dbConnection->query($filmQuery)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Filmen kudeaketa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
        }
        .films-list, .film-form {
            width: 50%;
            padding: 20px;
        }
        .films-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .films-list table, .films-list th, .films-list td {
            border: 1px solid #ddd;
        }
        .films-list th, .films-list td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>

<h1>Filmen kudeaketa</h1>

<div class="container">
    <!-- Filmen zerrenda -->
    <div class="films-list">
        <h2>Filmen informazioa</h2>
        <?php if (!empty($films)): ?>
        <table>
            <thead>
                <tr>
                    <th>Filma</th>
                    <th>ISAN</th>
                    <th>Urtea</th>
                    <th>Puntuazioa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($films as $film): ?>
                <tr>
                    <td><?php echo htmlspecialchars($film['film_name']); ?></td>
                    <td><?php echo htmlspecialchars($film['isan_number']); ?></td>
                    <td><?php echo htmlspecialchars($film['year']); ?></td>
                    <td><?php echo htmlspecialchars($film['rating']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>Ez dago erregistratutako filmarik.</p>
        <?php endif; ?>
    </div>

    <!-- Filmen formularioa -->
    <div class="film-form">
        <h2>Filma gehitu / eguneratu</h2>
        <?php if (!empty($message)) { echo "<p>$message</p>"; } ?>
        <form method="post" action="">
            <label for="film_name">Filmaren izena:</label>
            <input type="text" name="film_name" id="film_name" value="<?php echo isset($filmName) ? htmlspecialchars($filmName) : ''; ?>"><br><br>

            <label for="isan_number">ISAN zenbakia (8 digitu):</label>
            <input type="text" name="isan_number" id="isan_number" value="<?php echo isset($isanNumber) ? htmlspecialchars($isanNumber) : ''; ?>"><br><br>

            <label for="year">Urtea:</label>
            <input type="number" name="year" id="year" value="<?php echo isset($year) ? htmlspecialchars($year) : ''; ?>"><br><br>

            <label for="rating">Puntuazioa:</label>
            <select name="rating" id="rating">
                <option value="0" <?php echo (isset($rating) && $rating == 0) ? 'selected' : ''; ?>>0</option>
                <option value="1" <?php echo (isset($rating) && $rating == 1) ? 'selected' : ''; ?>>1</option>
                <option value="2" <?php echo (isset($rating) && $rating == 2) ? 'selected' : ''; ?>>2</option>
                <option value="3" <?php echo (isset($rating) && $rating == 3) ? 'selected' : ''; ?>>3</option>
                <option value="4" <?php echo (isset($rating) && $rating == 4) ? 'selected' : ''; ?>>4</option>
                <option value="5" <?php echo (isset($rating) && $rating == 5) ? 'selected' : ''; ?>>5</option>
            </select><br><br>

            <button type="submit">Bidali</button>
        </form>
    </div>
</div>

</body>
</html>
