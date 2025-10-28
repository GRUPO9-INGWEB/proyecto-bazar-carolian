<?php

/**
 * Función para registrar un nuevo producto en la BD
 * ¡CORREGIDA CON TRY...CATCH!
 */
function registrarProducto($conexion, $datos) {
    // Si el código está vacío, lo guardamos como NULL
    $codigo = !empty($datos['codigo']) ? $datos['codigo'] : null;

    $stmt = $conexion->prepare("INSERT INTO productos (id_categoria, codigo, nombre, descripcion, precio_venta, stock) 
                                VALUES (?, ?, ?, ?, ?, ?)");
    
    // "isssdi" = integer, string, string, string, decimal, integer
    $stmt->bind_param("isssdi", 
        $datos['id_categoria'], 
        $codigo,
        $datos['nombre'], 
        $datos['descripcion'], 
        $datos['precio'], 
        $datos['stock']
    );
    
    // Usamos try...catch para "atrapar" el error de código duplicado
    try {
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { // 1062 = Error de entrada duplicada (UNIQUE)
            return "code_exists";
        }
        return false; // Otro error
    }
}

/**
 * Función para obtener TODOS los productos
 */
function obtenerProductos($conexion) {
    $sql = "SELECT 
                p.id_producto,
                p.codigo, 
                p.nombre AS nombre_producto,
                p.descripcion,
                p.precio_venta,
                p.stock,
                c.nombre AS nombre_categoria 
            FROM productos p
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
            ORDER BY p.nombre ASC";
            
    $resultado = $conexion->query($sql);
    return $resultado;
}

/**
 * Función para eliminar un producto por su ID
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

/**
 * Función para OBTENER un producto específico por su ID
 */
function obtenerProductoPorID($conexion, $id) {
    $stmt = $conexion->prepare("SELECT id_producto, id_categoria, codigo, nombre, descripcion, precio_venta, stock 
                                FROM productos 
                                WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}

/**
 * Función para ACTUALIZAR un producto
 * ¡CORREGIDA CON TRY...CATCH!
 */
function actualizarProducto($conexion, $datos) {
    // Si el código está vacío, lo guardamos como NULL
    $codigo = !empty($datos['codigo']) ? $datos['codigo'] : null;

    $stmt = $conexion->prepare("UPDATE productos SET 
                                    id_categoria = ?, 
                                    codigo = ?, 
                                    nombre = ?, 
                                    descripcion = ?, 
                                    precio_venta = ?, 
                                    stock = ? 
                                WHERE id_producto = ?");
    
    // "isssdii" = integer, string, string, string, decimal, integer, integer
    $stmt->bind_param("isssdii", 
        $datos['id_categoria'],
        $codigo,
        $datos['nombre'],
        $datos['descripcion'],
        $datos['precio'],
        $datos['stock'],
        $datos['id_producto']
    );
    
    // Usamos try...catch para "atrapar" el error de código duplicado
    try {
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            return "code_exists";
        }
        return false; // Otro error
    }
}

?>