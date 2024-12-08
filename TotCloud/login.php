<?php
// login.php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar los datos del formulario
    $nombre = trim($_POST['nombreUsuario']);
    $contrasenya = $_POST['contrasenya'];

    if ($nombre && $contrasenya) {
        try {
            // Intentar autenticar en la tabla USUARIO
            $stmt = $pdo->prepare('SELECT idUsuario, contrasenya, idGrupo FROM USUARIO WHERE nombreUsuario = :nombreUsuario');
            $stmt->execute(['nombreUsuario' => $nombre]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                // Verificar la contraseña usando password_verify
                if (password_verify($contrasenya, $usuario['contrasenya'])) {
                    // Autenticación exitosa
                    $_SESSION['usuario_id'] = $usuario['idUsuario'];
                    switch ($usuario['idGrupo']) {
                        case NULL:
                            header('Location: espera.php');
                            exit();
                        case 4:
                            header('Location: home.php');
                            exit();
                        case 5:
                            header('Location: homePro.php');
                            exit();
                        case 6:
                            header('Location: homeBasic.php');
                            exit();
                    }
                } else {
                    // Contraseña incorrecta
                    $error = 'Contraseña incorrecta.';
                }
            } else {
                // Intentar autenticar en la tabla PERSONAL
                $stmt = $pdo->prepare('SELECT idPersonal, contrasenya FROM PERSONAL WHERE nombrePersonal = :nombreUsuario');
                $stmt->execute(['nombreUsuario' => $nombre]);
                $personal = $stmt->fetch();

                if ($personal) {
                    // Verificar la contraseña usando password_verify
                    if (password_verify($contrasenya, $personal['contrasenya'])) {
                        // Autenticación exitosa
                        $_SESSION['personal_id'] = $personal['idPersonal'];
                        header('Location: homeAdmin.php');
                        exit();
                    } else {
                        // Contraseña incorrecta
                        $error = 'Contraseña incorrecta.';
                    }
                } else {
                    // Usuario no encontrado
                    $error = 'Nombre de usuario no registrado.';
                }
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