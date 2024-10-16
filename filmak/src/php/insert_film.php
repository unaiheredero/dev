<?php
session_start();
require_once './db.php'; // Asegúrate de que este archivo contenga la clase Database

$database = new Database('db', 'mydatabase', 'root', 'root'); // Cambia estos valores
$dbConnection = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filmName = trim($_POST['film_name']);
    $isanNumber = trim($_POST['isan_number']);
    $year = trim($_POST['year']);
    $rating = trim($_POST['rating']);

    // Inicializar un mensaje para mostrar
    $message = "";

    // 1. Validar que el ISAN sea de 8 dígitos
    if (strlen($isanNumber) != 8 || !ctype_digit($isanNumber)) {
        $message = "El número ISAN debe tener 8 dígitos.";
    } else {
        // 2. Comprobar si el ISAN ya existe
        $query = "SELECT * FROM peliculas WHERE isan_number = ?";
        $stmt = $dbConnection->prepare($query);
        $stmt->execute([$isanNumber]);
        $filmExists = $stmt->fetch();

        // 3. Comprobar si el ISAN está vacío
        if (empty($isanNumber)) {
            // Si el ISAN está vacío, mostrar películas con el mismo nombre
            $filmQuery = "SELECT * FROM peliculas WHERE UPPER(film_name) = UPPER(?)";
            $filmStmt = $dbConnection->prepare($filmQuery);
            $filmStmt->execute([$filmName]);
            $films = $filmStmt->fetchAll();

            if ($films) {
                $message = "Películas encontradas con el nombre '$filmName':<br>";
                foreach ($films as $film) {
                    $message .= "ISAN: {$film['isan_number']}, Año: {$film['year']}, Puntuación: {$film['rating']}<br>";
                }
            } else {
                $message = "No se encontraron películas con el nombre '$filmName'.";
            }
        } elseif ($filmExists) {
            // 4. Actualizar película existente
            if (!empty($filmName) && !empty($year) && !empty($rating)) {
                $updateQuery = "UPDATE peliculas SET film_name = ?, year = ?, rating = ? WHERE isan_number = ?";
                $updateStmt = $dbConnection->prepare($updateQuery);

                if ($updateStmt->execute([$filmName, $year, $rating, $isanNumber])) {
                    $message = "Película actualizada correctamente.";
                } else {
                    $message = "Error al actualizar la película.";
                }
            }
        } else {
            // 5. Insertar nueva película si ISAN no existe
            if (!empty($filmName) && !empty($year) && !empty($rating)) {
                $insertQuery = "INSERT INTO peliculas (film_name, isan_number, year, rating) VALUES (?, ?, ?, ?)";
                $insertStmt = $dbConnection->prepare($insertQuery);

                if ($insertStmt->execute([$filmName, $isanNumber, $year, $rating])) {
                    $message = "Película insertada correctamente.";
                } else {
                    $message = "Error al insertar la película.";
                }
            } else {
                $message = "Por favor, complete todos los campos requeridos para insertar una nueva película.";
            }
        }
    }
}

// Recuperar datos para el formulario
$prevFilmName = isset($filmName) ? $filmName : '';
$prevIsanNumber = isset($isanNumber) ? $isanNumber : '';
$prevYear = isset($year) ? $year : '';
$prevRating = isset($rating) ? $rating : '';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Insertar Película</title>
</head>
<body>
    <h2>Insertar Película</h2>
    <?php if (!empty($message)) { echo "<p>$message</p>"; } ?>
    <form method="post" action="insert_film.php">
        <label for="film_name">Nombre de la película:</label>
        <input type="text" name="film_name" id="film_name" value="<?php echo htmlspecialchars($prevFilmName); ?>" required><br><br>
        
        <label for="isan_number">Número ISAN (8 dígitos):</label>
        <input type="text" name="isan_number" id="isan_number" value="<?php echo htmlspecialchars($prevIsanNumber); ?>" required><br><br>
        
        <label for="year">Año:</label>
        <input type="number" name="year" id="year" value="<?php echo htmlspecialchars($prevYear); ?>" required><br><br>
        
        <label for="rating">Tu puntuación (0-5):</label>
        <input type="number" name="rating" id="rating" min="0" max="5" value="<?php echo htmlspecialchars($prevRating); ?>" required><br><br>
        
        <button type="submit">Insertar Película</button>
    </form>
    <br><br>
    <a href="index.php">Volver a la página principal</a>
</body>
</html>
