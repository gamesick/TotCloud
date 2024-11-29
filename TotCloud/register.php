<?php
// register.php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = trim($_POST['nombreUsuario']);
    $contrasenya = $_POST['contrasenya'];
    $idOrganizacion = $_POST['idOrganizacion']; // Asegúrate de que este campo esté presente en el formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];

    // Validar los datos
    if (empty($nombreUsuario) || empty($contrasenya) || empty($idOrganizacion)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Hash de la contraseña
        $hashedPassword = password_hash($contrasenya, PASSWORD_DEFAULT);

        try {
            // Insertar la nueva persona
            $stmt = $pdo->prepare("INSERT INTO PERSONA (nombre, apellido, email) VALUES (:nombre, :apellido, :email)");
            $stmt->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email
            ]);
            
            $idUsuario = $pdo->lastInsertId();
            // Insertar el nuevo usuario
            $stmt = $pdo->prepare("INSERT INTO USUARIO (idUsuario, nombreUsuario, contrasenya, idOrganizacion) VALUES (:idUsuario, :nombreUsuario, :contrasenya, :idOrganizacion)");
            $stmt->execute([
                'idUsuario' => $idUsuario,
                'nombreUsuario' => $nombreUsuario,
                'contrasenya' => $hashedPassword,
                'idOrganizacion' => $idOrganizacion
            ]);

            // Autenticación exitosa después del registro
            $_SESSION['usuario_id'] = $pdo->lastInsertId();
            header('Location: home.php');
            exit();
        } catch (PDOException $e) {
            // Manejo de errores (por ejemplo, nombre de usuario duplicado)
            if ($e->getCode() == 23000) { // Código de error para violación de restricción de unicidad
                $error = "El nombre de usuario ya está en uso.";
            } else {
                $error = "Error al registrar el usuario: " . $e->getMessage();
            }
        }
    }

    // Redirigir con error
    header('Location: register.php?error=' . urlencode($error));
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registro - TotCloud</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }
        .register-container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
        }
        .register-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .register-container input[type="text"],
        .register-container input[type="password"],
        .register-container select {
            width: 90%;
            padding: 12px 20px;
            margin: 8px 0 16px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .register-container input[type="submit"] {
            width: 90%;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0 0 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .register-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Registro de Usuario</h2>
        <?php
        if (isset($_GET['error'])) {
            echo '<div class="error">' . htmlspecialchars($_GET['error']) . '</div>';
        }
        ?>
        <form action="register.php" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" required>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
            
            <label for="nombreUsuario">Nombre de Usuario:</label>
            <input type="text" id="nombreUsuario" name="nombreUsuario" required>

            <label for="contrasenya">Contraseña:</label>
            <input type="password" id="contrasenya" name="contrasenya" required>

            <label for="idOrganizacion">Organización:</label>
            <select id="idOrganizacion" name="idOrganizacion" required>
                <option value="">Selecciona una organización</option>
                <?php
                // Obtener las organizaciones para el desplegable
                $stmt = $pdo->query("SELECT idOrganizacion, nombreOrganizacion FROM ORGANIZACION");
                while ($row = $stmt->fetch()) {
                    echo '<option value="' . htmlspecialchars($row['idOrganizacion']) . '">' . htmlspecialchars($row['nombreOrganizacion']) . '</option>';
                }
                ?>
            </select>

            <input type="submit" value="Registrar">
        </form>
    </div>
</body>
</html>