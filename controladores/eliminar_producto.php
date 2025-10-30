<?php
// 1. Incluimos los archivos necesarios
include_once "../conexion.php";
include_once "../modelos/producto_modelo.php";

$exito = false; // Suponemos que fallará por defecto

// 2. Verificamos que se haya enviado un ID por la URL (GET)
if (isset($_GET['id'])) {
    
    $id_producto = $_GET['id'];

    // 3. Llamamos a la función del modelo para eliminar
    $exito = eliminarProducto($conexion, $id_producto);
}

// 4. Redirigimos al usuario de vuelta a la lista
// --- CAMBIO DE RUTA ---
if ($exito) {
    header("Location: ../vistas/producto.php?status=deleted");
} else {
    header("Location: ../vistas/producto.php?status=delete_error");
}
exit();
?>