<?php

/**
 * Función para registrar un nuevo proveedor
 */
function registrarProveedor($conexion, $datos) {
    // Preparamos los datos que pueden ser NULL
    $direccion = !empty($datos['direccion']) ? $datos['direccion'] : null;
    $telefono = !empty($datos['telefono']) ? $datos['telefono'] : null;
    $email = !empty($datos['email']) ? $datos['email'] : null;

    $stmt = $conexion->prepare("INSERT INTO proveedores (ruc, razon_social, direccion, telefono, email, estado) 
                                     VALUES (?, ?, ?, ?, ?, ?)");
    
    // "sssssi" = string, string, string, string, string, integer
    $stmt->bind_param("sssssi", 
        $datos['ruc'], 
        $datos['razon_social'],
        $direccion, 
        $telefono, 
        $email,
        $datos['estado']
    );
    
    try {
        if ($stmt->execute()) { return true; } else { return false; }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { // Error de RUC duplicado
            return "ruc_exists"; 
        }
        return false; // Otro error
    }
}

/**
 * Función para obtener TODOS los proveedores
 */
function obtenerProveedores($conexion) {
    $sql = "SELECT id_proveedor, ruc, razon_social, direccion, telefono, email, estado 
            FROM proveedores
            ORDER BY razon_social ASC";
    $resultado = $conexion->query($sql);
    return $resultado;
}

/**
 * Función para OBTENER un proveedor específico por su ID
 */
function obtenerProveedorPorID($conexion, $id) {
    $stmt = $conexion->prepare("SELECT id_proveedor, ruc, razon_social, direccion, telefono, email, estado
                                 FROM proveedores 
                                 WHERE id_proveedor = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}

/**
 * Función para ACTUALIZAR un proveedor
 */
function actualizarProveedor($conexion, $datos) {
    // Preparamos los datos que pueden ser NULL
    $direccion = !empty($datos['direccion']) ? $datos['direccion'] : null;
    $telefono = !empty($datos['telefono']) ? $datos['telefono'] : null;
    $email = !empty($datos['email']) ? $datos['email'] : null;

    $stmt = $conexion->prepare("UPDATE proveedores SET 
                                     ruc = ?, 
                                     razon_social = ?, 
                                     direccion = ?, 
                                     telefono = ?, 
                                     email = ?,
                                     estado = ? 
                                 WHERE id_proveedor = ?");
    
    // "sssssii" = string, string, string, string, string, integer, integer
    $stmt->bind_param("sssssii", 
        $datos['ruc'],
        $datos['razon_social'],
        $direccion,
        $telefono,
        $email,
        $datos['estado'],
        $datos['id_proveedor']
    );
    
    try {
        if ($stmt->execute()) { return true; } else { return false; }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            return "ruc_exists";
        }
        return false; // Otro error
    }
}

/**
 * Función para eliminar un proveedor por su ID
 */
function eliminarProveedor($conexion, $id) {
    $stmt = $conexion->prepare("DELETE FROM proveedores WHERE id_proveedor = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
?>