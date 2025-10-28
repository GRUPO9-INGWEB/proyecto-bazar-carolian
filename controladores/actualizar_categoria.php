<?php
// 1. Incluimos los archivos necesarios
include_once "../conexion.php";
include_once "../modelos/categoria_modelo.php";

// 2. Verificamos que los datos se hayan enviado por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3. Obtenemos los datos del formulario (¡incluyendo el ID!)
    $id_categoria = $_POST['id_categoria'];
    $nombre = $_POST['nombre_categoria'];
    $descripcion = $_POST['desc_categoria'];

    // 4. Llamamos a la función del modelo para actualizar
    $exito = actualizarCategoria($conexion, $id_categoria, $nombre, $descripcion);

    // 5. Redirigimos al usuario de vuelta a la lista
    // (Lo mandamos a /vistas/ con un mensaje de éxito)
    if ($exito) {
        header("Location: ../vistas/categorias.php?status=updated");
    } else {
        header("Location: ../vistas/categorias.php?status=update_error");
    }
    exit();
}
?>