<?php
// saas.php
session_start();

?>
<!DOCTYPE html>
<html>
<head>
    <title>SAAS - TotCloud</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #cff4fc;
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
            text-align: center;
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
        <h2>Software como Servicio (SAAS)</h2>
        <p>Aquí puedes gestionar tus servicios SAAS.</p>
        <!-- Implementa las funcionalidades de SAAS aquí -->
        <a href="home.php" class="back">Volver</a>
    </div>
</body>
</html>