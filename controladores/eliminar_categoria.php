<?php
// 1. Incluimos los archivos necesarios
include_once "../conexion.php";
include_once "../modelos/categoria_modelo.php";

// 2. Verificamos que se haya enviado un ID por la URL (GET)
if (isset($_GET['id'])) {
    
    $id_categoria = $_GET['id'];

    // 3. Llamamos a la función del modelo para eliminar
    $exito = eliminarCategoria($conexion, $id_categoria);

    // (Podríamos añadir un mensaje de éxito/error aquí,
    // pero por ahora solo redirigimos)
}

// 4. Redirigimos al usuario de vuelta a la lista
header("Location: ../vistas/categorias.php");
exit();

?>