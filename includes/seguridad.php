<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
// (Asumiendo que 'login.php' está en el mismo nivel que las otras vistas)
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); // Ajusta la ruta si es necesario (ej: ../vistas/login.php)
    exit;
}

/**
 * Función para restringir acceso por rol.
 * Se debe incluir en cada página protegida, ej: require_role(1);
 */
function require_role($rol_requerido) {
    // Comprobamos si el rol existe y coincide
    if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != $rol_requerido) {
        
        // Si no coincide, lo mandamos a su dashboard correspondiente
        // para evitar que un admin entre al panel de vendedor y viceversa.
        
        if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1) {
            header("Location: dashboard_admin.php");
        } elseif (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 2) {
            header("Location: dashboard_vendedor.php");
        } else {
            // --- CORRECIÓN LÓGICA ---
            // Si el rol no es 1 o 2, es un error.
            // Destruimos sesión y lo mandamos al login.
            session_destroy();
            header("Location: login.php");
        }
        exit;
    }
}
?>