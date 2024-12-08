<?php
// homeBasic.php
session_start();
require 'config.php';

// Obtener información del usuario desde la tabla PERSONA
try {
    $stmt = $pdo->prepare('
        SELECT nombre, apellido 
        FROM PERSONA 
        WHERE idPersona = :idUsuario
    ');
    $stmt->execute(['idUsuario' => $_SESSION['usuario_id']]);
    $cliente = $stmt->fetch();

    if (!$cliente) {
        echo "Usuario no encontrado.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error al obtener información del usuario: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - TotCloud</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <!-- Font Awesome para Iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset de estilos básicos */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Estilos generales del cuerpo */
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #333;
            position: relative;
        }

        /* Estilos para el enlace de cierre de sesión */
        .logout {
            position: absolute;
            top: 20px;
            right: 20px;
            text-decoration: none;
            color: #ff4d4f;
            font-weight: bold;
            font-size: 16px;
            transition: color 0.3s;
        }

        .logout:hover {
            color: #d9363e;
        }

        /* Contenedor principal */
        .container {
            background-color: #ffffff;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 700px;
            width: 90%;
            transition: transform 0.3s;
        }

        /* Estilo del título de bienvenida */
        .container h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 32px;
            margin-bottom: 10px;
            color: #2d3748;
        }

        /* Estilo del subtítulo */
        .container p {
            font-size: 18px;
            margin-bottom: 40px;
            color: #4a5568;
        }

        /* Contenedor de opciones */
        .options {
            display: flex;
            flex-direction: column;
            gap: 25px;
            align-items: center;
        }

        /* Estilos para los botones de opciones */
        .options a {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 30px;
            background-color: #3182ce;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 20px;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.2s;
            width: 100%;
            max-width: 350px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .options a:hover {
            background-color: #2b6cb0;
            transform: translateY(-3px);
        }

        /* Iconos en las opciones */
        .options a .icon {
            margin-right: 15px;
            font-size: 24px;
        }

        /* Media Queries para Responsividad */
        @media (min-width: 600px) {
            .options {
                flex-direction: row;
                justify-content: center;
            }

            .options a {
                width: auto;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout">Cerrar Sesión</a>
    <div class="container">
        <h2>Bienvenido, <?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']); ?>!</h2>
        <p>Selecciona una opción para continuar:</p>
        <div class="options">
            <a href="paasBasic.php">
                <i class="fas fa-cloud icon"></i> PAAS
            </a>
            <a href="saasBasic.php">
                <i class="fas fa-server icon"></i> SAAS
            </a>
        </div>
    </div>
</body>
</html>