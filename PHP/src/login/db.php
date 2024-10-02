<?php
$host = 'db';  // Cambia según tu configuración
$db = 'mydatabase';  // Cambia según tu configuración
$user = 'root';  // Cambia según tu configuración
$pass = 'root';  // Cambia según tu configuración

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
