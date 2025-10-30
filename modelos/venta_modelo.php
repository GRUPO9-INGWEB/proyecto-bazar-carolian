<?php

/**
 * Función para registrar una venta completa
 * ¡CORREGIDA! Ya no descuenta el stock manualmente.
 */
function registrarVenta($conexion, $id_usuario, $cliente_datos, $montos, $carrito) {
    
    $conexion->begin_transaction();

    try {
        // 1. Registrar la venta principal (con los nuevos campos)
        $sql_venta = "INSERT INTO ventas (
                           id_usuario, id_cliente, tipo_comprobante, 
                           metodo_pago, subtotal, igv, total,
                           monto_recibido, vuelto, observaciones, estado
                       ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
        
        $stmt_venta = $conexion->prepare($sql_venta);
        
        $estado = 1; 
        $observaciones = $cliente_datos['observaciones'] ?? ''; 
        
        // El formato de parámetros debe ser: "iisssddddsi"
        $stmt_venta->bind_param("iisssddddsi", 
            $id_usuario, 
            $cliente_datos['id_cliente'], 
            $cliente_datos['tipo_comprobante'],
            $cliente_datos['metodo_pago'], 
            $montos['subtotal'], 
            $montos['igv'],
            $montos['total'], 
            $montos['monto_recibido'], 
            $montos['vuelto'],
            $observaciones, 
            $estado         
        );
        
        if (!$stmt_venta->execute()) {
             throw new Exception("Error al registrar la cabecera de la venta: " . $stmt_venta->error);
        }
        
        $id_venta = $conexion->insert_id;
        if ($id_venta == 0) {
            throw new Exception("No se pudo obtener el ID de la venta.");
        }

        // 2. Preparar la consulta para los detalles
        $sql_detalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
        $stmt_detalle = $conexion->prepare($sql_detalle);

        // 3. Recorrer el carrito y registrar detalles
        foreach ($carrito as $producto) {
            $id_prod = $producto['id'];
            $cantidad = $producto['cantidad'];
            $precio = $producto['precio']; 

            $stmt_detalle->bind_param("iiid", $id_venta, $id_prod, $cantidad, $precio);
            $stmt_detalle->execute();
        }

        // 4. Commit y cerrar
        $conexion->commit();
        $stmt_detalle->close();
        $stmt_venta->close();
        return $id_venta; 

    } catch (Exception $e) {
        $conexion->rollback();
        return 0; 
    }
}

// --- FUNCIONES REQUERIDAS POR GENERAR_COMPROBANTE.PHP ---

function obtenerVentaCompleta($conexion, $id_venta) {
    $sql = "SELECT 
                 v.*, 
                 u.nombre_completo AS nombre_vendedor,
                 c.nombre_completo AS nombre_cliente,
                 c.documento_tipo, c.documento_numero,
                 c.direccion AS direccion_cliente, c.email AS email_cliente
             FROM ventas v
             JOIN usuarios u ON v.id_usuario = u.id_usuario
             LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
             WHERE v.id_venta = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_venta);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function obtenerDetalleVenta($conexion, $id_venta) {
    $sql = "SELECT 
                 d.cantidad, d.precio_unitario,
                 p.nombre AS nombre_producto, p.codigo AS codigo_producto
             FROM detalle_ventas d
             JOIN productos p ON d.id_producto = p.id_producto
             WHERE d.id_venta = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_venta);
    $stmt->execute();
    return $stmt->get_result();
}

?>