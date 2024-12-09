<?php
// AsignarGrupos.php
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

// Manejo de acciones (Asignar, Eliminar)
$action = isset($_GET['action']) ? $_GET['action'] : '';
$idGrupo = isset($_GET['idGrupo']) ? intval($_GET['idGrupo']) : 0;
$idUsuario = isset($_GET['idUsuario']) ? intval($_GET['idUsuario']) : 0;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'assign' && $idGrupo > 0 && isset($_POST['idUsuario'])) {
        $idUsuario = intval($_POST['idUsuario']);
        // Asignar grupo al usuario
        try {
            // Verificar si el usuario ya pertenece a otro grupo
            $stmt = $pdo->prepare("SELECT idGrupo FROM USUARIO WHERE idUsuario = :idUsuario");
            $stmt->execute(['idUsuario' => $idUsuario]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                // Asignar el nuevo grupo (esto automáticamente elimina del anterior si solo se permite un grupo)
                $stmt = $pdo->prepare("UPDATE USUARIO SET idGrupo = :idGrupo WHERE idUsuario = :idUsuario");
                $stmt->execute([
                    'idGrupo' => $idGrupo,
                    'idUsuario' => $idUsuario
                ]);
                $success = "Usuario asignado al grupo exitosamente.";
            } else {
                $error = "Usuario no encontrado.";
            }
        } catch (PDOException $e) {
            $error = "Error al asignar el grupo: " . $e->getMessage();
        }
    } elseif ($action === 'remove' && $idGrupo > 0 && $idUsuario > 0) {
        // Eliminar grupo del usuario
        try {
            $stmt = $pdo->prepare("UPDATE USUARIO SET idGrupo = NULL WHERE idUsuario = :idUsuario AND idGrupo = :idGrupo");
            $stmt->execute([
                'idUsuario' => $idUsuario,
                'idGrupo' => $idGrupo
            ]);
            $success = "Usuario eliminado del grupo exitosamente.";
        } catch (PDOException $e) {
            $error = "Error al eliminar el usuario del grupo: " . $e->getMessage();
        }
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

// Obtener todos los usuarios que no pertenecen a ningún grupo
try {
    $stmt = $pdo->query("SELECT U.idUsuario, U.nombreUsuario, P.nombre, P.apellido FROM USUARIO U JOIN PERSONA P ON U.idUsuario = P.idPersona WHERE U.idGrupo IS NULL ORDER BY U.nombreUsuario ASC");
    $usuariosSinGrupo = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al obtener los usuarios sin grupo: " . $e->getMessage();
    exit();
}

// Obtener todos los usuarios para asignar (incluye usuarios sin grupo y los que ya están en grupos)
try {
    $stmt = $pdo->query("SELECT U.idUsuario, U.nombreUsuario, P.nombre, P.apellido, G.nombreGrupo FROM USUARIO U JOIN PERSONA P ON U.idUsuario = P.idPersona LEFT JOIN GRUPO G ON U.idGrupo = G.idGrupo ORDER BY U.nombreUsuario ASC");
    $usuarios = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al obtener los usuarios: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Asignar Grupos - TotCloud</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7fafc;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
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
        .group-section {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .group-section h3 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #cbd5e0;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #edf2f7;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #e53e3e;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .assign-form, .remove-form {
            margin-top: 15px;
        }
        .assign-form select, .remove-form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
        }
        .assign-form input[type="submit"], .remove-form input[type="submit"] {
            background-color: #38a169;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .assign-form input[type="submit"]:hover, .remove-form input[type="submit"]:hover {
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
        <h2>Asignar Usuarios a Grupos</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php foreach ($grupos as $grupo): ?>
            <div class="group-section">
                <h3>Grupo: <?php echo htmlspecialchars($grupo['nombreGrupo']); ?></h3>
                
                <!-- Listado de Usuarios en el Grupo -->
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Usuario</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Acciones</th>
                    </tr>
                    <?php
                        // Obtener usuarios asignados al grupo actual
                        try {
                            $stmt = $pdo->prepare("
                                SELECT U.idUsuario, U.nombreUsuario, P.nombre, P.apellido
                                FROM USUARIO U
                                JOIN PERSONA P ON U.idUsuario = P.idPersona
                                WHERE U.idGrupo = :idGrupo
                                ORDER BY U.nombreUsuario ASC
                            ");
                            $stmt->execute(['idGrupo' => $grupo['idGrupo']]);
                            $usuariosEnGrupo = $stmt->fetchAll();
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='5'>Error al obtener usuarios del grupo: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                            continue;
                        }
                        
                        if ($usuariosEnGrupo):
                            foreach ($usuariosEnGrupo as $usuario):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['idUsuario']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombreUsuario']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
                            <td class="actions">
                                <a href="#" onclick="document.getElementById('removeForm<?php echo $usuario['idUsuario']; ?>').style.display='block';">Eliminar</a>
                                
                                <!-- Formulario para Eliminar Usuario del Grupo -->
                                <div id="removeForm<?php echo $usuario['idUsuario']; ?>" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background-color:#fff; padding:20px; border:1px solid #ccc; border-radius:8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                                    <h4>Eliminar Usuario del Grupo</h4>
                                    <p>¿Estás seguro de eliminar a <strong><?php echo htmlspecialchars($usuario['nombreUsuario']); ?></strong> del grupo <strong><?php echo htmlspecialchars($grupo['nombreGrupo']); ?></strong>?</p>
                                    <form action="AsignarGrupos.php?action=remove&idGrupo=<?php echo $grupo['idGrupo']; ?>&idUsuario=<?php echo $usuario['idUsuario']; ?>" method="POST" class="remove-form">
                                        <input type="submit" value="Eliminar">
                                        <button type="button" onclick="document.getElementById('removeForm<?php echo $usuario['idUsuario']; ?>').style.display='none';">Cancelar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php
                            endforeach;
                        else:
                    ?>
                        <tr>
                            <td colspan="5">No hay usuarios asignados a este grupo.</td>
                        </tr>
                    <?php endif; ?>
                </table>
                
                <!-- Formulario para Asignar Usuarios al Grupo -->
                <form action="AsignarGrupos.php?action=assign&idGrupo=<?php echo $grupo['idGrupo']; ?>" method="POST" class="assign-form">
                    <label for="idUsuario<?php echo $grupo['idGrupo']; ?>">Asignar Usuario al Grupo:</label>
                    <select name="idUsuario" id="idUsuario<?php echo $grupo['idGrupo']; ?>" required>
                        <option value="">Selecciona un Usuario</option>
                        <?php foreach ($usuariosSinGrupo as $usuarioSinGrupo): ?>
                            <option value="<?php echo $usuarioSinGrupo['idUsuario']; ?>">
                                <?php echo htmlspecialchars($usuarioSinGrupo['nombreUsuario'] . ' (' . $usuarioSinGrupo['nombre'] . ' ' . $usuarioSinGrupo['apellido'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" value="Asignar Usuario">
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>