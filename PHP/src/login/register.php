<?php
require_once './db.php'; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Encriptar la contraseña
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Preparar la consulta para insertar el usuario
    $stmt = $pdo->prepare("INSERT INTO test (username, password) VALUES (:username, :password)");
    
    if ($stmt->execute(['username' => $username, 'password' => $password_hashed])) {
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
