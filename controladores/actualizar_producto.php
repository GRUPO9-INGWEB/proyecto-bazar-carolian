<?php
include_once "../conexion.php"; 
include_once "../modelos/producto_modelo.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $datos_producto = [
        'id_producto' => $_POST['id_producto'],
        'id_categoria' => $_POST['id_categoria'],
        'codigo' => $_POST['codigo_producto'], // <-- ¡CAMPO NUEVO!
        'nombre' => $_POST['nombre_producto'],
        'descripcion' => $_POST['desc_producto'],
        'precio' => $_POST['precio_producto'],
        'stock' => $_POST['stock_producto']
    ];
    
    $resultado = actualizarProducto($conexion, $datos_producto);

    if ($resultado === true) {
        header("Location: ../vistas/inventario.php?status=updated");
    } else if ($resultado === "code_exists") {
        header("Location: ../vistas/inventario.php?status=code_exists");
    } else {
        header("Location: ../vistas/inventario.php?status=update_error");
    }
    exit();
}
?>