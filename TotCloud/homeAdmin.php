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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrativo - TotCloud</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <!-- Font Awesome para Iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset de estilos básicos */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Estilos generales del cuerpo */
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #333;
            position: relative;
        }

        /* Estilos para el enlace de cierre de sesión */
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

        /* Contenedor principal */
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 800px;
            width: 90%;
            transition: transform 0.3s;
        }

        /* Estilo del título de bienvenida */
        .container h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 28px;
            margin-bottom: 10px;
            color: #2d3748;
        }

        /* Estilo del subtítulo */
        .container p {
            font-size: 16px;
            margin-bottom: 30px;
            color: #4a5568;
        }

        /* Contenedor de opciones */
        .options {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }

        /* Estilos para los botones de opciones */
        .options a {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px 25px;
            background-color: #3182ce;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.2s;
            width: 100%;
            max-width: 300px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .options a:hover {
            background-color: #2b6cb0;
            transform: translateY(-2px);
        }

        /* Iconos opcionales para las opciones */
        .options a .icon {
            margin-right: 15px; /* Aumentado de 10px a 15px */
            font-size: 20px;
        }

        /* Contenedor de opciones administrativas */
        .admin-options {
            margin-top: 40px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Botón de "Administrar" */
        .admin-options .administrar {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px 20px;
            background-color: #1e90ff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.2s;
            max-width: 200px; /* Reducción del ancho máximo */
            width: auto; /* Permite que el ancho se ajuste al contenido */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            margin-bottom: 15px; /* Espaciado inferior */
        }

        .admin-options .administrar:hover {
            background-color: #1c86ee;
            transform: translateY(-2px);
        }

        /* Aumento del espacio entre el icono y el texto en "Administrar" */
        .admin-options .administrar .icon {
            margin-right: 15px; /* Aumentado de 10px a 15px */
            font-size: 20px;
        }

        /* Subopciones administrativas */
        .admin-suboptions {
            display: none;
            flex-direction: column;
            gap: 15px;
            align-items: center;
            transition: max-height 0.3s ease-out;
        }

        .admin-suboptions a {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            background-color: #3182ce;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.2s;
            width: 100%;
            max-width: 280px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .admin-suboptions a:hover {
            background-color: #2b6cb0;
            transform: translateY(-2px);
        }

        /* Media Queries para Responsividad */
        @media (min-width: 600px) {
            .options {
                flex-direction: row;
                justify-content: center;
            }

            .options a, .admin-options .administrar {
                width: auto;
                max-width: none;
            }

            .admin-suboptions {
                flex-direction: row;
                justify-content: center;
            }

            .admin-suboptions a {
                max-width: none;
                width: 180px;
            }
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout">Cerrar Sesión</a>
    <div class="container">
        <h2>Bienvenido, <?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']); ?>!</h2>
        <p>Selecciona una opción para continuar:</p>
        <div class="options">
            <a href="paas.php">
                <i class="fas fa-cloud icon"></i> PAAS
            </a>
            <a href="saas.php">
                <i class="fas fa-server icon"></i> SAAS
            </a>
        </div>
        <div class="admin-options">
            <div class="administrar" id="administrarBtn">
                <i class="fas fa-tools icon"></i> Administrar
            </div>
            <div class="admin-suboptions" id="adminSuboptions">
                <a href="AdministrarGrupos.php">
                    <i class="fas fa-users-cog icon"></i> Administrar Grupos
                </a>
                <a href="AsignarPrivilegios.php">
                    <i class="fas fa-user-shield icon"></i> Asignar Privilegios
                </a>
                <a href="AsignarGrupos.php">
                    <i class="fas fa-user-plus icon"></i> Asignar\n Grupos
                </a>
            </div>
        </div>
    </div>
    <script>
        // JavaScript para mostrar/ocultar las subopciones al hacer clic en "Administrar"
        document.getElementById('administrarBtn').addEventListener('click', function(e) {
            e.preventDefault();
            var suboptions = document.getElementById('adminSuboptions');
            if (suboptions.style.display === 'flex') {
                suboptions.style.display = 'none';
            } else {
                suboptions.style.display = 'flex';
            }
        });

        // Cerrar el menú si se hace clic fuera de él
        window.addEventListener('click', function(e) {
            var administrarBtn = document.getElementById('administrarBtn');
            var suboptions = document.getElementById('adminSuboptions');
            if (!administrarBtn.contains(e.target) && !suboptions.contains(e.target)) {
                suboptions.style.display = 'none';
            }
        });
    </script>
</body>
</html>