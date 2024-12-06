<?php
// saasAdmin.php
session_start();
require 'config.php';

// Función para verificar si el usuario es administrador
function esAdministrador() {
    return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';
}

// Verificar si el empleado está autenticado y es administrador
if (!isset($_SESSION['personal_id']) || !esAdministrador()) {
    echo "Acceso denegado. No tienes permisos para acceder a esta página.";
    exit();
}

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

// TODO: Aquí podrías obtener datos sobre el estado actual de las bases de datos y aplicaciones
// Ejemplo: $dbServices = ... ; $appServices = ... ;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración SAAS - TotCloud</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <!-- Font Awesome para Iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset de estilos */
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
        <p>Aquí el personal de TotCloud puede gestionar la configuración, etapas y acceso a los servicios SAAS ofrecidos.</p>
        <div class="sections">
            <!-- Sección para manejo de Base de Datos -->
            <div class="section-card">
                <h3><i class="fas fa-database"></i> Configuración de Bases de Datos</h3>
                <form action="saasAdmin.php?action=crearDB" method="POST">
                    <label for="dbName">Nombre de la Base de Datos:</label>
                    <input type="text" id="dbName" name="dbName" placeholder="ej: clientes_db" required>

                    <label for="dbPlatform">Plataforma (Motor de Base de Datos):</label>
                    <select id="dbPlatform" name="dbPlatform">
                        <option value="mysql">MySQL</option>
                        <option value="postgresql">PostgreSQL</option>
                        <option value="mariadb">MariaDB</option>
                        <!-- Agregar más opciones según tus necesidades -->
                    </select>

                    <label for="dbStage">Etapa del Servicio:</label>
                    <select id="dbStage" name="dbStage">
                        <option value="en_pruebas">En Pruebas</option>
                        <option value="disponible">Disponible</option>
                        <option value="no_disponible">No Disponible</option>
                    </select>

                    <input type="submit" value="Crear/Actualizar DB">
                </form>

                <div class="service-list">
                    <h4>Bases de Datos Configuradas</h4>
                    <table>
                        <tr>
                            <th>Nombre</th>
                            <th>Plataforma</th>
                            <th>Etapa</th>
                            <th>Acciones</th>
                        </tr>
                        <!-- TODO: Aquí iterar sobre las BD existentes (ej: $dbServices) -->
                        <!-- Ejemplo Estático -->
                        <tr>
                            <td>clientes_db</td>
                            <td>MySQL</td>
                            <td>Disponible</td>
                            <td class="actions">
                                <a href="#">Editar</a>
                                <a href="#">Eliminar</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Sección para manejo de Aplicaciones -->
            <div class="section-card">
                <h3><i class="fas fa-cogs"></i> Acceso a Aplicaciones</h3>
                <form action="saasAdmin.php?action=agregarApp" method="POST">
                    <label for="appName">Nombre de la Aplicación:</label>
                    <input type="text" id="appName" name="appName" placeholder="ej: CRM_ventas" required>

                    <label for="appStage">Etapa del Servicio:</label>
                    <select id="appStage" name="appStage">
                        <option value="en_pruebas">En Pruebas</option>
                        <option value="disponible">Disponible</option>
                        <option value="no_disponible">No Disponible</option>
                    </select>

                    <input type="submit" value="Agregar/Actualizar App">
                </form>

                <div class="service-list">
                    <h4>Aplicaciones Disponibles</h4>
                    <table>
                        <tr>
                            <th>Aplicación</th>
                            <th>Etapa</th>
                            <th>Acciones</th>
                        </tr>
                        <!-- TODO: Aquí iterar sobre las aplicaciones existentes (ej: $appServices) -->
                        <!-- Ejemplo Estático -->
                        <tr>
                            <td>CRM_ventas</td>
                            <td>En Pruebas</td>
                            <td class="actions">
                                <a href="#">Editar</a>
                                <a href="#">Eliminar</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
