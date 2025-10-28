<?php

// Datos de conexión a la base de datos
$servidor = "localhost";  // Es el servidor de XAMPP
$usuario = "root";        // Es el usuario por defecto de XAMPP
$password = "";           // Es la contraseña por defecto de XAMPP (vacía)
$db = "bazar_db";         // El nombre de tu base de datos

// Crear la conexión
$conexion = new mysqli($servidor, $usuario, $password, $db);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Opcional: Configurar para que acepte tildes y eñes (UTF-8)
$conexion->set_charset("utf8");

// Si quieres probar si funciona, descomenta (borra el //) la siguiente línea:
// echo "¡Conexión exitosa!";

?>