<?php
// Controlador para la búsqueda AJAX de productos
include_once "../conexion.php";

if (isset($_GET['term'])) {
    $termino = $_GET['term'];
    
    // Preparamos los términos de búsqueda
    $termino_nombre = $termino . '%'; // Para el LIKE (buscar por nombre)
    $termino_codigo = $termino;       // Para el Código (búsqueda exacta)

    /* * ¡CAMBIO IMPORTANTE! 
     * Ahora la consulta busca donde el NOMBRE coincida O donde el CÓDIGO sea exacto.
     */
    $stmt = $conexion->prepare("SELECT id_producto, codigo, nombre, precio_venta, stock 
                                FROM productos 
                                WHERE (nombre LIKE ? OR codigo = ?) AND stock > 0
                                LIMIT 5");
    
    // "ss" = pasamos dos strings
    $stmt->bind_param("ss", $termino_nombre, $termino_codigo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $productos = [];
    while ($fila = $resultado->fetch_assoc()) {
        // Damos formato a la respuesta que espera el JavaScript
        $fila['label'] = $fila['nombre'] . " (Cod: " . $fila['codigo'] . " | Stock: " . $fila['stock'] . ")";
        $fila['value'] = $fila['id_producto'];
        $productos[] = $fila;
    }
    
    echo json_encode($productos); // Devolvemos la respuesta en formato JSON
}
?>