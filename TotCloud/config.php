<?php
// config.php

$host = 'localhost';
$db   = 'totcloud';
$user = 'root'; // Reemplaza con tu usuario de MySQL
$pass = '';     // Reemplaza con tu contraseña de MySQL si tiene
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Manejo de errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Modo de fetch
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Deshabilitar emulación
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Manejo de errores de conexión
    echo "Error de conexión: " . $e->getMessage();
    exit();
}
?>
