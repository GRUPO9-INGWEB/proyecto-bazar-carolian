<?php
session_start();
include_once "../conexion.php";

// Si no está logueado, redirige al login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Redirige según rol
if ($_SESSION['rol_id'] == 1) {
    header("Location: dashboard_admin.php");
    exit;
} elseif ($_SESSION['rol_id'] == 2) {
    header("Location: dashboard_vendedor.php");
    exit;
} else {
    echo "Rol no reconocido.";
}
?>
