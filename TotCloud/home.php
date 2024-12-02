<?php
// home.php
session_start();
require 'config.php';

// Obtener información del empleado desde la tabla PERSONAL
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
    echo "Error al obtener información del empleado: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inicio - TotCloud</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 50px;
        }
        .welcome {
            margin-bottom: 30px;
        }
        .options {
            display: flex;
            gap: 20px;
        }
        .options a {
            display: block;
            padding: 20px 40px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 18px;
            transition: background-color 0.3s;
        }
        .options a:hover {
            background-color: #45a049;
        }
        .logout {
            position: absolute;
            top: 20px;
            right: 20px;
            text-decoration: none;
            color: #ff0000;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout">Cerrar Sesión</a>
    <div class="welcome">
    <h2>Bienvenido, <?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']); ?>!</h2>
        <p>Selecciona una opción:</p>
    </div>
    <div class="options">
        <a href="paas.php">PAAS</a>
        <a href="saas.php">SAAS</a>
    </div>
</body>
</html>