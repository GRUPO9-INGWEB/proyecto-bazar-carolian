<?php
// Ubicación: modelos/cliente_modelo.php (FINAL)

/**
 * Registra un nuevo cliente en la base de datos
 */
function registrarCliente($conexion, $datos) {
    $sql = "INSERT INTO clientes (
                nombre_completo, documento_tipo, documento_numero, 
                direccion, email, telefono, estado
            ) VALUES (?, ?, ?, ?, ?, ?, 1)";
    
    $stmt = $conexion->prepare($sql);
    
    $stmt->bind_param(
        "ssssss", 
        $datos['nombre_completo'], 
        $datos['documento_tipo'], // CAMPO: documento_tipo
        $datos['documento_numero'], // CAMPO: documento_numero
        $datos['direccion'], 
        $datos['email'], 
        $datos['telefono']
    );

    if ($stmt->execute()) {
        $id_cliente = $conexion->insert_id;
        $stmt->close();
        return $id_cliente;
    } else {
        // Cierra el statement antes de devolver 0 si falla
        $stmt->close();
        return 0;
    }
}

/**
 * Obtiene todos los clientes activos
 */
function obtenerClientes($conexion) {
    $sql = "SELECT id_cliente, nombre_completo, documento_tipo, documento_numero, direccion, telefono, email 
            FROM clientes 
            WHERE estado = 1 
            ORDER BY nombre_completo ASC";
    return $conexion->query($sql);
}

/**
 * Obtiene los datos de un cliente por su ID
 */
function obtenerClientePorId($conexion, $id_cliente) {
    $sql = "SELECT id_cliente, nombre_completo, documento_tipo, documento_numero, direccion, email, telefono 
            FROM clientes 
            WHERE id_cliente = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Actualiza la información de un cliente
 */
function actualizarCliente($conexion, $datos) {
    $sql = "UPDATE clientes SET 
                nombre_completo = ?, documento_tipo = ?, documento_numero = ?, 
                direccion = ?, email = ?, telefono = ? 
            WHERE id_cliente = ?";
    
    $stmt = $conexion->prepare($sql);
    
    $stmt->bind_param(
        "ssssssi", 
        $datos['nombre_completo'], 
        $datos['documento_tipo'], 
        $datos['documento_numero'], 
        $datos['direccion'], 
        $datos['email'], 
        $datos['telefono'],
        $datos['id_cliente']
    );

    return $stmt->execute();
}

/**
 * Elimina lógicamente un cliente (cambia el estado a 0)
 */
function eliminarLogicoCliente($conexion, $id_cliente) {
    $sql = "UPDATE clientes SET estado = 0 WHERE id_cliente = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    return $stmt->execute();
}
?>