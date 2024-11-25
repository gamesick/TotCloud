<?php
// login.php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar los datos del formulario
    $nombreUsuario = trim($_POST['nombreUsuario']);
    $contrasenya = $_POST['contrasenya'];

    if ($nombreUsuario && $contrasenya) {
        try {
            // Preparar la consulta para evitar inyecciones SQL
            $stmt = $pdo->prepare('SELECT idUsuario, contrasenya FROM USUARIO WHERE nombreUsuario = :nombreUsuario');
            $stmt->execute(['nombreUsuario' => $nombreUsuario]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                // Verificar la contraseña usando password_verify
                if (password_verify($contrasenya, $usuario['contrasenya'])) {
                    // Autenticación exitosa
                    $_SESSION['usuario_id'] = $usuario['idUsuario'];
                    header('Location: home.php');
                    exit();
                } else {
                    // Contraseña incorrecta
                    $error = 'Contraseña incorrecta.';
                }
            } else {
                // Usuario no encontrado
                $error = 'Nombre de usuario no registrado.';
            }
        } catch (Exception $e) {
            // Manejo de errores
            $error = 'Error: ' . $e->getMessage();
        }
    } else {
        $error = 'Por favor, completa todos los campos.';
    }

    // Redirigir de vuelta al formulario con el error
    header('Location: index.php?error=' . urlencode($error));
    exit();
} else {
    // Acceso directo no permitido
    header('Location: index.php');
    exit();
}
?>