<?php
session_start();
// Si ya inició sesión, redirigir directamente al dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Bazar Carolian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
        }
        .login-container {
            max-width: 420px;
            margin: 90px auto;
            background: #fff;
            padding: 30px 35px;
            border-radius: 10px;
            box-shadow: 0px 3px 10px rgba(0,0,0,0.15);
        }
        .login-container h3 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        .btn-primary {
            width: 100%;
        }
        .footer-text {
            text-align: center;
            margin-top: 15px;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h3>Control de Inventario</h3>
    <form action="../controladores/procesar_login.php" method="POST">
        <div class="mb-3">
            <label for="dni" class="form-label">DNI</label>
            <input type="text" class="form-control" id="dni" name="dni" placeholder="Ingrese su DNI" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña" required>
        </div>

        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    </form>

    <p class="footer-text">© 2025 Bazar Carolian</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
