<?php
// controladores/buscar_producto_compra.php
// (Este es un NUEVO ARCHIVO, exclusivo para el módulo de Compras)

include_once "../conexion.php";

// 1. Leemos 'termino' que viene por POST (así lo manda el compras.js)
if (isset($_POST['termino'])) {
    $termino = $_POST['termino'];
    
    $termino_nombre = $termino . '%'; 
    $termino_codigo = $termino;
    
    // --- CAMBIOS IMPORTANTES ---
    // 2. AÑADIMOS 'precio_costo' al SELECT.
    // 3. QUITAMOS 'stock > 0' y lo cambiamos por 'estado = 1' (para no comprar productos inactivos).
    $stmt = $conexion->prepare("SELECT id_producto, codigo, nombre, precio_venta, stock, precio_costo
                                FROM productos 
                                WHERE (nombre LIKE ? OR codigo = ?) AND estado = 1
                                LIMIT 5");
    
    $stmt->bind_param("ss", $termino_nombre, $termino_codigo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $productos = [];
    while ($fila = $resultado->fetch_assoc()) {
        // 4. Devolvemos el objeto simple (no el formato label/value)
        // Le pasamos el ID como 'id' para que el JS lo entienda
        $fila['id'] = $fila['id_producto']; 
        $productos[] = $fila;
    }
    
    // Devolvemos la respuesta en formato JSON simple
    header('Content-Type: application/json');
    echo json_encode($productos); 
}
?>