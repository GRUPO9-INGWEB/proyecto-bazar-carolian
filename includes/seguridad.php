<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Función para restringir acceso por rol
function require_role($rol_requerido) {
    if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != $rol_requerido) {
        // Redirigir al dashboard de su propio rol
        if ($_SESSION['usuario_rol'] == 1) {
            header("Location: dashboard_admin.php");
        } elseif ($_SESSION['usuario_rol'] == 2) {
            header("Location: dashboard_vendedor.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    }
}
?>
