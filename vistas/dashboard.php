<?php
session_start();
include_once "../conexion.php";

// Si no está logueado, redirige al login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// --- CORRECCIÓN PRINCIPAL ---
// Se usa 'usuario_rol' en lugar de 'id_rol' para coincidir con el login
// Se añade isset() para evitar warnings si la variable no existiera.

if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1) {
    header("Location: dashboard_admin.php");
    exit;
} elseif (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 2) {
    header("Location: dashboard_vendedor.php");
    exit;
} else {
    // Si llega aquí, es porque el rol no es 1 ni 2, o es nulo.
    // Destruimos la sesión para evitar bucles y lo mandamos a login.
    session_destroy();
    echo "Rol no reconocido. Serás redirigido al login.";
    // Redirige al login después de 3 segundos
    header("refresh:3;url=login.php");
    exit;
}
?>