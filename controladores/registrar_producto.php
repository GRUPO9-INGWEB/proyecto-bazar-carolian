<?php
include_once "../conexion.php"; 
include_once "../modelos/producto_modelo.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $datos_producto = [
        'id_categoria' => $_POST['id_categoria'],
        'codigo' => $_POST['codigo_producto'],
        'nombre' => $_POST['nombre_producto'],
        'descripcion' => $_POST['desc_producto'],
        'precio' => $_POST['precio_producto'],
        'fecha_caducidad' => $_POST['fecha_caducidad'],
        'estado' => $_POST['estado']
    ];

    $resultado = registrarProducto($conexion, $datos_producto);

    if ($resultado === true) {
        header("Location: ../vistas/producto.php?status=success");
    } else if ($resultado === "code_exists") {
        header("Location: ../vistas/producto.php?status=code_exists");
    } else {
        header("Location: ../vistas/producto.php?status=error");
    }
    exit();
}
?>