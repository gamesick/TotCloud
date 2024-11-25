<?php
// paas.php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>PAAS - TotCloud</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #d1e7dd;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 50px;
        }
        .content {
            background-color: #fff;
            padding: 30px 50px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .back {
            margin-top: 20px;
            text-decoration: none;
            color: #0d6efd;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>Plataforma como Servicio (PAAS)</h2>
        <p>Aquí puedes gestionar tus servicios PAAS.</p>
        <!-- Implementa las funcionalidades de PAAS aquí -->
        <a href="home.php" class="back">Volver</a>
    </div>
</body>
</html>