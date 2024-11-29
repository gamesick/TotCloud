<?php
// homeadmin.php
session_start();
require 'config.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

// Verificar si el usuario es administrador
try {
    // Obtener el grupo al que pertenece el usuario
    $stmt = $pdo->prepare("
        SELECT G.nombreGrupo 
        FROM USUARIO U
        JOIN PERSONA P ON U.idUsuario = P.idPersona
        JOIN GRUPO G ON P.idGrupo = G.idGrupo
        WHERE U.idUsuario = :idUsuario
    ");
    $stmt->execute(['idUsuario' => $_SESSION['usuario_id']]);
    $grupo = $stmt->fetch();

    // Asumimos que el nombre del grupo de administradores es 'Administradores'
    if (!$grupo || $grupo['nombreGrupo'] !== 'Administradores') {
        echo "Acceso denegado. No tienes permisos para acceder a esta página.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error al verificar permisos: " . $e->getMessage();
    exit();
}

// Obtener información del usuario
try {
    $stmt = $pdo->prepare('
        SELECT U.nombreUsuario, P.nombre, P.apellido 
        FROM USUARIO U 
        JOIN PERSONA P ON U.idUsuario = P.idPersona 
        WHERE U.idUsuario = :id
    ');
    $stmt->execute(['id' => $_SESSION['usuario_id']]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        echo "Usuario no encontrado.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error al obtener información del usuario: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inicio Administrativo - TotCloud</title>
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
        .options, .admin-options {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .options a, .admin-options a {
            display: block;
            padding: 20px 40px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 18px;
            transition: background-color 0.3s;
        }
        .options a:hover, .admin-options a:hover {
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
        .admin-options {
            margin-top: 20px;
        }
        .administrar {
            background-color: #1e90ff;
        }
        .administrar:hover {
            background-color: #1c86ee;
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout">Cerrar Sesión</a>
    <div class="welcome">
        <h2>Bienvenido, <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>!</h2>
        <p>Selecciona una opción:</p>
    </div>
    <div class="options">
        <a href="paas.php">PAAS</a>
        <a href="saas.php">SAAS</a>
    </div>
    <div class="admin-options">
        <a href="AdministrarGrupos.php" class="administrar">Administrar</a>
    </div>
</body>
</html>