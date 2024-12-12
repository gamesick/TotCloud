<?php
// AsignarEtapas.php
session_start();
require 'config.php';

// Manejo de acciones (Agregar, Editar, Eliminar)
$action = isset($_GET['action']) ? $_GET['action'] : '';
$idServicio = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'edit' && $idServicio > 0) {
        $idEtapa = intval($_POST['idEtapa']);
        $tipoServicio = trim($_POST['tipoServicio']);
            try {
                $stmt = $pdo->prepare("UPDATE SERVICIO SET idEtapa = :idEtapa WHERE tipoServicio = :tipoServicio");
                $stmt->execute([
                    'idEtapa' => $idEtapa,
                    'tipoServicio' => $tipoServicio
                ]);
                $success = "Servicio actualizado exitosamente.";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Violación de unicidad
                    $error = "El nombre del servicio ya está en uso.";
                } else {
                    $error = "Error al actualizar el servicio: " . $e->getMessage();
                }
            }
    }
}

// Obtener todos los servicios
try {
    $stmt = $pdo->query("SELECT S.idServicio, S.tipoServicio, S.descripcion, E.nombreEtapa, E.idEtapa 
                         FROM SERVICIO S 
                         JOIN ETAPA E ON S.idEtapa = E.idEtapa
                         GROUP BY S.tipoServicio
                         ORDER BY S.idServicio ASC");
    $servicio = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al obtener los servicios: " . $e->getMessage();
    exit();
}

// Obtener todas las estpas para asignar a servicios
try {
    $stmt = $pdo->query("SELECT idEtapa, nombreEtapa FROM ETAPA ORDER BY idEtapa ASC");
    $etapa = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al obtener las etapas: " . $e->getMessage();
    exit();
}

// Si la acción es editar, obtener los datos del servicio
$servicioEdit = null;
if ($action === 'edit' && $idServicio > 0) {
    try {
        $stmt = $pdo->prepare("SELECT idEtapa, tipoServicio FROM SERVICIO WHERE idServicio = :idServicio");
        $stmt->execute(['idServicio' => $idServicio]);
        $servicioEdit = $stmt->fetch();
        if (!$servicioEdit) {
            $error = "Servicio no encontrado.";
        }
    } catch (PDOException $e) {
        echo "Error al obtener el servicio: " . $e->getMessage();
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Administrar Etapas - TotCloud</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7fafc;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        h2 {
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #cbd5e0;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #edf2f7;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #3182ce;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .form-container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container form input[type="text"],
        .form-container form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
        }
        .form-container form input[type="submit"] {
            background-color: #38a169;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-container form input[type="submit"]:hover {
            background-color: #2f855a;
        }
        .back-link {
            display: block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #3182ce;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="homeadmin.php" class="back-link">← Volver al Inicio Administrativo</a>
        <h2>Administrar Etapas</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <!-- Tabla de Servicios -->
        <table>
            <tr>
                <th>Nombre del Servicio</th>
                <th>Descripción</th>
                <th>Etapa</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($servicio as $servicios): ?>
                <tr>
                    <td><?php echo htmlspecialchars($servicios['tipoServicio']); ?></td>
                    <td><?php echo htmlspecialchars($servicios['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($servicios['nombreEtapa']); ?></td>
                    <td class="actions">
                        <a href="AdministrarEtapas.php?action=edit&id=<?php echo $servicios['idServicio']; ?>">Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        
        <!-- Formulario para Editar Servicios -->
        <div class="form-container">
            <?php if ($action === 'edit'): ?>
                <h3>Editar Servicio</h3>
                <form action="AdministrarEtapas.php?action=edit&id=<?php echo $idServicio; ?>" method="POST">
                    <label for="tipoServicio">Nombre del Servicio:</label>
                    <input type="text" id="tipoServicio" name="tipoServicio" value="<?php echo htmlspecialchars($servicioEdit['tipoServicio']); ?>" required>
                    
                    <label for="idEtapa">Etapa:</label>
                    <select id="idEtapa" name="idEtapa" required>
                        <option value="">Selecciona una etapa</option>
                        <?php foreach ($etapa as $etapas): ?>
                            <option value="<?php echo $etapas['idEtapa']; ?>">
                                <?php echo htmlspecialchars($etapas['nombreEtapa']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="submit" value="Actualizar Servicio">
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
