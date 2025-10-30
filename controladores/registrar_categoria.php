<?php
include_once "../conexion.php"; 
include_once "../modelos/categoria_modelo.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recolectamos los datos del formulario (ahora en un array)
    $datos = [
        'nombre' => $_POST['nombre_categoria'],
        'descripcion' => $_POST['desc_categoria'],
        'estado' => $_POST['estado'] // <-- Nuevo campo
    ];

    $exito = registrarCategoria($conexion, $datos);

    if ($exito) {
        header("Location: ../vistas/categorias.php?status=success");
    } else {
        header("Location: ../vistas/categorias.php?status=error");
    }
    exit();
}
?>