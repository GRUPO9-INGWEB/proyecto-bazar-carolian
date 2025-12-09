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
    <!-- Estilos propios (opcional) -->
    <link rel="stylesheet" href="../../public/css/estilos.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <span class="navbar-brand">Bazar Carolian</span>

        <div class="d-flex">
            <span class="navbar-text me-3">
                <?php echo htmlspecialchars($_SESSION["nombres"] . " " . $_SESSION["apellidos"]); ?>
                (<?php echo htmlspecialchars($_SESSION["nombre_rol"]); ?>)
            </span>
            <a href="../../controladores/cerrar_sesion.php" class="btn btn-outline-light btn-sm">
                Cerrar sesión
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid mt-3">
    <div class="row">
