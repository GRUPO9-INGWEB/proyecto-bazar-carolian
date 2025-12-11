<?php
// vistas/plantillas/encabezado.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bazar Carolian - Panel</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons (para iconos en menú y topbar) -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Estilos propios compartidos (login + panel) -->
    <link rel="stylesheet" href="../../public/css/custom.css">
</head>

<body class="app-body bg-app">

<nav class="navbar navbar-expand-lg app-topbar">
    <div class="container-fluid px-3 px-md-4">
        <div class="d-flex align-items-center gap-2">
            <span class="navbar-brand mb-0 d-flex align-items-center gap-2">
                <span class="brand-icon">
                    <i class="bi bi-bag-check-fill"></i>
                </span>
                <span class="fw-semibold">Bazar Carolian</span>
            </span>
        </div>

        <div class="d-flex align-items-center ms-auto gap-3">
            <div class="d-none d-sm-flex flex-column text-end me-1">
                <span class="fw-semibold small">
                    <?php echo htmlspecialchars($_SESSION["nombres"] . " " . $_SESSION["apellidos"]); ?>
                </span>
                <span class="text-muted extra-small">
                    <?php echo htmlspecialchars($_SESSION["nombre_rol"]); ?>
                </span>
            </div>

            <a href="../../controladores/cerrar_sesion.php"
               class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Cerrar sesión</span>
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid py-3">
    <div class="row g-3">
