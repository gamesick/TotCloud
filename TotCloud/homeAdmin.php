<?php
// homeadmin.php
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
            text-align: center;
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
            font-size: 16px;
        }
        .admin-options {
            margin-top: 20px;
            position: relative;
        }
        .administrar {
            background-color: #1e90ff;
        }
        .administrar:hover {
            background-color: #1c86ee;
        }
        .admin-suboptions {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 50px;
            left: 0;
            gap: 10px;
        }
        .admin-suboptions a {
            background-color: #3182ce;
        }
        .admin-suboptions a:hover {
            background-color: #2b6cb0;
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout">Cerrar Sesión</a>
    <div class="welcome">
        <h2>Bienvenido, <?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']); ?>!</h2>
        <p>Selecciona una opción:</p>
    </div>
    <div class="options">
        <a href="paas.php">PAAS</a>
        <a href="saas.php">SAAS</a>
    </div>
    <div class="admin-options">
        <a href="#" class="administrar">Administrar</a>
        <div class="admin-suboptions">
            <a href="AdministrarGrupos.php">Administrar Grupos</a>
            <a href="AsignarPrivilegios.php">Asignar Privilegios</a>
            <a href="AsignarGrupos.php">Asignar Grupos</a>
        </div>
    </div>
    <script>
        // JavaScript para mostrar/ocultar las subopciones al hacer clic en "Administrar"
        document.querySelector('.administrar').addEventListener('click', function(e) {
            e.preventDefault();
            var suboptions = document.querySelector('.admin-suboptions');
            if (suboptions.style.display === 'none' || suboptions.style.display === '') {
                suboptions.style.display = 'flex';
            } else {
                suboptions.style.display = 'none';
            }
        });

        // Cerrar el menú si se hace clic fuera de él
        window.addEventListener('click', function(e) {
            var administrar = document.querySelector('.administrar');
            var suboptions = document.querySelector('.admin-suboptions');
            if (!administrar.contains(e.target) && !suboptions.contains(e.target)) {
                suboptions.style.display = 'none';
            }
        });
    </script>
</body>
</html>