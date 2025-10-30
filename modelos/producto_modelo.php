<?php

/**
 * Función para registrar un nuevo producto en la BD
 * ¡VERSIÓN FINAL!
 */
function registrarProducto($conexion, $datos) {
    $codigo = !empty($datos['codigo']) ? $datos['codigo'] : null;
    $fecha_caducidad = !empty($datos['fecha_caducidad']) ? $datos['fecha_caducidad'] : null;

    // Se añade 'estado' y 'fecha_caducidad'.
    // 'stock' y 'precio_costo' se insertan como 0.
    $stmt = $conexion->prepare("INSERT INTO productos (id_categoria, codigo, nombre, descripcion, precio_venta, fecha_caducidad, estado, precio_costo, stock) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0)");
    
    // "isssdsi" = integer, string, string, string, decimal, string(fecha), integer(estado)
    $stmt->bind_param("isssdsi", 
        $datos['id_categoria'], 
        $codigo,
        $datos['nombre'], 
        $datos['descripcion'], 
        $datos['precio'],
        $fecha_caducidad,
        $datos['estado']
    );
    
    try {
        if ($stmt->execute()) { return true; } else { return false; }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { return "code_exists"; }
        return false;
    }
}

/**
 * Función para obtener TODOS los productos
 * ¡VERSIÓN FINAL!
 */
function obtenerProductos($conexion) {
    $sql = "SELECT 
                 p.id_producto, p.codigo, p.nombre AS nombre_producto,
                 p.descripcion, p.precio_venta, p.precio_costo,
                 p.fecha_caducidad, p.stock, p.estado,
                 c.nombre AS nombre_categoria 
             FROM productos p
             LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
             ORDER BY p.nombre ASC";
    $resultado = $conexion->query($sql);
    return $resultado;
}

/**
 * Función para OBTENER un producto específico por su ID
 * ¡VERSIÓN FINAL!
 */
function obtenerProductoPorID($conexion, $id) {
    $stmt = $conexion->prepare("SELECT id_producto, id_categoria, codigo, nombre, 
                                       descripcion, precio_venta, precio_costo, 
                                       stock, fecha_caducidad, estado
                                 FROM productos 
                                 WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}

/**
 * Función para ACTUALIZAR un producto
 * ¡VERSIÓN FINAL!
 */
function actualizarProducto($conexion, $datos) {
    $codigo = !empty($datos['codigo']) ? $datos['codigo'] : null;
    $fecha_caducidad = !empty($datos['fecha_caducidad']) ? $datos['fecha_caducidad'] : null;

    // Se actualiza 'estado' y 'fecha_caducidad'.
    // 'stock' NO se actualiza.
    $stmt = $conexion->prepare("UPDATE productos SET 
                                     id_categoria = ?, 
                                     codigo = ?, 
                                     nombre = ?, 
                                     descripcion = ?, 
                                     precio_venta = ?,
                                     fecha_caducidad = ?,
                                     estado = ? 
                                 WHERE id_producto = ?");
    
    // "isssdsii" = ..., integer(estado), integer(id)
    $stmt->bind_param("isssdsii", 
        $datos['id_categoria'],
        $codigo,
        $datos['nombre'],
        $datos['descripcion'],
        $datos['precio'],
        $fecha_caducidad,
        $datos['estado'],
        $datos['id_producto']
    );
    
    try {
        if ($stmt->execute()) { return true; } else { return false; }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { return "code_exists"; }
        return false;
    }
}

/**
 * Función para eliminar un producto (Sin cambios)
 */
function eliminarProducto($conexion, $id) {
    $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
?>