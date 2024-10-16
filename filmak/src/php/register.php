<?php
session_start();
require_once './db.php'; // Asegúrate de que este archivo contenga la clase Database
require_once './User.php'; // Asegúrate de incluir el archivo de la clase User

$database = new Database('db', 'mydatabase', 'root', 'root'); // Cambia estos valores
$dbConnection = $database->getConnection();
$userModel = new User($dbConnection);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Intentar registrar el usuario
    if ($userModel->register($username, $password)) {
        echo "Registro exitoso. Puedes iniciar sesión ahora.";
    } else {
        echo "Error en el registro.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
</head>
<body>
    <h2>Registro</h2>
    <form method="post" action="register.php">
        <label for="username">Usuario:</label>
        <input type="text" name="username" id="username" required><br><br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required><br><br>
        <button type="submit">Registrar</button>
    </form>
    <br><br>
    <a href="login.php">Iniciar sesión</a>
</body>
</html>
