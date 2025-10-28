<?php
// 1. Incluimos los archivos necesarios
include_once "../conexion.php";
include_once "../modelos/producto_modelo.php";

// 2. Verificamos que los datos se hayan enviado por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3. Creamos un array (diccionario) con los datos del formulario
    $datos_producto = [
        'id_producto' => $_POST['id_producto'], // ¡Importante!
        'id_categoria' => $_POST['id_categoria'],
        'nombre' => $_POST['nombre_producto'],
        'descripcion' => $_POST['desc_producto'],
        'precio' => $_POST['precio_producto'],
        'stock' => $_POST['stock_producto']
    ];

    // 4. Llamamos a la función del modelo para actualizar
    $exito = actualizarProducto($conexion, $datos_producto);

    // 5. Redirigimos al usuario de vuelta a la lista
    if ($exito) {
        header("Location: ../vistas/inventario.php?status=updated");
    } else {
        header("Location: ../vistas/inventario.php?status=update_error");
    }
    exit();
}
?>