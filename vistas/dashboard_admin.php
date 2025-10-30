<?php
// Ubicación: vistas/dashboard_admin.php

include_once "../includes/seguridad.php";
require_role(1); 

// --- LÓGICA DE CARGA DE CONTENIDO ---
$pagina_solicitada = $_GET['page'] ?? 'home_admin';

$vistas_permitidas = [
    'home_admin' => 'home_admin.php',
    'ventas' => 'ventas.php',
    'registrar_compra' => 'registrar_compra.php',
    'categorias' => 'categorias.php',
    'productos' => 'producto.php',
    'reportes' => 'reportes.php',
    'usuarios' => 'usuarios.php', 
    'clientes' => 'clientes.php',
    'proveedores' => 'proveedores.php',
];

$ruta_contenido = $vistas_permitidas[$pagina_solicitada] ?? 'home_admin.php'; 
$ruta_completa = __DIR__ . '/' . $ruta_contenido;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    
    <style>
        /* (Tus estilos CSS originales para el sidebar y layout) */
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f4f6fb;
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #2b3a67;
            color: white;
            display: flex;
            flex-direction: column;
            padding-top: 30px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        /* ... (Otros estilos del sidebar y main) ... */
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
        .sidebar a:hover, .sidebar a.active {
            background: #40528f;
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
            margin-bottom: 20px;
        }
        #contenido_dinamico {
            background: white; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            min-height: calc(100vh - 120px);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>👩‍💼 Admin</h2>
        <a href="?page=home_admin" class="<?= ($pagina_solicitada == 'home_admin') ? 'active' : ''; ?>"><span>🏠</span> Inicio</a>
        <a href="?page=ventas" class="<?= ($pagina_solicitada == 'ventas') ? 'active' : ''; ?>"><span>🛒</span> Ventas (Salidas)</a> 
        <a href="?page=registrar_compra" class="<?= ($pagina_solicitada == 'registrar_compra') ? 'active' : ''; ?>"><span>📥</span> Registrar Compra</a>
        <a href="?page=categorias" class="<?= ($pagina_solicitada == 'categorias') ? 'active' : ''; ?>"><span>📂</span> Categorías</a>
        <a href="?page=productos" class="<?= ($pagina_solicitada == 'productos') ? 'active' : ''; ?>"><span>📦</span> Productos</a>
        <a href="?page=reportes" class="<?= ($pagina_solicitada == 'reportes') ? 'active' : ''; ?>"><span>📈</span> Reportes</a>
        <a href="?page=usuarios" class="<?= ($pagina_solicitada == 'usuarios') ? 'active' : ''; ?>"><span>👥</span> Usuarios</a>
        <a href="?page=clientes" class="<?= ($pagina_solicitada == 'clientes') ? 'active' : ''; ?>"><span>👤</span> Clientes</a>
        <a href="?page=proveedores" class="<?= ($pagina_solicitada == 'proveedores') ? 'active' : ''; ?>"><span>🚚</span> Proveedores</a>
        
        <a class="logout" href="../controladores/logout.php"><span>🚪</span> Cerrar Sesión</a>
    </div>

    <div class="main">
        <header>
            <h1>Panel de Administración</h1>
            <p>Bienvenida, <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></strong> 👋</p>
        </header>

        <div id="contenido_dinamico">
            <?php
            // Incluye la vista (ej: usuarios.php)
            if (file_exists($ruta_completa)) {
                include $ruta_completa;
            } else {
                echo '<div class="alert alert-danger">Error: La página solicitada no existe.</div>';
            }
            ?>
        </div>
    </div>
    
    <script src="../vendor/jquery/jquery.min.js"></script> 
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    
</body>
</html>