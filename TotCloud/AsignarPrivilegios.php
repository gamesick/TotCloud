<?php
// AsignarPrivilegios.php
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

// Manejo de asignaciones (Agregar, Eliminar)
$action = isset($_POST['action']) ? $_POST['action'] : '';
$idGrupo = isset($_POST['idGrupo']) ? intval($_POST['idGrupo']) : 0;
$idPrivilegio = isset($_POST['idPrivilegio']) ? intval($_POST['idPrivilegio']) : 0;
$error = '';
$success = '';

if ($action === 'assign' && $idGrupo > 0 && $idPrivilegio > 0) {
    // Asignar privilegio al grupo
    try {
        $stmt = $pdo->prepare("INSERT INTO R_GRUPO_PRIVILEGIOS (idGrupo, idPrivilegio) VALUES (:idGrupo, :idPrivilegio)");
        $stmt->execute([
            'idGrupo' => $idGrupo,
            'idPrivilegio' => $idPrivilegio
        ]);
        $success = "Privilegio asignado exitosamente.";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Violación de unicidad
            $error = "El privilegio ya está asignado a este grupo.";
        } else {
            $error = "Error al asignar el privilegio: " . $e->getMessage();
        }
    }
} elseif ($action === 'remove' && $idGrupo > 0 && $idPrivilegio > 0) {
    // Eliminar privilegio del grupo
    try {
        $stmtCheck = $pdo->prepare("
            SELECT 1 
            FROM R_GRUPO_PRIVILEGIOS 
            WHERE idGrupo = :idGrupo AND idPrivilegio = :idPrivilegio
        ");
        $stmtCheck->execute([
            'idGrupo' => $idGrupo,
            'idPrivilegio' => $idPrivilegio
        ]);
        $exists = $stmtCheck->fetch();

        if ($exists) {
            $stmt = $pdo->prepare("
                DELETE FROM R_GRUPO_PRIVILEGIOS 
                WHERE idGrupo = :idGrupo AND idPrivilegio = :idPrivilegio
            ");
            $stmt->execute([
                'idGrupo' => $idGrupo,
                'idPrivilegio' => $idPrivilegio
            ]);
            $success = "Privilegio revocado exitosamente.";
        } else {
            $error = "No se encontró la relación para eliminar.";
        }

        // Redirección para evitar reenvíos al actualizar la página
        header("Location: AsignarPrivilegios.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error al eliminar el privilegio: " . $e->getMessage();
    }
}

// Obtener todos los grupos
try {
    $stmt = $pdo->query("SELECT idGrupo, nombreGrupo FROM GRUPO ORDER BY nombreGrupo ASC");
    $grupos = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al obtener los grupos: " . $e->getMessage();
    exit();
}

// Obtener todos los privilegios
try {
    $stmt = $pdo->query("SELECT idPrivilegio, nombrePrivilegio FROM PRIVILEGIOS ORDER BY nombrePrivilegio ASC");
    $privilegios = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al obtener los privilegios: " . $e->getMessage();
    exit();
}

// Obtener todas las asignaciones actuales
try {
    $stmt = $pdo->query("
        SELECT R.idGrupo, R.idPrivilegio, G.nombreGrupo, P.nombrePrivilegio
        FROM R_GRUPO_PRIVILEGIOS R
        JOIN GRUPO G ON R.idGrupo = G.idGrupo
        JOIN PRIVILEGIOS P ON R.idPrivilegio = P.idPrivilegio
        ORDER BY G.nombreGrupo, P.nombrePrivilegio ASC
    ");
    $asignaciones = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al obtener las asignaciones: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Asignar Privilegios - TotCloud</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7fafc;
            padding: 20px;
        }
        .container {
            max-width: 900px;
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
        .assign-section, .current-assignments {
            margin-bottom: 40px;
        }
        .assign-section form select {
            width: 45%;
            padding: 10px;
            margin-right: 5%;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
        }
        .assign-section form input[type="submit"] {
            padding: 10px 20px;
            background-color: #3182ce;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .assign-section form input[type="submit"]:hover {
            background-color: #2b6cb0;
        }
        .current-assignments table {
            width: 100%;
            border-collapse: collapse;
        }
        .current-assignments th, .current-assignments td {
            border: 1px solid #cbd5e0;
            padding: 12px;
            text-align: left;
        }
        .current-assignments th {
            background-color: #edf2f7;
        }
        .current-assignments a {
            color: #e53e3e;
            text-decoration: none;
        }
        .current-assignments a:hover {
            text-decoration: underline;
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
        <a href="homeAdmin.php" class="back-link">← Volver al Inicio Administrativo</a>
        <h2>Asignar Privilegios a Grupos</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <!-- Sección para Asignar Privilegios -->
        <div class="assign-section">
            <h3>Asignar Privilegio</h3>
            <form action="AsignarPrivilegios.php?action=assign" method="POST">
            <input type="hidden" name="action" value="assign">
                <select name="idGrupo" required>
                    <option value="">Selecciona un grupo</option>
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?php echo $grupo['idGrupo']; ?>"><?php echo htmlspecialchars($grupo['nombreGrupo']); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select name="idPrivilegio" required>
                    <option value="">Selecciona un privilegio</option>
                    <?php foreach ($privilegios as $privilegio): ?>
                        <option value="<?php echo $privilegio['idPrivilegio']; ?>"><?php echo htmlspecialchars($privilegio['nombrePrivilegio']); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <input type="submit" value="Asignar Privilegio">
            </form>
        </div>
        
        <!-- Sección de Asignaciones Actuales -->
        <div class="current-assignments">
            <h3>Privilegios Asignados</h3>
            <table>
                <tr>
                    <th>Grupo</th>
                    <th>Privilegio</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($asignaciones as $asignacion): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($asignacion['nombreGrupo']); ?></td>
                        <td><?php echo htmlspecialchars($asignacion['nombrePrivilegio']); ?></td>
                        <td>
                        <form action="AsignarPrivilegios.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="idGrupo" value="<?php echo $asignacion['idGrupo']; ?>">
                        <input type="hidden" name="idPrivilegio" value="<?php echo $asignacion['idPrivilegio']; ?>">
                         <button type="submit" onclick="return confirm('¿Estás seguro de eliminar este privilegio?');" style="background:none; color:#e53e3e; border:none; cursor:pointer; text-decoration:underline;">Eliminar</button>
                        </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>