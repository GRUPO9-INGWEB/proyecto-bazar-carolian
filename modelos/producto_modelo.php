<?php
// Ubicación: modelos/producto_modelo.php

/**
 * Función para registrar un nuevo producto en la BD (FINAL)
 */
function registrarProducto($conexion, $datos) {
    $codigo = !empty($datos['codigo']) ? $datos['codigo'] : null;
    $fecha_caducidad = !empty($datos['fecha_caducidad']) ? $datos['fecha_caducidad'] : null;

    $stmt = $conexion->prepare("INSERT INTO productos 
        (id_categoria, codigo, nombre, descripcion, precio_venta, precio_costo, stock, fecha_caducidad, estado) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // isssddisi: i(cat), s(cod), s(nom), s(desc), d(p_venta), d(p_costo), i(stock), s(fecha), i(estado)
    $stmt->bind_param("isssddisi", 
        $datos['id_categoria'], 
        $codigo,
        $datos['nombre'], 
        $datos['descripcion'], 
        $datos['precio_producto'], // <--- USA el nombre del formulario (precio_producto)
        $datos['precio_costo'], 
        $datos['stock'],        
        $fecha_caducidad,
        $datos['estado'] // <--- USA el nombre del formulario (estado)
    );
    
    try {
        if ($stmt->execute()) { return true; } else { return $stmt->error; } 
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { return "code_exists"; }
        return $e->getMessage();
    }
}

/**
 * Función para obtener TODOS los productos (SIN CAMBIOS)
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
 * Función para OBTENER un producto específico por su ID (SIN CAMBIOS)
 */
function obtenerProductoPorID($conexion, $id) {
    $stmt = $conexion->prepare("SELECT id_producto, id_categoria, codigo, nombre AS nombre_producto, 
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
 * Función para ACTUALIZAR un producto (FINAL)
 */
function actualizarProducto($conexion, $datos) {
    $codigo = !empty($datos['codigo']) ? $datos['codigo'] : null;
    $fecha_caducidad = !empty($datos['fecha_caducidad']) ? $datos['fecha_caducidad'] : null;

    // NO actualizamos 'stock', solo los datos administrativos/precio
    $stmt = $conexion->prepare("UPDATE productos SET 
                                   id_categoria = ?, 
                                   codigo = ?, 
                                   nombre = ?, 
                                   descripcion = ?, 
                                   precio_venta = ?,
                                   precio_costo = ?,
                                   fecha_caducidad = ?,
                                   estado = ? 
                                WHERE id_producto = ?");
    
    // isssddsii: i(cat), s(cod), s(nom), s(desc), d(p_venta), d(p_costo), s(fecha), i(estado), i(id)
    $stmt->bind_param("isssddsii", 
        $datos['id_categoria'],
        $codigo,
        $datos['nombre'],
        $datos['descripcion'],
        $datos['precio_producto'], // <--- USA el nombre del formulario (precio_producto)
        $datos['precio_costo'], 
        $fecha_caducidad,
        $datos['estado'], // <--- USA el nombre del formulario (estado)
        $datos['id_producto']
    );
    
    try {
        if ($stmt->execute()) { return true; } else { return $stmt->error; }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { return "code_exists"; }
        return $e->getMessage();
    }
}

/**
 * Función para eliminar un producto (FINAL)
 */
function eliminarProducto($conexion, $id) {
    $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    
    try {
        if ($stmt->execute()) { return true; } else { return false; }
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}
?>