<?php
// saasAdmin.php
session_start();
require 'config.php';

// Obtener información del empleado desde la tabla PERSONAL
try {
    $stmt = $pdo->prepare('
        SELECT nombre, apellido 
        FROM PERSONA 
        WHERE idPersona = :idPersonal
    ');
    $stmt->execute(['idPersonal' => $_SESSION['personal_id']]);
    $empleado = $stmt->fetch();

    if (!$empleado) {
        echo "Empleado no encontrado.";
        exit();
    }
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
            // Crear una nueva entrada en DATA_BASE
            $pdo->exec("INSERT INTO DATA_BASE() VALUES()"); // Solo crea un idDataBase
            $idDataBase = $pdo->lastInsertId();

            // Insertar configuración en DB_CONFIG
            $stmt = $pdo->prepare("INSERT INTO DB_CONFIG (nombreDB, motor, usuarios, almacenamiento, cpu, puerto, direccionIP, idDataBase) 
                                   VALUES (:nombreDB, :motor, :usuarios, :almacenamiento, :cpu, :puerto, :direccionIP, :idDataBase)");
            $stmt->execute([
                'nombreDB' => $nombreDB,
                'motor' => $motor,
                'usuarios' => $usuarios,
                'almacenamiento' => $almacenamiento,
                'cpu' => $cpu,
                'puerto' => $puerto,
                'direccionIP' => $direccionIP,
                'idDataBase' => $idDataBase
            ]);

            $success = "Base de datos creada exitosamente.";
        } catch (PDOException $e) {
            $error = "Error al crear la base de datos: " . $e->getMessage();
        }
    }
}

// Eliminar Base de Datos
if ($action === 'eliminarDB' && isset($_GET['idDataBase'])) {
    $idDB = intval($_GET['idDataBase']);
    try {
        // Primero eliminar DB_CONFIG asociada
        $stmt = $pdo->prepare("DELETE FROM DB_CONFIG WHERE idDataBase = :idDataBase");
        $stmt->execute(['idDataBase' => $idDB]);

        // Luego eliminar la entrada de DATA_BASE
        $stmt = $pdo->prepare("DELETE FROM DATA_BASE WHERE idDataBase = :idDataBase");
        $stmt->execute(['idDataBase' => $idDB]);

        $success = "Base de datos eliminada exitosamente.";
    } catch (PDOException $e) {
        $error = "Error al eliminar la base de datos: " . $e->getMessage();
    }
}

// Crear Aplicación (Servicio de tipo 'Aplicacion')
if ($action === 'crearApp' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $appName = trim($_POST['appName']);
    $idEtapa = intval($_POST['idEtapa']);
    $descripcion = trim($_POST['descripcion']);

    if (empty($appName) || $idEtapa <= 0) {
        $error = "El nombre de la aplicación y la etapa son obligatorios.";
    } else {
        try {
            // Insertar en SERVICIO tipoServicio='Aplicacion'
            $stmt = $pdo->prepare("INSERT INTO SERVICIO (tipoServicio, descripcion, idEtapa) 
                                   VALUES ('Aplicacion', :descripcion, :idEtapa)");
            $stmt->execute([
                'descripcion' => $descripcion,
                'idEtapa' => $idEtapa
            ]);

            $success = "Aplicación creada exitosamente.";
        } catch (PDOException $e) {
            $error = "Error al crear la aplicación: " . $e->getMessage();
        }
    }
}

// Eliminar Aplicación
if ($action === 'eliminarApp' && isset($_GET['idServicio'])) {
    $idServicio = intval($_GET['idServicio']);
    try {
        $stmt = $pdo->prepare("DELETE FROM SERVICIO WHERE idServicio = :idServicio AND tipoServicio='Aplicacion'");
        $stmt->execute(['idServicio' => $idServicio]);

        $success = "Aplicación eliminada exitosamente.";
    } catch (PDOException $e) {
        $error = "Error al eliminar la aplicación: " . $e->getMessage();
    }
}

// Obtener lista de cloud storage
$dbList = [];
try {
    $stmt = $pdo->query("
        SELECT CS_CONFIG.idCloudStorage, CS_CONFIG.nombreCS, CS_CONFIG.almacenamiento
        FROM CS_CONFIG
        JOIN CLOUD_STORAGE ON CS_CONFIG.idCloudStorage = CLOUD_STORAGE.idCloudStorage
        ORDER BY CS_CONFIG.nombreCS ASC
    ");
    $dbList = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error al obtener la lista de bases de datos: " . $e->getMessage();
}

// Obtener lista de video conference
$appList = [];
try {
    $stmt = $pdo->query("
        SELECT VC_CONFIG.idVideoConference, VC_CONFIG.nombreVC, VC_CONFIG.calidad, VC_CONFIG.anchoBanda, VC_CONFIG.maxParticipantes, VC_CONFIG.idioma
        FROM VC_CONFIG
        JOIN VIDEO_CONFERENCE ON VC_CONFIG.idVideoConference = VIDEO_CONFERENCE.idVideoConference
        ORDER BY VC_CONFIG.nombreVC ASC
    ");
    $appList = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error al obtener la lista de aplicaciones: " . $e->getMessage();
}

// Obtener etapas para el dropdown en aplicaciones
$etapas = [];
try {
    $stmt = $pdo->query("SELECT idEtapa, nombreEtapa FROM ETAPA ORDER BY idEtapa ASC");
    $etapas = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error al obtener etapas: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración SAAS - TotCloud</title>
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
        <h2>Administración de SAAS</h2>
        <p>Aquí el personal de TotCloud puede gestionar la configuración, etapas y acceso a los servicios SAAS ofrecidos (Cloud Storage y Video Conference).</p>

        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="sections">
            <!-- Sección para manejo de Base de Datos -->
            <div class="section-card">
                <h3><i class="fas fa-database"></i> Configuración de Cloud Storage</h3>
                <form action="saasAdmin.php?action=crearCS" method="POST">
                    <label for="nombreCS">Nombre de la Cloud Storage:</label>
                    <input type="text" id="nombreCS" name="nombreCS" placeholder="Nombre" required>

                    <label for="almacenamiento">Almacenamiento (MB):</label>
                    <input type="number" id="almacenamiento" name="almacenamiento" min="1" required>

                    <input type="submit" value="Crear Cloud Storage">
                </form>

                <div class="service-list">
                    <h4>Cloud Storage Configuradas</h4>
                    <table>
                        <tr>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                        <?php if (!empty($dbList)): ?>
                            <?php foreach ($dbList as $dbItem): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($dbItem['nombreCS']); ?></td>
                                    <td class="actions">
                                        <a href="saasAdmin.php?action=editarCS&idCloudStorage=<?php echo (int)$dbItem['idCloudStorage']; ?>">Editar</a>
                                        <a href="saasAdmin.php?action=eliminarCS&idCloudStorage=<?php echo (int)$dbItem['idCloudStorage']; ?>">Eliminar</a>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8">No hay bases de datos configuradas.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Sección para manejo de Aplicaciones -->
            <div class="section-card">
                <h3><i class="fas fa-cogs"></i> Configuración de Video Conference</h3>
                <form action="saasAdmin.php?action=crearVC" method="POST">
                    <label for="nombreVC">Nombre de la Video Conference:</label>
                    <input type="text" id="nombreVC" name="nombreVC" placeholder="Nombre" required>

                    <label for="calidad">Calidad:</label>
                    <input type="text" id="calidad" name="calidad" placeholder="HD" required>

                    <label for="anchoBanda">Ancho de Banda:</label>
                    <input type="number" id="anchoBanda" name="anchoBanda" min="1" required>

                    <label for="maxParticipantes">numero maximo de Participantes:</label>
                    <input type="number" id="maxParticipantes" name="maxParticipantes" min="1" required>

                    <label for="cpu">CPU (número de cores):</label>
                    <input type="number" id="cpu" name="cpu" min="1" required>

                    <label for="puerto">Puerto:</label>
                    <input type="number" id="puerto" name="puerto" min="1" required>

                    <label for="direccionIP">Dirección IP:</label>
                    <input type="text" id="direccionIP" name="direccionIP" placeholder="192.168.1.100" required>

                    <input type="submit" value="Crear Aplicación">
                </form>

                <div class="service-list">
                    <h4>Video Conference Disponibles</h4>
                    <table>
                        <tr>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                        <?php if (!empty($appList)): ?>
                            <?php foreach ($appList as $appItem): ?>
                                <tr>
                                    <td><?php echo (int)$appItem['nombreVC']; ?></td>
                                    <td class="actions">
                                        <a href="saasAdmin.php?action=editarVC&idVideoConference=<?php echo (int)$appItem['idVideoConference']; ?>">Editar</a>
                                        <a href="saasAdmin.php?action=eliminarVC&idVideoConference=<?php echo (int)$appItem['idVideoConference']; ?>">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4">No hay aplicaciones registradas.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>