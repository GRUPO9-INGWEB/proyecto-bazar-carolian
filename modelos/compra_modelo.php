<?php

/**
 * Función para registrar la cabecera de la compra
 * Devuelve el ID de la compra registrada para usarlo en los detalles.
 */
function registrarCompra($conexion, $datos) {
    
    // El usuario que está registrando la compra (el que está en sesión)
    $id_usuario = $_SESSION['usuario_id']; 

    $stmt = $conexion->prepare("INSERT INTO compras (id_usuario, id_proveedor, tipo_comprobante, numero_comprobante, subtotal, igv, total_compra, estado) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
    
    // i, i, s, s, d, d, d (integer, integer, string, string, decimal, decimal, decimal)
    $stmt->bind_param("iisssss", 
        $id_usuario,
        $datos['id_proveedor'],
        $datos['tipo_comprobante'],
        $datos['numero_comprobante'],
        $datos['subtotal'],
        $datos['igv'],
        $datos['total']
    );
    
    if ($stmt->execute()) {
        // Si la cabecera se guardó, devuelve el ID que se acaba de crear
        return $conexion->insert_id;
    } else {
        return false;
    }
}

/**
 * Función para registrar un item (producto) en el detalle de la compra
 * Esta función será llamada automáticamente por el Trigger.
 */
function registrarDetalleCompra($conexion, $id_compra, $item) {
    
    $stmt = $conexion->prepare("INSERT INTO detalle_compras (id_compra, id_producto, cantidad, precio_costo_unitario) 
                                     VALUES (?, ?, ?, ?)");
    
    // i, i, i, d (integer, integer, integer, decimal)
    $stmt->bind_param("iiid",
        $id_compra,
        $item['id'],
        $item['cantidad'],
        $item['precio_costo']
    );
    
    return $stmt->execute();
}
?>