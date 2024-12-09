<?php
// AdministrarGrupos.php
session_start();
require 'config.php';

// Manejo de acciones (Agregar, Editar, Eliminar)
$action = isset($_GET['action']) ? $_GET['action'] : '';
$idGrupo = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        // Agregar nuevo grupo
        $nombreGrupo = trim($_POST['nombreGrupo']);
        $descripcion = trim($_POST['descripcion']);
        $idPersonal = intval($_POST['idPersonal']);

        if (empty($nombreGrupo)) {
            $error = "El nombre del grupo es obligatorio.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO GRUPO (nombreGrupo, descripcion, idPersonal) VALUES (:nombreGrupo, :descripcion, :idPersonal)");
                $stmt->execute([
                    'nombreGrupo' => $nombreGrupo,
                    'descripcion' => $descripcion,
                    'idPersonal' => $idPersonal
                ]);
                $success = "Grupo agregado exitosamente.";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Violación de unicidad
                    $error = "El nombre del grupo ya está en uso.";
                } else {
                    $error = "Error al agregar el grupo: " . $e->getMessage();
                }
            }
        }
    } elseif ($action === 'edit' && $idGrupo > 0) {
        // Editar grupo existente
        $nombreGrupo = trim($_POST['nombreGrupo']);
        $descripcion = trim($_POST['descripcion']);
        $idPersonal = intval($_POST['idPersonal']);

        if (empty($nombreGrupo)) {
            $error = "El nombre del grupo es obligatorio.";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE GRUPO SET nombreGrupo = :nombreGrupo, descripcion = :descripcion, idPersonal = :idPersonal WHERE idGrupo = :idGrupo");
                $stmt->execute([
                    'nombreGrupo' => $nombreGrupo,
                    'descripcion' => $descripcion,
                    'idPersonal' => $idPersonal,
                    'idGrupo' => $idGrupo
                ]);
                $success = "Grupo actualizado exitosamente.";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Violación de unicidad
                    $error = "El nombre del grupo ya está en uso.";
                } else {
                    $error = "Error al actualizar el grupo: " . $e->getMessage();
                }
            }
        }
    }
} elseif ($action === 'delete' && $idGrupo > 0) {
    // Eliminar grupo
    try {
        // Verificar si hay personas asignadas a este grupo
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM USUARIO WHERE idGrupo = :idGrupo");
        $stmt->execute(['idGrupo' => $idGrupo]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error = "No se puede eliminar el grupo porque tiene personas asignadas.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM GRUPO WHERE idGrupo = :idGrupo");
            $stmt->execute(['idGrupo' => $idGrupo]);
            $success = "Grupo eliminado exitosamente.";
        }
    } catch (PDOException $e) {
        $error = "Error al eliminar el grupo: " . $e->getMessage();
    }
}

// Obtener todos los grupos
try {
    $stmt = $pdo->query("SELECT G.idGrupo, G.nombreGrupo, G.descripcion, P.nombrePersonal 
                         FROM GRUPO G 
                         JOIN PERSONAL P ON G.idPersonal = P.idPersonal 
                         ORDER BY G.idGrupo ASC");
    $grupos = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al obtener los grupos: " . $e->getMessage();
    exit();
}

// Obtener todas las personas para asignar a grupos
try {
    $stmt = $pdo->query("SELECT idPersonal, nombrePersonal FROM PERSONAL ORDER BY nombrePersonal ASC");
    $personales = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al obtener las personas: " . $e->getMessage();
    exit();
}

// Si la acción es editar, obtener los datos del grupo
$grupoEdit = null;
if ($action === 'edit' && $idGrupo > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM GRUPO WHERE idGrupo = :idGrupo");
        $stmt->execute(['idGrupo' => $idGrupo]);
        $grupoEdit = $stmt->fetch();
        if (!$grupoEdit) {
            $error = "Grupo no encontrado.";
        }
    } catch (PDOException $e) {
        echo "Error al obtener el grupo: " . $e->getMessage();
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Administrar Grupos - TotCloud</title>
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
        <h2>Administrar Grupos</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <!-- Tabla de Grupos -->
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre del Grupo</th>
                <th>Descripción</th>
                <th>Personal Responsable</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($grupos as $grupo): ?>
                <tr>
                    <td><?php echo htmlspecialchars($grupo['idGrupo']); ?></td>
                    <td><?php echo htmlspecialchars($grupo['nombreGrupo']); ?></td>
                    <td><?php echo htmlspecialchars($grupo['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($grupo['nombrePersonal']); ?></td>
                    <td class="actions">
                        <a href="AdministrarGrupos.php?action=edit&id=<?php echo $grupo['idGrupo']; ?>">Editar</a>
                        <a href="AdministrarGrupos.php?action=delete&id=<?php echo $grupo['idGrupo']; ?>" onclick="return confirm('¿Estás seguro de eliminar este grupo?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        
        <!-- Formulario para Agregar o Editar Grupos -->
        <div class="form-container">
            <?php if ($action === 'edit' && $grupoEdit): ?>
                <h3>Editar Grupo</h3>
                <form action="AdministrarGrupos.php?action=edit&id=<?php echo $idGrupo; ?>" method="POST">
                    <label for="nombreGrupo">Nombre del Grupo:</label>
                    <input type="text" id="nombreGrupo" name="nombreGrupo" value="<?php echo htmlspecialchars($grupoEdit['nombreGrupo']); ?>" required>
                    
                    <label for="descripcion">Descripción:</label>
                    <input type="text" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($grupoEdit['descripcion']); ?>">
                    
                    <label for="idPersonal">Personal Responsable:</label>
                    <select id="idPersonal" name="idPersonal" required>
                        <option value="">Selecciona una persona</option>
                        <?php foreach ($personales as $personal): ?>
                            <option value="<?php echo $personal['idPersonal']; ?>" <?php echo ($grupoEdit['idPersonal'] == $personal['idPersonal']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($personal['nombrePersonal']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="submit" value="Actualizar Grupo">
                </form>
            <?php else: ?>
                <h3>Agregar Nuevo Grupo</h3>
                <form action="AdministrarGrupos.php?action=add" method="POST">
                    <label for="nombreGrupo">Nombre del Grupo:</label>
                    <input type="text" id="nombreGrupo" name="nombreGrupo" required>
                    
                    <label for="descripcion">Descripción:</label>
                    <input type="text" id="descripcion" name="descripcion">
                    
                    <label for="idPersonal">Personal Responsable:</label>
                    <select id="idPersonal" name="idPersonal" required>
                        <option value="">Selecciona una persona</option>
                        <?php foreach ($personales as $personal): ?>
                            <option value="<?php echo $personal['idPersonal']; ?>">
                                <?php echo htmlspecialchars($personal['nombrePersonal']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="submit" value="Agregar Grupo">
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
