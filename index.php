<?php
// index.php
session_start();

// Si ya hay sesión, redirigimos directamente al panel
if (isset($_SESSION["id_usuario"])) {
    if ($_SESSION["nombre_rol"] === "ADMINISTRADORA") {
        header("Location: vistas/panel/panel_admin.php");
    } else {
        header("Location: vistas/panel/panel_vendedora.php");
    }
    exit;
}

// Si no hay sesión, cargamos el controlador de login
require_once __DIR__ . "/controladores/LoginControlador.php";

$controlador = new LoginControlador();

// Revisamos la acción
$accion = $_GET["accion"] ?? "login";

if ($accion === "validar") {
    $controlador->validar();
} else {
    // Mostrar el formulario de login
    $controlador->mostrarFormulario();
}
