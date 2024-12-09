<?php
// saas.php
session_start();
require 'config.php';

// Obtener información del empleado desde la tabla PERSONAL
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

// Crear Cloud Storage
if ($action === 'crearCS' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreCS = trim($_POST['nombreCS']);
    $almacenamiento = intval($_POST['almacenamiento']);
    $idCloudStorage = intval($_POST['idCloudStorage']);

    if (empty($nombreCS) || $almacenamiento <= 0 || $idCloudStorage <= 0) {
        $error = "Todos los campos de la Cloud Storage son obligatorios y deben ser válidos.";
    } else {
        try {
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
if ($action === 'eliminarCS' && isset($_GET['idCSConfig'])) {
    $idDB = intval($_GET['idCSConfig']);
    try {
        // Primero eliminar CS_CONFIG asociada
        $stmt = $pdo->prepare("DELETE FROM CS_CONFIG WHERE idCSConfig = :idCSConfig");
        $stmt->execute(['idCSConfig' => $idDB]);

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

// Editar Cloud Storage (GET)
if ($action === 'editarCS' && isset($_GET['idCSConfig']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $idCS = intval($_GET['idCSConfig']);
    try {
        $stmt = $pdo->prepare("SELECT nombreCS, almacenamiento, idCloudStorage FROM CS_CONFIG WHERE idCSConfig = :idCSConfig");
        $stmt->execute(['idCSConfig' => $idCS]);
        $csToEdit = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Error al obtener la Video Conference: " . $e->getMessage();
    }
}

// Editar Cloud Storage (UPDATE)
if ($action === 'editarCS' && isset($_GET['idCSConfig']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCS = intval($_GET['idCSConfig']);
    $nombreCS = trim($_POST['nombreCS']);
    $almacenamiento = intval($_POST['almacenamiento']);
    $idCloudStorage = intval($_POST['idCloudStorage']);

    if (empty($nombreCS) || $almacenamiento <= 0 || $idCloudStorage <= 0) {
        $error = "Todos los campos son obligatorios y deben ser válidos.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE CS_CONFIG SET nombreCS=:nombreCS, almacenamiento=:almacenamiento, idCloudStorage=:idCloudStorage WHERE idCSConfig=:idCSConfig");
            $stmt->execute([
                'nombreCS' => $nombreCS,
                'almacenamiento' => $almacenamiento,
                'idCloudStorage' => $idCloudStorage,
                'idCSConfig' => $idCS
            ]);

            $success = "Video Conference editada exitosamente.";
            $action = '';
            $csToEdit = null;
        } catch (PDOException $e) {
            $error = "Error al editar la video conference: " . $e->getMessage();
        }
    }
}

// Editar Video Conference (GET)
if ($action === 'editarVC' && isset($_GET['idVCConfig']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $idVC = intval($_GET['idVCConfig']);
    try {
        $stmt = $pdo->prepare("SELECT nombreVC, calidad, anchoBanda, maxParticipantes, idioma FROM VC_CONFIG WHERE idVCConfig = :idVCConfig");
        $stmt->execute(['idVCConfig' => $idVC]);
        $vcToEdit = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Error al obtener la Video Conference: " . $e->getMessage();
    }
}

// Editar Video Conference (UPDATE)
if ($action === 'editarVC' && isset($_GET['idVCConfig']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $idVC = intval($_GET['idVCConfig']);
    $nombreVC = trim($_POST['nombreVC']);
    $calidad = trim($_POST['calidad']);
    $anchoBanda = intval($_POST['anchoBanda']);
    $maxParticipantes = intval($_POST['maxParticipantes']);
    $idioma = trim($_POST['idioma']);

    if (empty($nombreVC) || empty($calidad) || $anchoBanda <= 0 || $maxParticipantes <= 0 || empty($idioma)) {
        $error = "Todos los campos son obligatorios y deben ser válidos.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE VC_CONFIG SET nombreVC=:nombreVC, calidad=:calidad, anchoBanda=:anchoBanda, maxParticipantes=:maxParticipantes, idioma=:idioma WHERE idVCConfig=:idVCConfig");
            $stmt->execute([
                'nombreVC' => $nombreVC,
                'calidad' => $calidad,
                'anchoBanda' => $anchoBanda,
                'maxParticipantes' => $maxParticipantes,
                'idioma' => $idioma,
                'idVCConfig' => $idVC
            ]);

            $success = "Video Conference editada exitosamente.";
            $action = '';
            $vcToEdit = null;
        } catch (PDOException $e) {
            $error = "Error al editar la video conference: " . $e->getMessage();
        }
    }
}

// Obtener lista de cloud storage
$csList = [];
try {
    // Verificar si $idPersona tiene un valor válido
    if (isset($idPersona)) {
        $stmt = $pdo->prepare("
            SELECT CS_CONFIG.idCSConfig, CS_CONFIG.idCloudStorage, CS_CONFIG.nombreCS, CS_CONFIG.almacenamiento, CS_CONFIG.idPersona
            FROM CS_CONFIG
            JOIN CLOUD_STORAGE ON CS_CONFIG.idCloudStorage = CLOUD_STORAGE.idCloudStorage
             WHERE CS_CONFIG.idPersona = :idPersona
            ORDER BY CS_CONFIG.nombreCS ASC
        ");
        $stmt->execute(['idPersona' => $idPersona]);
        $csList = $stmt->fetchAll();
    } else {
        echo "El parámetro idPersona no está definido.";
    }
} catch (PDOException $e) {
    $error = "Error al obtener la lista de bases de datos: " . $e->getMessage();
}

// Obtener lista de video conference
$vcList = [];
try {
    // Verificar si $idPersona tiene un valor válido
    if (isset($idPersona)) {
        $stmt = $pdo->prepare("
            SELECT VC_CONFIG.idVCConfig, VC_CONFIG.idVideoConference, VC_CONFIG.nombreVC, VC_CONFIG.calidad, 
            VC_CONFIG.anchoBanda, VC_CONFIG.maxParticipantes, VC_CONFIG.idioma, VC_CONFIG.idPersona
            FROM VC_CONFIG
            JOIN USUARIO ON VC_CONFIG.idPersona = USUARIO.idUsuario
            JOIN VIDEO_CONFERENCE ON VC_CONFIG.idVideoConference = VIDEO_CONFERENCE.idVideoConference
            WHERE VC_CONFIG.idPersona = :idPersona
            ORDER BY VC_CONFIG.nombreVC ASC
        ");
        $stmt->execute(['idPersona' => $idPersona]);
        $vcList = $stmt->fetchAll();

        // Puedes hacer algo con $vcList aquí

    } else {
        echo "El parámetro idPersona no está definido.";
    }
} catch (PDOException $e) {
    $error = "Error al obtener la lista de aplicaciones: " . $e->getMessage();
}

// Obtener etapas para el dropdown en aplicaciones
$cloudstorage = [];
try {
    $stmt = $pdo->query("SELECT idCloudStorage, nombreCS FROM CLOUD_STORAGE ORDER BY idCloudStorage ASC");
    $cloudstorage = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error al obtener etapas: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios SAAS - TotCloud</title>
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
        <a href="home.php" class="back-link">← Volver al Inicio</a>
        <h2>Servicios SAAS</h2>
        <p>Aquí el personal de TotCloud puede gestionar la configuración, etapas y acceso a los servicios SAAS ofrecidos (Cloud Storage y Video Conference).</p>

        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="sections">

            <!-- Sección para manejo de Cloud Storage -->
            <div class="section-card">
                <?php if ($action === 'editarCS' && isset($_GET['idCSConfig']) && !empty($csToEdit) && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
                        <h3><i class="fas fa-edit"></i> Editar Configuración Video Conference</h3>
                        <form action="saas.php?action=editarCS&idCSConfig=<?php echo (int)$_GET['idCSConfig']; ?>" method="POST">
                            <label>Nombre de la Cloud Storage:</label>
                            <input type="text" name="nombreCS" value="<?php echo htmlspecialchars($csToEdit['nombreCS']); ?>" required>

                            <label>Almacenamiento (MB):</label>
                            <input type="number" name="almacenamiento" min="1" value="<?php echo (int)$csToEdit['almacenamiento']; ?>" required>
                            
                            <label for="idCloudStorage">Cloud Storage:</label>
                            <select id="idCloudStorage" name="idCloudStorage" required>
                                <option value="">Selecciona una Cloud Storage</option>
                                <?php foreach ($cloudstorage as $et): ?>
                                    <option value="<?php echo (int)$et['idCloudStorage']; ?>">
                                        <?php echo htmlspecialchars($et['nombreCS']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <input type="submit" value="Guardar Cambios">
                        </form>
                    <?php else: ?>
                        <h3><i class="fas fa-database"></i> Configuración de Cloud Storage</h3>
                        <form action="saas.php?action=crearCS" method="POST">
                            <label for="nombreCS">Nombre de la Cloud Storage:</label>
                            <input type="text" id="nombreCS" name="nombreCS" placeholder="Nombre" required>

                            <label for="almacenamiento">Almacenamiento (MB):</label>
                            <input type="number" id="almacenamiento" name="almacenamiento" min="1" required>

                            <label for="idCloudStorage">Cloud Storage:</label>
                            <select id="idCloudStorage" name="idCloudStorage" required>
                                <option value="">Selecciona una Cloud Storage</option>
                                <?php foreach ($cloudstorage as $et): ?>
                                    <option value="<?php echo (int)$et['idCloudStorage']; ?>">
                                        <?php echo htmlspecialchars($et['nombreCS']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <input type="submit" value="Crear Cloud Storage">
                        </form>
                        <?php endif; ?>

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
                                        <a href="saas.php?action=editarCS&idCSConfig=<?php echo (int)$csItem['idCSConfig']; ?>">Editar</a>
                                        <a href="saas.php?action=eliminarCS&idCSConfig=<?php echo (int)$csItem['idCSConfig']; ?>">Eliminar</a>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8">No hay bases de datos configuradas.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
                            
            <!-- Panel Video Conference a la derecha -->
            <div class="section-card">
                <?php if ($action === 'editarVC' && isset($_GET['idVCConfig']) && !empty($vcToEdit) && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
                    <h3><i class="fas fa-edit"></i> Editar Configuración Video Conference</h3>
                    <form action="saas.php?action=editarVC&idVCConfig=<?php echo (int)$_GET['idVCConfig']; ?>" method="POST">
                        <label>Nombre de la Video Conference:</label>
                        <input type="text" name="nombreVC" value="<?php echo htmlspecialchars($vcToEdit['nombreVC']); ?>" required>

                        <label>Calidad:</label>
                        <select name="calidad" required>
                            <option value="">Selecciona la Calidad</option>
                            <option value="240p" <?php if($vcToEdit['calidad']=='240p') echo 'selected'; ?>>240p</option>
                            <option value="480p" <?php if($vcToEdit['calidad']=='480p') echo 'selected'; ?>>480p</option>
                            <option value="720p" <?php if($vcToEdit['calidad']=='720p') echo 'selected'; ?>>720p</option>
                            <option value="1080p" <?php if($vcToEdit['calidad']=='1080p') echo 'selected'; ?>>1080p</option>
                            <option value="4k" <?php if($vcToEdit['calidad']=='4k') echo 'selected'; ?>>4k</option>
                        </select>

                        <label>Ancho de Banda:</label>
                        <input type="number" name="anchoBanda" min="1" value="<?php echo (int)$vcToEdit['anchoBanda']; ?>" required>

                        <label>Número máximo de Participantes:</label>
                        <input type="number" name="maxParticipantes" min="1" value="<?php echo (int)$vcToEdit['maxParticipantes']; ?>" required>
                        
                        <label>Idioma:</label>
                        <select name="idioma" required>
                            <option value="">Selecciona un Idioma</option>
                            <option value="Español" <?php if($vcToEdit['idioma']=='Español') echo 'selected'; ?>>Español</option>
                            <option value="English" <?php if($vcToEdit['idioma']=='English') echo 'selected'; ?>>English</option>
                            <option value="Deutsch" <?php if($vcToEdit['idioma']=='Deutsch') echo 'selected'; ?>>Deutsch</option>
                            <option value="Italian" <?php if($vcToEdit['idioma']=='Italian') echo 'selected'; ?>>Italian</option>
                            <option value="French" <?php if($vcToEdit['idioma']=='French') echo 'selected'; ?>>French</option>
                        </select>

                        <input type="submit" value="Guardar Cambios">
                    </form>
                <?php else: ?>
                    <h3><i class="fas fa-cogs"></i> Configuración de Video Conference</h3>
                    <form action="saas.php?action=crearVC" method="POST">
                        <label for="nombreVC">Nombre de la Video Conference:</label>
                        <input type="text" id="nombreVC" name="nombreVC" placeholder="Nombre" required>

                        <label for="calidad">Calidad:</label>
                        <select id="calidad" name="calidad" required>
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
                        <select id="idioma" name="idioma" required>
                            <option value="">Selecciona un Idioma</option>
                            <option value="Español">Español</option>
                            <option value="English">English</option>
                            <option value="Deutsch">Deutsch</option>
                            <option value="Italian">Italian</option>
                            <option value="French">French</option>
                        </select>

                        <input type="submit" value="Crear Video Conference">
                    </form>
                <?php endif; ?>

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
                                        <a href="saas.php?action=editarVC&idVCConfig=<?php echo (int)$vcItem['idVCConfig']; ?>">Editar</a>
                                        <a href="saas.php?action=eliminarVC&idVCConfig=<?php echo (int)$vcItem['idVCConfig']; ?>">Eliminar</a>
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