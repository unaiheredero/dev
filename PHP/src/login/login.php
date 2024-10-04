<?php
session_start();
require_once './db.php'; // Asegúrate de que este archivo contenga la clase Database
require_once './User.php'; // Incluye el archivo de la clase User

$error = '';
$database = new Database('db', 'mydatabase', 'root', 'root'); // Cambia estos valores
$dbConnection = $database->getConnection();
$userModel = new User($dbConnection);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Intentar iniciar sesión
    $user = $userModel->login($username, $password);

    if ($user) {
        // Guardar el nombre de usuario en la sesión
        $_SESSION['username'] = $username;
        header('Location: welcome.php');
        exit();
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post" action="login.php">
        <label for="username">Usuario:</label>
        <input type="text" name="username" id="username" required><br><br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required><br><br>
        <button type="submit">Iniciar sesión</button>
    </form>
    <br><br>
    <a href="register.php">Registrar usuario</a>
</body>
</html>
