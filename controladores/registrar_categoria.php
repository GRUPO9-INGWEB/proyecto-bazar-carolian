<?php
// 1. Incluimos los archivos necesarios
// (Usamos ../ para "subir un nivel" y encontrar los archivos)
include_once "../conexion.php"; 
include_once "../modelos/categoria_modelo.php"; 

// 2. Verificamos que los datos se hayan enviado por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3. Obtenemos los datos del formulario
    $nombre = $_POST['nombre_categoria'];
    $descripcion = $_POST['desc_categoria'];

    // 4. Llamamos a la función del modelo para registrar
    $exito = registrarCategoria($conexion, $nombre, $descripcion);

    // 5. Redirigimos al usuario de vuelta a la página de categorías
    // ¡AQUÍ ESTÁ LA CORRECCIÓN! Lo redirigimos a /vistas/
    if ($exito) {
        header("Location: ../vistas/categorias.php?status=success");
    } else {
        header("Location: ../vistas/categorias.php?status=error");
    }
    exit(); // Importante: detener la ejecución después de redirigir
}
?>