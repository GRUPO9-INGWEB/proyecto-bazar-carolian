<?php
// Ubicación: controladores/procesar_compra.php

// Iniciamos sesión para obtener el id_usuario
session_start(); 

include_once "../conexion.php";
include_once "../modelos/compra_modelo.php";

// Verificar que el usuario esté logueado y sea admin (o rol permitido)
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['usuario_id'])) {
    header("Location: ../vistas/login.php?status=error_permiso");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Recoger datos de la cabecera
    $datos_compra = [
        'id_proveedor' => $_POST['id_proveedor'],
        'tipo_comprobante' => $_POST['tipo_comprobante'],
        'numero_comprobante' => $_POST['numero_comprobante'],
        'subtotal' => $_POST['subtotal'],
        'igv' => $_POST['igv'],
        'total' => $_POST['total']
    ];

    // 2. Recoger los items del carrito (vienen como un JSON string)
    $carrito_json = $_POST['carrito'];
    $carrito = json_decode($carrito_json, true); // Convertir a array PHP

    // 3. Validar que tengamos todos los datos
    if (empty($datos_compra['id_proveedor']) || empty($carrito)) {
        // ⭐ ¡CORRECCIÓN! Usar 'page=registrar_compra'
        header("Location: ../vistas/dashboard_admin.php?page=registrar_compra&status=error_datos");
        exit;
    }

    // --- INICIO DE LA TRANSACCIÓN ---
    $conexion->begin_transaction();

    try {
        // 4. Registrar la cabecera de la compra
        $id_compra = registrarCompra($conexion, $datos_compra);
        
        if (!$id_compra) {
            throw new Exception("Error al guardar la cabecera de la compra.");
        }

        // 5. Registrar cada item del detalle de la compra
        foreach ($carrito as $item) {
            $exito_detalle = registrarDetalleCompra($conexion, $id_compra, $item);
            if (!$exito_detalle) {
                throw new Exception("Error al guardar el detalle del producto ID: " . $item['id']);
            }
        }

        // 6. Si todo salió bien: COMMIT
        $conexion->commit();
        
        // ⭐ ¡CORRECCIÓN! Usar 'page=registrar_compra'
        header("Location: ../vistas/dashboard_admin.php?page=registrar_compra&status=success");
        exit;

    } catch (Exception $e) {
        // 7. Si algo salió mal: ROLLBACK
        $conexion->rollback();
        
        // ⭐ ¡CORRECCIÓN! Usar 'page=registrar_compra'
        header("Location: ../vistas/dashboard_admin.php?page=registrar_compra&status=error");
        exit;
    }

} else {
    // Si no es POST, redirigir a la vista de compras general
    // ⭐ ¡CORRECCIÓN! Usar 'page=ventas' o la vista que muestre la lista de compras/ventas
    header("Location: ../vistas/dashboard_admin.php?page=ventas");
    exit;
}
?>