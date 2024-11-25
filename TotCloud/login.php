<?php
// login.php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar los datos del formulario
    $correo_electronico = filter_input(INPUT_POST, 'correo_electronico', FILTER_SANITIZE_EMAIL);
    $contrasenya = $_POST['contrasenya']; // Nota: En producción, las contraseñas deben ser manejadas de forma segura

    if ($correo_electronico && $contrasenya) {
        try {
            // Preparar la consulta para evitar inyecciones SQL
            $stmt = $pdo->prepare('SELECT id, contrasenya FROM usuarios WHERE correo_electronico = :correo_electronico');
            $stmt->execute(['correo_electronico' => $correo_electronico]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                // Verificar la contraseña (en producción, usa password_verify)
                if ($contrasenya === $usuario['contrasenya']) {
                    // Autenticación exitosa
                    $_SESSION['usuario_id'] = $usuario['id'];
                    header('Location: home.php');
                    exit();
                } else {
                    // Contraseña incorrecta
                    $error = 'Contraseña incorrecta.';
                }
            } else {
                // Usuario no encontrado
                $error = 'Correo electrónico no registrado.';
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