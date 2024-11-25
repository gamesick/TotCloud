<!-- index.php -->
<?php
session_start();

// Si el usuario ya está logueado, redirígelo a home.php
if (isset($_SESSION['usuario_id'])) {
    header('Location: home.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Iniciar Sesión - TotCloud</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
        }
        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 90%;
            padding: 12px 20px;
            margin: 8px 0 16px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .login-container input[type="submit"] {
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
        .login-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
        .register-link a {
            color: #0d6efd;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php
        if (isset($_GET['error'])) {
            echo '<div class="error">' . htmlspecialchars($_GET['error']) . '</div>';
        }
        ?>
        <form action="login.php" method="POST">
            <label for="nombreUsuario">Nombre de Usuario:</label>
            <input type="text" id="nombreUsuario" name="nombreUsuario" required>
            
            <label for="contrasenya">Contraseña:</label>
            <input type="password" id="contrasenya" name="contrasenya" required>
            
            <input type="submit" value="Iniciar Sesión">
        </form>
        <div class="register-link">
            <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>.</p>
        </div>
    </div>
</body>
</html>