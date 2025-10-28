<?php

/**
 * Función para registrar una venta completa
 * ¡CAMBIO! Ahora devuelve el ID de la venta o 0 si falla.
 */
function registrarVenta($conexion, $id_usuario, $cliente_datos, $montos, $carrito) {
    
    // 1. Iniciar la transacción
    $conexion->begin_transaction();

    try {
        // 2. Registrar la venta principal
        $sql_venta = "INSERT INTO ventas (
                        id_usuario, id_cliente, tipo_comprobante, 
                        metodo_pago, subtotal, igv, total,
                        monto_recibido, vuelto
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_venta = $conexion->prepare($sql_venta);
        
        $stmt_venta->bind_param("iisssdddd", 
            $id_usuario, $cliente_datos['id_cliente'], $cliente_datos['tipo_comprobante'],
            $cliente_datos['metodo_pago'], $montos['subtotal'], $montos['igv'],
            $montos['total'], $montos['monto_recibido'], $montos['vuelto']
        );
        $stmt_venta->execute();
        
        // 3. Obtener el ID de la venta que acabamos de crear
        $id_venta = $conexion->insert_id;

        // 4. Preparar las consultas para los detalles y el stock
        $sql_detalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
        $stmt_detalle = $conexion->prepare($sql_detalle);

        $sql_stock = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
        $stmt_stock = $conexion->prepare($sql_stock);

        // 5. Recorrer el carrito y registrar cada producto
        foreach ($carrito as $producto) {
            $id_prod = $producto['id'];
            $cantidad = $producto['cantidad'];
            $precio = $producto['precio']; 

            $stmt_detalle->bind_param("iiid", $id_venta, $id_prod, $cantidad, $precio);
            $stmt_detalle->execute();
            $stmt_stock->bind_param("ii", $cantidad, $id_prod);
            $stmt_stock->execute();
        }

        // 6. Si todo salió bien, confirmamos la transacción
        $conexion->commit();
        return $id_venta; // ¡CAMBIO! Devolvemos el ID de la venta

    } catch (Exception $e) {
        // 7. Si algo falló, revertimos todo
        $conexion->rollback();
        return 0; // ¡CAMBIO! Devolvemos 0 en caso de error
    }
}


/* * ======================================================
 * ¡NUEVAS FUNCIONES PARA LEER LA VENTA Y GENERAR EL PDF!
 * ======================================================
 */

/**
 * Obtiene los datos principales de una venta y del cliente (si existe)
 */
function obtenerVentaCompleta($conexion, $id_venta) {
    $sql = "SELECT 
                v.*, 
                u.nombre_completo AS nombre_vendedor,
                c.nombre_completo AS nombre_cliente,
                c.documento_tipo,
                c.documento_numero,
                c.direccion AS direccion_cliente,
                c.email AS email_cliente
            FROM ventas v
            JOIN usuarios u ON v.id_usuario = u.id_usuario
            LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
            WHERE v.id_venta = ?";
            
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_venta);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}

/**
 * Obtiene los productos (detalles) de una venta específica
 */
function obtenerDetalleVenta($conexion, $id_venta) {
    $sql = "SELECT 
                d.cantidad,
                d.precio_unitario,
                p.nombre AS nombre_producto,
                p.codigo AS codigo_producto
            FROM detalle_ventas d
            JOIN productos p ON d.id_producto = p.id_producto
            WHERE d.id_venta = ?";
            
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_venta);
    $stmt->execute();
    return $stmt->get_result();
}

?>