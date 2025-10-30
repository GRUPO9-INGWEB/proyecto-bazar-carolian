<?php
include_once "../conexion.php"; 
include_once "../modelos/proveedor_modelo.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recolectamos todos los datos del formulario
    $datos = [
        'ruc' => $_POST['ruc'],
        'razon_social' => $_POST['razon_social'],
        'direccion' => $_POST['direccion'],
        'telefono' => $_POST['telefono'],
        'email' => $_POST['email'],
        'estado' => $_POST['estado']
    ];

    $resultado = registrarProveedor($conexion, $datos);

    // Redirigimos con el estado correspondiente
    if ($resultado === true) {
        header("Location: ../vistas/proveedores.php?status=success");
    } else if ($resultado === "ruc_exists") {
        header("Location: ../vistas/proveedores.php?status=ruc_exists");
    } else {
        header("Location: ../vistas/proveedores.php?status=error");
    }
    exit();
}
?>