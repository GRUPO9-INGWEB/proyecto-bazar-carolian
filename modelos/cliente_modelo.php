<?php
// Ubicación: modelos/cliente_modelo.php (FINAL CON ESTADO)

/**
 * Registra un nuevo cliente en la base de datos
 */
function registrarCliente($conexion, $datos) {
    $sql = "INSERT INTO clientes (
                nombre_completo, documento_tipo, documento_numero, 
                direccion, email, telefono, estado
            ) VALUES (?, ?, ?, ?, ?, ?, 1)"; // Estado por defecto 1 (Activo)
    
    $stmt = $conexion->prepare($sql);
    
    // El tipo de parámetro debe ser "ssssss" (string x6)
    $stmt->bind_param(
        "ssssss", 
        $datos['nombre_completo'], 
        $datos['documento_tipo'], 
        $datos['documento_numero'], 
        $datos['direccion'], 
        $datos['email'], 
        $datos['telefono']
    );

    if ($stmt->execute()) {
        $id_cliente = $conexion->insert_id;
        $stmt->close();
        return $id_cliente;
    } else {
        $stmt->close();
        return 0;
    }
}

/**
 * Obtiene todos los clientes (activos e inactivos) para la tabla.
 */
function obtenerClientes($conexion) {
    // 🌟 CORRECCIÓN 1: INCLUIR EL CAMPO 'ESTADO' EN LA CONSULTA 🌟
    // Se elimina el WHERE estado = 1 para que la tabla muestre todos y el estado.
    $sql = "SELECT id_cliente, nombre_completo, documento_tipo, documento_numero, direccion, telefono, email, estado
             FROM clientes 
             ORDER BY nombre_completo ASC";
    return $conexion->query($sql);
}

/**
 * Obtiene los datos de un cliente por su ID
 */
function obtenerClientePorId($conexion, $id_cliente) {
    // 🌟 CORRECCIÓN 2: INCLUIR EL CAMPO 'ESTADO' PARA EL MODAL DE EDICIÓN 🌟
    $sql = "SELECT id_cliente, nombre_completo, documento_tipo, documento_numero, direccion, email, telefono, estado
             FROM clientes 
             WHERE id_cliente = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Actualiza la información de un cliente (incluye el estado)
 */
function actualizarCliente($conexion, $datos) {
    // 🌟 CORRECCIÓN 3.1: Leer el estado y mapearlo a 1/0 🌟
    $estado = $datos['estado'];
    $estado_db = ($estado == 'A') ? 1 : 0; // Mapeo A='1', I='0'

    // 🌟 CORRECCIÓN 3.2: Incluir 'estado = ?' en la sentencia SQL 🌟
    $sql = "UPDATE clientes SET 
                nombre_completo = ?, documento_tipo = ?, documento_numero = ?, 
                direccion = ?, email = ?, telefono = ?, estado = ? 
            WHERE id_cliente = ?";
    
    $stmt = $conexion->prepare($sql);
    
    // 🌟 CORRECCIÓN 3.3: Añadir el estado_db al bind_param 🌟
    // Tipos: s (nombre) s (tipo doc) s (num doc) s (dir) s (email) s (tel) i (estado) i (id)
    $stmt->bind_param(
        "ssssssii", 
        $datos['nombre_completo'], 
        $datos['documento_tipo'], 
        $datos['documento_numero'], 
        $datos['direccion'], 
        $datos['email'], 
        $datos['telefono'],
        $estado_db, // Valor 1 o 0
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