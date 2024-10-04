<?php
class User {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM test WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user; // Devuelve el usuario si las credenciales son correctas
        }

        return false; // Devuelve false si las credenciales son incorrectas
    }
    
    public function register($username, $password) {
        // Encriptar la contraseña
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Preparar la consulta para insertar el usuario
        $stmt = $this->db->prepare("INSERT INTO test (username, password) VALUES (:username, :password)");

        return $stmt->execute(['username' => $username, 'password' => $password_hashed]);
    }
}
