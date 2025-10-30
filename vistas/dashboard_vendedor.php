<?php
include_once "../includes/seguridad.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Vendedor</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f5faf3;
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #2d572c;
            color: white;
            display: flex;
            flex-direction: column;
            padding-top: 30px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 17px;
            transition: background 0.2s;
        }

        .sidebar a:hover {
            background: #3d8c3d;
        }

        .sidebar .logout {
            margin-top: auto;
            background: #ff4d4d;
        }

        .sidebar .logout:hover {
            background: #e63939;
        }

        .main {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            color: #2d572c;
        }

        header p {
            font-size: 16px;
            color: #555;
        }

        iframe {
            width: 100%;
            height: calc(100vh - 120px);
            border: none;
            border-radius: 12px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>🧍‍♂️ Vendedor</h2>
        <a href="home_vendedor.php" target="contenido"><span>🏠</span> Inicio</a>
        <a href="ventas.php" target="contenido"><span>🛒</span> Ventas</a>
        <a href="categorias.php" target="contenido"><span>📂</span> Categorías</a>
        <a href="inventario.php" target="contenido"><span>📦</span> Productos</a>
        <a href="reportes.php" target="contenido"><span>📈</span> Reportes</a>
        <a class="logout" href="../controladores/logout.php"><span>🚪</span> Cerrar Sesión</a>
    </div>

    <div class="main">
        <header>
            <h1>Panel de Vendedor</h1>
            <p>Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></strong> 👋</p>
        </header>

        <iframe name="contenido" src="home_vendedor.php"></iframe>
    </div>
</body>
</html>
