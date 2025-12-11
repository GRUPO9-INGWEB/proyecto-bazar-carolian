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

    <!-- Bootstrap Icons (para los íconos de usuario/contraseña) -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Tu CSS personalizado -->
    <link rel="stylesheet" href="public/css/custom.css">
</head>

<body class="bg-login">

<div class="login-wrapper d-flex justify-content-center align-items-center min-vh-100">
    <div class="card login-card shadow-lg border-0">
        <div class="card-header text-center border-0 pb-0 bg-transparent">
            <div class="login-icon mb-3">
                <i class="bi bi-bag-check-fill"></i>
            </div>
            <h4 class="mb-1 fw-semibold">Bazar Carolian</h4>
            <p class="text-muted small mb-0">Sistema de inventario y ventas</p>
        </div>

        <div class="card-body px-4 px-md-5 pt-4 pb-4">
            <?php if ($mensaje !== ""): ?>
                <div class="alert alert-warning text-center mb-3">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form action="index.php?accion=validar" method="post" class="login-form">
                <div class="mb-3">
                    <label for="nombre_usuario" class="form-label small text-muted mb-1">Usuario</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text"
                               class="form-control border-start-0"
                               id="nombre_usuario"
                               name="nombre_usuario"
                               required
                               autocomplete="off"
                               placeholder="Ingresa tu usuario">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="clave" class="form-label small text-muted mb-1">Contraseña</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password"
                               class="form-control border-start-0"
                               id="clave"
                               name="clave"
                               required
                               placeholder="Ingresa tu contraseña">
                        <button type="button"
                                class="btn btn-outline-secondary border-start-0"
                                onclick="toggleClave('clave', this)"
                                aria-label="Mostrar contraseña">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check small">
                        <input class="form-check-input" type="checkbox" value="" id="recordarme">
                        <label class="form-check-label" for="recordarme">
                            Recordarme
                        </label>
                    </div>
                    <a href="#" class="small text-decoration-none text-primary-600">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-primary btn-lg login-btn">
                        Iniciar sesión
                    </button>
                </div>

                <p class="text-center text-muted small mb-0">
                    Acceso exclusivo para personal autorizado
                </p>
            </form>
        </div>

        <div class="card-footer text-center bg-transparent border-0 pt-0 pb-3">
            <small class="text-muted">&copy; <?php echo date("Y"); ?> Bazar Carolian</small>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleClave(idCampo, boton) {
    const input = document.getElementById(idCampo);
    if (!input) return;

    const icon = boton.querySelector('i');

    if (input.type === "password") {
        input.type = "text";
        if (icon) {
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        }
        boton.setAttribute("aria-label", "Ocultar contraseña");
    } else {
        input.type = "password";
        if (icon) {
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
        boton.setAttribute("aria-label", "Mostrar contraseña");
    }
}
</script>

</body>
</html>
