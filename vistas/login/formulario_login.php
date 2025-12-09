<?php
// vistas/login/formulario_login.php
if (!isset($mensaje)) {
    $mensaje = "";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - Bazar Carolian</title>
    <!-- Bootstrap 5 desde CDN -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow" style="max-width: 400px; width: 100%;">
        <div class="card-header text-center">
            <h4 class="mb-0">Bazar Carolian</h4>
            <small>Sistema de inventario y ventas</small>
        </div>
        <div class="card-body">
            <?php if ($mensaje !== ""): ?>
                <div class="alert alert-warning text-center">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form action="index.php?accion=validar" method="post">
                <div class="mb-3">
                    <label for="nombre_usuario" class="form-label">Usuario</label>
                    <input type="text"
                           class="form-control"
                           id="nombre_usuario"
                           name="nombre_usuario"
                           required
                           autocomplete="off">
                </div>

                <div class="mb-3">
                    <label for="clave" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input type="password"
                               class="form-control"
                               id="clave"
                               name="clave"
                               required>
                        <button type="button"
                                class="btn btn-outline-secondary"
                                onclick="toggleClave('clave', this)">
                            Ver
                        </button>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        Iniciar sesión
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <small>&copy; <?php echo date("Y"); ?> Bazar Carolian</small>
        </div>
    </div>
</div>

<script>
function toggleClave(idCampo, boton) {
    const input = document.getElementById(idCampo);
    if (!input) return;

    if (input.type === "password") {
        input.type = "text";
        boton.textContent = "Ocultar";
    } else {
        input.type = "password";
        boton.textContent = "Ver";
    }
}
</script>

</body>
</html>
