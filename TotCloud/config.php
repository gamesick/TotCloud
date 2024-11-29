<?php
// config.php

$host = 'localhost';
$db   = 'totcloud';
$user = 'app_user'; // Usuario creado con permisos limitados
$pass = 'tu_contraseña_segura'; // Contraseña del usuario
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
    error_log("Error de conexión: " . $e->getMessage());
    echo "Error de conexión. Por favor, intenta de nuevo más tarde.";
    exit();
}
?>
