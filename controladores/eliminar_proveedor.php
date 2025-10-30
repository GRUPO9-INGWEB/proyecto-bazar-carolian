<?php
include_once "../conexion.php";
include_once "../modelos/proveedor_modelo.php";

$exito = false; 

// Verificamos que se haya enviado un ID por la URL (GET)
if (isset($_GET['id'])) {
    $id_proveedor = $_GET['id'];
    
    // Llamamos a la función del modelo para eliminar
    // (Considerar manejo de errores si el proveedor tiene compras asociadas)
    $exito = eliminarProveedor($conexion, $id_proveedor);
}

// Redirigimos al usuario de vuelta a la lista
if ($exito) {
    header("Location: ../vistas/proveedores.php?status=deleted");
} else {
    // (Puede fallar si el proveedor tiene llaves foráneas activas, ej. en 'compras')
    header("Location: ../vistas/proveedores.php?status=delete_error");
}
exit();
?>