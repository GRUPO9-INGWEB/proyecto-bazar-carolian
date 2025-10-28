<?php
// Controlador FINAL para registrar la Venta
include_once "../conexion.php";
include_once "../modelos/venta_modelo.php";

session_start();
// Preparamos la respuesta que enviaremos al JavaScript
header('Content-Type: application/json');
$respuesta = ['exito' => false, 'mensaje' => 'Error desconocido.'];

// 1. Recoger datos del cliente
$cliente_datos = [
    'id_cliente' => !empty($_POST['id_cliente_oculto']) ? $_POST['id_cliente_oculto'] : null,
    'tipo_comprobante' => $_POST['tipo_comprobante'],
    'metodo_pago' => $_POST['metodo_pago']
];

// 2. Recoger montos
$montos = [
    'subtotal' => $_POST['subtotal'],
    'igv' => $_POST['igv'],
    'total' => $_POST['total'],
    'monto_recibido' => !empty($_POST['monto_recibido']) ? $_POST['monto_recibido'] : null,
    'vuelto' => !empty($_POST['vuelto']) ? $_POST['vuelto'] : null
];

// 3. Recoger ID de usuario (Asumimos 1, luego vendrá del login)
$id_usuario = 1; 

// 4. Recoger el carrito (viene como un texto JSON)
$carrito = json_decode($_POST['carrito_data'], true);

// 5. Validar que el carrito no esté vacío
if (empty($carrito)) {
    $respuesta['mensaje'] = 'No se puede registrar una venta sin productos.';
    echo json_encode($respuesta);
    exit();
}

// 6. Llamar al modelo
$id_nueva_venta = registrarVenta($conexion, $id_usuario, $cliente_datos, $montos, $carrito);

// 7. Preparar la respuesta final
if ($id_nueva_venta > 0) {
    // ¡ÉXITO!
    $respuesta['exito'] = true;
    $respuesta['mensaje'] = '¡Venta registrada exitosamente!';
    $respuesta['id_venta'] = $id_nueva_venta; // Enviamos el ID al JS
} else {
    // ¡ERROR!
    $respuesta['mensaje'] = 'Error al registrar la venta. Verifique el stock.';
}

echo json_encode($respuesta);
exit();
?>