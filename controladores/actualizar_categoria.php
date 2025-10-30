<?php
include_once "../conexion.php"; 
include_once "../modelos/categoria_modelo.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recolectamos los datos (ahora en un array)
    $datos = [
        'id_categoria' => $_POST['id_categoria'],
        'nombre' => $_POST['nombre_categoria'],
        'descripcion' => $_POST['desc_categoria'],
        'estado' => $_POST['estado'] // <-- Nuevo campo
    ];
    
    $exito = actualizarCategoria($conexion, $datos);

    if ($exito) {
        header("Location: ../vistas/categorias.php?status=updated");
    } else {
        header("Location: ../vistas/categorias.php?status=update_error");
    }
    exit();
}
?>