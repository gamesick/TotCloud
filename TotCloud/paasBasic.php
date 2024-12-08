<?php
// paasBasic.php
session_start();
require 'config.php';

// Obtener información del empleado desde la tabla PERSONAL incluyendo idPersona
try {
    $stmt = $pdo->prepare('
        SELECT idPersona, nombre, apellido 
        FROM PERSONA 
        WHERE idPersona = :idUsuario
    ');
    $stmt->execute(['idUsuario' => $_SESSION['usuario_id']]);
    $empleado = $stmt->fetch();

    if (!$empleado) {
        echo "Empleado no encontrado.";
        exit();
    }

    $idPersona = $empleado['idPersona'];

} catch (PDOException $e) {
    echo "Error al obtener información del empleado: " . $e->getMessage();
    exit();
}

// Manejo de acciones: crear DB, eliminar DB, crear App, eliminar App
$action = isset($_GET['action']) ? $_GET['action'] : '';
$error = '';
$success = '';

// Crear Base de Datos
if ($action === 'crearDB' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreDB = trim($_POST['nombreDB']);
    $motor = trim($_POST['motor']);
    $usuarios = intval($_POST['usuarios']);
    $almacenamiento = intval($_POST['almacenamiento']);
    $cpu = intval($_POST['cpu']);
    $puerto = intval($_POST['puerto']);
    $direccionIP = trim($_POST['direccionIP']);

    if (empty($nombreDB) || empty($motor) || $usuarios <= 0 || $almacenamiento <= 0 || $cpu <= 0 || $puerto <= 0 || empty($direccionIP)) {
        $error = "Todos los campos de la Base de Datos son obligatorios y deben ser válidos.";
    } else {
        try {
            // Insertar configuración en DB_CONFIG
            $stmt = $pdo->prepare("INSERT INTO DB_CONFIG (nombreDB, motor, usuarios, almacenamiento, cpu, puerto, direccionIP, idDataBase, idPersona) 
                                   VALUES (:nombreDB, :motor, :usuarios, :almacenamiento, :cpu, :puerto, :direccionIP, :idDataBase, :idPersona)");
            $stmt->execute([
                'nombreDB' => $nombreDB,
                'motor' => $motor,
                'usuarios' => $usuarios,
                'almacenamiento' => $almacenamiento,
                'cpu' => $cpu,
                'puerto' => $puerto,
                'direccionIP' => $direccionIP,
                'idDataBase' => 1,
                'idPersona' => $idPersona
            ]);

            $success = "Base de datos creada exitosamente.";
        } catch (PDOException $e) {
            $error = "Error al crear la base de datos: " . $e->getMessage();
        }
    }
}

// Eliminar Base de Datos
if ($action === 'eliminarDB' && isset($_GET['idDBConfig'])) {
    $idDB = intval($_GET['idDBConfig']);
    try {
        // Primero eliminar DB_CONFIG asociada
        $stmt = $pdo->prepare("DELETE FROM DB_CONFIG WHERE idDBConfig = :idDBConfig");
        $stmt->execute(['idDBConfig' => $idDB]);

        $success = "Base de datos eliminada exitosamente.";
    } catch (PDOException $e) {
        $error = "Error al eliminar la base de datos: " . $e->getMessage();
    }
}

// Editar Base de Datos
if ($action === 'editarDB' && isset($_GET['idDataBase'])) {
    $idDB = intval($_GET['idDataBase']);
    try {
        // Primero editar DB_CONFIG asociada
        $stmt = $pdo->prepare("DELETE FROM DB_CONFIG WHERE idDataBase = :idDataBase");
        $stmt->execute(['idDataBase' => $idDB]);

        // Luego editar la entrada de DATA_BASE
        $stmt = $pdo->prepare("DELETE FROM DATA_BASE WHERE idDataBase = :idDataBase");
        $stmt->execute(['idDataBase' => $idDB]);

        $success = "Base de datos editada exitosamente.";
    } catch (PDOException $e) {
        $error = "Error al editar la base de datos: " . $e->getMessage();
    }
}

// Obtener lista de bases de datos
$dbList = [];
try {
    $stmt = $pdo->query("
        SELECT DB_CONFIG.idDBConfig, DB_CONFIG.idDataBase, DB_CONFIG.nombreDB, DB_CONFIG.motor 
        FROM DB_CONFIG
        JOIN DATA_BASE ON DB_CONFIG.idDataBase = DATA_BASE.idDataBase
        ORDER BY DB_CONFIG.nombreDB ASC
    ");
    $dbList = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error al obtener la lista de bases de datos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios PAAS - TotCloud</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            color: #333;
        }

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

        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 1000px;
            width: 90%;
            margin-top: 80px;
        }

        .container h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 28px;
            margin-bottom: 10px;
            color: #2d3748;
        }

        .container p {
            font-size: 16px;
            margin-bottom: 30px;
            color: #4a5568;
        }

        .message {
            margin-bottom: 20px;
            text-align: center;
            font-size: 16px;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }

        .sections {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }

        .section-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
            max-width: 450px;
            text-align: left;
        }

        .section-card h3 {
            font-family: 'Montserrat', sans-serif;
            font-size: 22px;
            color: #2d3748;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .section-card h3 i {
            margin-right: 15px;
            font-size: 24px;
            color: #3182ce;
        }

        .section-card form,
        .section-card .service-list {
            margin-bottom: 20px;
        }

        .section-card label {
            display: block;
            margin-bottom: 10px;
            color: #4a5568;
            font-weight: 500;
        }

        .section-card input[type="text"],
        .section-card select,
        .section-card input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .section-card input[type="submit"] {
            background-color: #3182ce;
            color: #ffffff;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .section-card input[type="submit"]:hover {
            background-color: #2b6cb0;
        }

        .service-list h4 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .service-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .service-list table th,
        .service-list table td {
            border: 1px solid #e2e8f0;
            padding: 10px;
            font-size: 14px;
            text-align: left;
        }

        .service-list table th {
            background-color: #edf2f7;
        }

        .service-list .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #3182ce;
            font-weight: 500;
        }

        .service-list .actions a:hover {
            color: #2b6cb0;
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #3182ce;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #2b6cb0;
            text-decoration: underline;
        }

        @media (min-width: 600px) {
            .sections {
                flex-wrap: nowrap;
                justify-content: space-between;
            }

            .section-card {
                width: 45%;
            }
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout">Cerrar Sesión</a>
    <div class="container">
        <a href="homeBasic.php" class="back-link">← Volver al Inicio</a>
        <h2>Servicios PAAS</h2>
        <p>Aquí el personal de TotCloud puede gestionar la configuración, etapas y acceso a los servicios PAAS ofrecidos (Bases de Datos).</p>

        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="sections">

        </div>
    </div>
</body>
</html>