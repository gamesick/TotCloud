<?php
// paasPro.php
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

// Manejo de acciones: crear DB, eliminar DB y editar DB
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
                'idDataBase' => 4,
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

// Editar Base de Datos - Mostrar datos (GET)
if ($action === 'editarDB' && isset($_GET['idDBConfig']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $idDBConfig = intval($_GET['idDBConfig']);
    try {
        // Obtener datos actuales de la DB_CONFIG
        $stmt = $pdo->prepare("SELECT * FROM DB_CONFIG WHERE idDBConfig = :idDBConfig");
        $stmt->execute(['idDBConfig' => $idDBConfig]);
        $dbToEdit = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Error al obtener la base de datos: " . $e->getMessage();
    }
}

// Editar Base de Datos (UPDATE)
if ($action === 'editarDB' && isset($_GET['idDBConfig']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $idDBConfig = intval($_GET['idDBConfig']);
    $nombreDB = trim($_POST['nombreDB']);
    $motor = trim($_POST['motor']);
    $usuarios = intval($_POST['usuarios']);
    $almacenamiento = intval($_POST['almacenamiento']);
    $cpu = intval($_POST['cpu']);
    $puerto = intval($_POST['puerto']);
    $direccionIP = trim($_POST['direccionIP']);

    if (empty($nombreDB) || empty($motor) || $usuarios <= 0 || $almacenamiento <= 0 || $cpu <= 0 || $puerto <= 0 || empty($direccionIP)) {
        $error = "Todos los campos son obligatorios y deben ser válidos.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE DB_CONFIG SET nombreDB=:nombreDB, motor=:motor, usuarios=:usuarios, almacenamiento=:almacenamiento, cpu=:cpu, puerto=:puerto, direccionIP=:direccionIP WHERE idDBConfig=:idDBConfig");
            $stmt->execute([
                'nombreDB' => $nombreDB,
                'motor' => $motor,
                'usuarios' => $usuarios,
                'almacenamiento' => $almacenamiento,
                'cpu' => $cpu,
                'puerto' => $puerto,
                'direccionIP' => $direccionIP,
                'idDBConfig' => $idDBConfig
            ]);

            $success = "Base de datos actualizada exitosamente.";
            $action = '';
            $dbToEdit = null; // Volver al modo normal (sin edición)
        } catch (PDOException $e) {
            $error = "Error al actualizar la base de datos: " . $e->getMessage();
        }
    }
}

// Obtener lista de bases de datos
$dbList = [];
try {
    // Verificar si $idPersona tiene un valor válido
    if (isset($idPersona)) {
        $stmt = $pdo->prepare("
            SELECT DB_CONFIG.idDBConfig, DB_CONFIG.idDataBase, DB_CONFIG.nombreDB, DB_CONFIG.motor, DB_CONFIG.idPersona 
            FROM DB_CONFIG
            JOIN DATA_BASE ON DB_CONFIG.idDataBase = DATA_BASE.idDataBase
            JOIN USUARIO ON  DB_CONFIG.idPersona = USUARIO.idUsuario WHERE DB_CONFIG.idPersona = :idPersona
            ORDER BY DB_CONFIG.nombreDB ASC
        ");
        $stmt->execute(['idPersona' => $idPersona]);
        $dbList = $stmt->fetchAll();
    } else {
        echo "El parámetro idPersona no está definido.";
    }
} catch (PDOException $e) {
    $error = "Error al obtener la lista de bases de datos: " . $e->getMessage();
}

// Obtener lista de bases de datos
$etapa = [];
$idEtapaPermitida = false;

try {
    if (isset($idPersona)) {
        $stmt = $pdo->prepare("
            SELECT SERVICIO.idEtapa
            FROM SERVICIO WHERE SERVICIO.tipoServicio = 'Data Base'
        ");
        $stmt->execute();
        $etapa = $stmt->fetchAll();

        // Verificar si existe una etapa con valor 4 en los registros
        foreach ($etapa as $etapas) {
            if ($etapas['idEtapa'] == 5) {
                $idEtapaPermitida = true;
                break;
            }
        }
    } else {
        echo "El parámetro idPersona no está definido.";
    }
} catch (PDOException $e) {
    $error = "Error al obtener la lista de bases de datos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración PAAS - TotCloud</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos aplicados anteriormente */
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
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
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
    </style>
</head>
<body>
    <a href="logout.php" class="logout">Cerrar Sesión</a>
    <div class="container">
        <a href="homePro.php" class="back-link">← Volver al Inicio</a>
        <h2>Servicios PAAS</h2>
        <p>Aquí el personal de TotCloud puede gestionar la configuración, etapas y acceso a los servicios PAAS ofrecidos (Bases de Datos).</p>

        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($idEtapaPermitida): ?>
        <div class="sections">
            <!-- Sección para manejo de Base de Datos -->
            <div class="section-card">
                <?php if ($action === 'editarDB' && isset($_GET['idDBConfig']) && !empty($dbToEdit) && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
                    <h3><i class="fas fa-edit"></i> Editar Configuración</h3>
                    <form action="paasPro.php?action=editarDB&idDBConfig=<?php echo (int)$_GET['idDBConfig']; ?>" method="POST">
                        <label for="nombreDB">Nombre de la Base de Datos:</label>
                        <input type="text" id="nombreDB" name="nombreDB" value="<?php echo htmlspecialchars($dbToEdit['nombreDB']); ?>" required>

                        <label for="motor">Motor:</label>
                        <select id="motor" name="motor" required>
                            <option value="">Selecciona el Motor</option>
                            <option value="MySQL" <?php if($dbToEdit['motor']=='MySQL') echo 'selected'; ?>>MySQL</option>
                            <option value="PostgreSQL" <?php if($dbToEdit['motor']=='PostgreSQL') echo 'selected'; ?>>PostgreSQL</option>
                            <option value="MariaDB" <?php if($dbToEdit['motor']=='MariaDB') echo 'selected'; ?>>MariaDB</option>
                            <option value="Oracle" <?php if($dbToEdit['motor']=='Oracle') echo 'selected'; ?>>Oracle</option>
                        </select>

                        <label for="usuarios">Número de usuarios permitidos:</label>
                        <input type="number" id="usuarios" name="usuarios" min="1" max="100" value="<?php echo (int)$dbToEdit['usuarios']; ?>" required>

                        <label for="almacenamiento">Almacenamiento (MB):</label>
                        <input type="number" id="almacenamiento" name="almacenamiento" min="1" max="5000" value="<?php echo (int)$dbToEdit['almacenamiento']; ?>" required>

                        <label for="cpu">CPU (número de cores):</label>
                        <input type="number" id="cpu" name="cpu" min="1" max="8" value="<?php echo (int)$dbToEdit['cpu']; ?>" required>

                        <label for="puerto">Puerto:</label>
                        <input type="number" id="puerto" name="puerto" min="0" max="65535" value="<?php echo (int)$dbToEdit['puerto']; ?>" required>

                        <label for="direccionIP">Dirección IP:</label>
                        <input type="text" id="direccionIP" name="direccionIP" value="<?php echo htmlspecialchars($dbToEdit['direccionIP']); ?>" required>

                        <input type="submit" value="Guardar Cambios">
                    </form>
                <?php else: ?>
                    <h3><i class="fas fa-database"></i> Configuración de Bases de Datos</h3>
                    <form action="paasPro.php?action=crearDB" method="POST">
                        <label for="nombreDB">Nombre de la Base de Datos:</label>
                        <input type="text" id="nombreDB" name="nombreDB" placeholder="Nombre" required>

                        <label for="motor">Motor:</label>
                        <select id="motor" name="motor" required>
                            <option value="">Selecciona el Motor</option>
                            <option value="MySQL">MySQL</option>
                            <option value="PostgreSQL">PostgreSQL</option>
                            <option value="MariaDB">MariaDB</option>
                            <option value="Oracle">Oracle</option>
                        </select>

                        <label for="usuarios">Número de usuarios permitidos:</label>
                        <input type="number" id="usuarios" name="usuarios" min="1" max="100" required>

                        <label for="almacenamiento">Almacenamiento (MB):</label>
                        <input type="number" id="almacenamiento" name="almacenamiento" min="1" max="5000" required>

                        <label for="cpu">CPU (número de cores):</label>
                        <input type="number" id="cpu" name="cpu" min="1" max="8" required>

                        <label for="puerto">Puerto:</label>
                        <input type="number" id="puerto" name="puerto" min="0" max="65535" required>

                        <label for="direccionIP">Dirección IP:</label>
                        <input type="text" id="direccionIP" name="direccionIP" placeholder="192.168.1.100" required>

                        <input type="submit" value="Crear Base de Datos">
                    </form>
                <?php endif; ?>

                <div class="service-list">
                    <h4>Bases de Datos Configuradas</h4>
                    <table>
                        <tr>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                        <?php if (!empty($dbList)): ?>
                            <?php foreach ($dbList as $dbItem): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($dbItem['nombreDB']); ?></td>
                                    <td class="actions">
                                        <a href="paas.php?action=editarDB&idDBConfig=<?php echo (int)$dbItem['idDBConfig']; ?>">Editar</a>
                                        <a href="paas.php?action=eliminarDB&idDBConfig=<?php echo (int)$dbItem['idDBConfig']; ?>">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="2">No hay bases de datos configuradas.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>