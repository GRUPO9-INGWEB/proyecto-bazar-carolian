<?php

/**
 * Función para registrar un nuevo producto en la BD
 */
function registrarProducto($conexion, $datos) {
    // Preparamos la consulta
    $stmt = $conexion->prepare("INSERT INTO productos (id_categoria, nombre, descripcion, precio_venta, stock) 
                                VALUES (?, ?, ?, ?, ?)");
    
    // "issdi" = integer, string, string, decimal, integer
    $stmt->bind_param("issdi", 
        $datos['id_categoria'], 
        $datos['nombre'], 
        $datos['descripcion'], 
        $datos['precio'], 
        $datos['stock']
    );
    
    // Ejecutamos
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

/**
 * Función para obtener TODOS los productos con el NOMBRE de su categoría
 * Esta consulta usa un INNER JOIN para conectar las tablas
 */
function obtenerProductos($conexion) {
    $sql = "SELECT 
                p.id_producto,
                p.nombre AS nombre_producto,
                p.descripcion,
                p.precio_venta,
                p.stock,
                c.nombre AS nombre_categoria 
            FROM productos p
            INNER JOIN categorias c ON p.id_categoria = c.id_categoria
            ORDER BY p.nombre ASC";
            
    $resultado = $conexion->query($sql);
    return $resultado;
}

// --- NUEVA FUNCIÓN AÑADIDA ---

/**
 * Función para eliminar un producto por su ID
 */
function eliminarProducto($conexion, $id) {
    // Preparamos la consulta
    $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
    
    // "i" = integer
    $stmt->bind_param("i", $id);
    
    // Ejecutamos
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

/**
 * Función para OBTENER un producto específico por su ID
 * (Necesitamos id_categoria para el dropdown de edición)
 */
function obtenerProductoPorID($conexion, $id) {
    // Preparamos la consulta
    $stmt = $conexion->prepare("SELECT id_producto, id_categoria, nombre, descripcion, precio_venta, stock FROM productos WHERE id_producto = ?");
    
    // "i" = integer
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // Obtenemos el resultado
    $resultado = $stmt->get_result();
    
    // Devolvemos la fila (o null si no se encontró)
    return $resultado->fetch_assoc();
}

/**
 * Función para ACTUALIZAR un producto
 */
function actualizarProducto($conexion, $datos) {
    // Preparamos la consulta
    $stmt = $conexion->prepare("UPDATE productos 
                                SET id_categoria = ?, nombre = ?, descripcion = ?, precio_venta = ?, stock = ? 
                                WHERE id_producto = ?");
    
    // "issdii" = integer, string, string, decimal, integer, integer
    $stmt->bind_param("issdii", 
        $datos['id_categoria'], 
        $datos['nombre'], 
        $datos['descripcion'], 
        $datos['precio'], 
        $datos['stock'],
        $datos['id_producto']
    );
    
    // Ejecutamos
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

?>