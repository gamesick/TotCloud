<?php
// saasAdmin.php
session_start();
require 'config.php';

// Obtener información del empleado desde la tabla PERSONAL
try {
    $stmt = $pdo->prepare('
        SELECT idPersona, nombre, apellido 
        FROM PERSONA 
        WHERE idPersona = :idPersonal
    ');
    $stmt->execute(['idPersonal' => $_SESSION['personal_id']]);
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

// Crear Cloud Storage
if ($action === 'crearCS' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreCS = trim($_POST['nombreCS']);
    $almacenamiento = intval($_POST['almacenamiento']);
    $limiteSubida = intval($_POST['limiteSubida']);
    $velocidad = intval($_POST['velocidad']);
    $latencia = intval($_POST['latencia']);

    if (empty($nombreCS) || $almacenamiento <= 0 || $limiteSubida <= 0 || $velocidad <= 0 || $latencia <= 0) {
        $error = "Todos los campos de la Cloud Storage son obligatorios y deben ser válidos.";
    } else {
        try {
            // Crear una nueva entrada en CLOUD_STORAGE
            $stmt = $pdo->prepare("INSERT INTO CLOUD_STORAGE(nombreCS, limiteSubida, velocidad, latencia) 
                                    VALUES(:nombreCS, :limiteSubida, :velocidad, :latencia)");
            $stmt->execute([
                'nombreCS' => $nombreCS,
                'limiteSubida' => $limiteSubida,
                'velocidad' => $velocidad,
                'latencia' => $latencia
            ]);                
            $idCloudStorage = $pdo->lastInsertId();

            // Insertar configuración en CS_CONFIG
            $stmt = $pdo->prepare("INSERT INTO CS_CONFIG (nombreCS, almacenamiento, idCloudStorage, idPersona) 
                                    VALUES (:nombreCS, :almacenamiento, :idCloudStorage, :idPersona)");
            $stmt->execute([
                'nombreCS' => $nombreCS,
                'almacenamiento' => $almacenamiento,
                'idCloudStorage' => $idCloudStorage,
                'idPersona' => $idPersona
            ]);

            $success = "Cloud Storage creada exitosamente.";
        } catch (PDOException $e) {
            $error = "Error al crear la cloud storage: " . $e->getMessage();
        }
    }
}

// Eliminar Cloud Storage
if ($action === 'eliminarCS' && isset($_GET['idCloudStorage'])) {
    $idDB = intval($_GET['idCloudStorage']);
    try {
        // Primero eliminar CS_CONFIG asociada
        $stmt = $pdo->prepare("DELETE FROM CS_CONFIG WHERE idCloudStorage = :idCloudStorage");
        $stmt->execute(['idCloudStorage' => $idDB]);

        // Luego eliminar la entrada de CLOUD_STORAGE
        $stmt = $pdo->prepare("DELETE FROM CLOUD_STORAGE WHERE idCloudStorage = :idCloudStorage");
        $stmt->execute(['idCloudStorage' => $idDB]);

        $success = "Cloud Storage eliminada exitosamente.";
    } catch (PDOException $e) {
        $error = "Error al eliminar la cloud storage: " . $e->getMessage();
    }
}

// Crear Video Conference
if ($action === 'crearVC' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreVC = trim($_POST['nombreVC']);
    $calidad = trim($_POST['calidad']);
    $anchoBanda = intval($_POST['anchoBanda']);
    $maxParticipantes = intval($_POST['maxParticipantes']);
    $idioma = trim($_POST['idioma']);

    if (empty($nombreVC) || empty($calidad) || $anchoBanda <= 0 || $maxParticipantes <= 0 || empty($idioma)) {
        $error = "Todos los campos de la Video Conference son obligatorios y deben ser válidos.";
    } else {
        try {
            // Insertar configuración en VC_CONFIG
            $stmt = $pdo->prepare("INSERT INTO VC_CONFIG (nombreVC, calidad, anchoBanda, maxParticipantes, idioma, idVideoConference, idPersona) 
                                   VALUES (:nombreVC, :calidad, :anchoBanda, :maxParticipantes, :idioma, :idVideoConference, :idPersona)");
            $stmt->execute([
                'nombreVC' => $nombreVC,
                'calidad' => $calidad,
                'anchoBanda' => $anchoBanda,
                'maxParticipantes' => $maxParticipantes,
                'idioma' => $idioma,
                'idVideoConference' => 8,
                'idPersona' => $idPersona
            ]);

            $success = "Video Conference creada exitosamente.";
        } catch (PDOException $e) {
            $error = "Error al crear la video conference: " . $e->getMessage();
        }
    }
}

// Eliminar Video Conference
if ($action === 'eliminarVC' && isset($_GET['idVCConfig'])) {
    $idVC = intval($_GET['idVCConfig']);
    try {
        // Primero eliminar VC_CONFIG asociada
        $stmt = $pdo->prepare("DELETE FROM VC_CONFIG WHERE idVCConfig = :idVCConfig");
        $stmt->execute(['idVCConfig' => $idVC]);

        $success = "Video Conference eliminada exitosamente.";
    } catch (PDOException $e) {
        $error = "Error al eliminar la video conference: " . $e->getMessage();
    }
}

// Obtener lista de cloud storage
$csList = [];
try {
    $stmt = $pdo->query("
        SELECT CS_CONFIG.idCloudStorage, CS_CONFIG.nombreCS, CS_CONFIG.almacenamiento
        FROM CS_CONFIG
        JOIN CLOUD_STORAGE ON CS_CONFIG.idCloudStorage = CLOUD_STORAGE.idCloudStorage
        ORDER BY CS_CONFIG.nombreCS ASC
    ");
    $csList = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error al obtener la lista de bases de datos: " . $e->getMessage();
}

// Obtener lista de video conference
$vcList = [];
try {
    $stmt = $pdo->query("
        SELECT VC_CONFIG.idVCConfig, VC_CONFIG.idVideoConference, VC_CONFIG.nombreVC, VC_CONFIG.calidad, VC_CONFIG.anchoBanda, VC_CONFIG.maxParticipantes, VC_CONFIG.idioma
        FROM VC_CONFIG
        JOIN VIDEO_CONFERENCE ON VC_CONFIG.idVideoConference = VIDEO_CONFERENCE.idVideoConference
        ORDER BY VC_CONFIG.nombreVC ASC
    ");
    $vcList = $stmt->fetchAll();
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
        <a href="homeadmin.php" class="back-link">← Volver al Inicio Administrativo</a>
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

                    <label for="limiteSubida">Límite de Subida (MB):</label>
                    <input type="number" id="limiteSubida" name="limiteSubida" min="1" required>

                    <label for="velocidad">Velocidad (MB/s):</label>
                    <input type="number" id="velocidad" name="velocidad" min="1" required>

                    <label for="latencia">Latencia (ms):</label>
                    <input type="number" id="latencia" name="latencia" min="1" required>

                    <input type="submit" value="Crear Cloud Storage">
                </form>

                <div class="service-list">
                    <h4>Cloud Storage Configuradas</h4>
                    <table>
                        <tr>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                        <?php if (!empty($csList)): ?>
                            <?php foreach ($csList as $csItem): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($csItem['nombreCS']); ?></td>
                                    <td class="actions">
                                        <a href="saasAdmin.php?action=editarCS&idCloudStorage=<?php echo (int)$csItem['idCloudStorage']; ?>">Editar</a>
                                        <a href="saasAdmin.php?action=eliminarCS&idCloudStorage=<?php echo (int)$csItem['idCloudStorage']; ?>">Eliminar</a>
                                        
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
                    <select id="calidad" name="calidad">
                        <option value="">Selecciona la Calidad</option>
                        <option value="240p">240p</option>
                        <option value="480p">480p</option>
                        <option value="720p">720p</option>
                        <option value="1080p">1080p</option>
                        <option value="4k">4k</option>
                    </select>

                    <label for="anchoBanda">Ancho de Banda:</label>
                    <input type="number" id="anchoBanda" name="anchoBanda" min="1" required>

                    <label for="maxParticipantes">Número máximo de Participantes:</label>
                    <input type="number" id="maxParticipantes" name="maxParticipantes" min="1" required>
                    
                    <label for="idioma">Idioma:</label>
                    <select id="idioma" name="idioma">
                        <option value="">Selecciona un Idioma</option>
                        <option value="Español">Español</option>
                        <option value="English">English</option>
                        <option value="Deutsch">Deutsch</option>
                        <option value="Italian">Italian</option>
                        <option value="French">French</option>
                    </select>

                    <input type="submit" value="Crear Video Conference">
                </form>

                <div class="service-list">
                    <h4>Video Conference Configuradas</h4>
                    <table>
                        <tr>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                        <?php if (!empty($vcList)): ?>
                            <?php foreach ($vcList as $vcItem): ?>
                                <tr>
                                <td><?php echo htmlspecialchars($vcItem['nombreVC']); ?></td>
                                    <td class="actions">
                                        <a href="saasAdmin.php?action=editarVC&idVCConfig=<?php echo (int)$vcItem['idVCConfig']; ?>">Editar</a>
                                        <a href="saasAdmin.php?action=eliminarVC&idVCConfig=<?php echo (int)$vcItem['idVCConfig']; ?>">Eliminar</a>
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