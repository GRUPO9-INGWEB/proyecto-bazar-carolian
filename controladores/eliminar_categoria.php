<?php
include_once "../conexion.php";
include_once "../modelos/categoria_modelo.php";

$exito = false; 

if (isset($_GET['id'])) {
    $id_categoria = $_GET['id'];
    $exito = eliminarCategoria($conexion, $id_categoria);
}

if ($exito) {
    header("Location: ../vistas/categorias.php?status=deleted");
} else {
    // Falla si la llave foránea (FK) en 'productos' está en uso
    header("Location: ../vistas/categorias.php?status=delete_error");
}
exit();
?>