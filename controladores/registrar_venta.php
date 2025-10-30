<?php
include_once "../conexion.php";
include_once "../modelos/venta_modelo.php";

session_start();
header('Content-Type: application/json');
$respuesta = ['exito' => false, 'mensaje' => 'Error desconocido.'];

// 1. Validar Sesión
if (!isset($_SESSION['usuario_id'])) {
    $respuesta['mensaje'] = 'Error: Sesión no válida. Inicie sesión de nuevo.';
    echo json_encode($respuesta);
    exit();
}

// 2. Recoger datos 
$cliente_datos = [
    'id_cliente' => !empty($_POST['id_cliente_oculto']) ? $_POST['id_cliente_oculto'] : null,
    'tipo_comprobante' => $_POST['tipo_comprobante'],
    'metodo_pago' => $_POST['metodo_pago'],
    'observaciones' => $_POST['observaciones'] ?? '' 
];

$montos = [
    'subtotal' => $_POST['subtotal'],
    'igv' => $_POST['igv'],
    'total' => $_POST['total'],
    'monto_recibido' => !empty($_POST['monto_recibido']) ? $_POST['monto_recibido'] : null,
    'vuelto' => !empty($_POST['vuelto']) ? $_POST['vuelto'] : null
];

$id_usuario = $_SESSION['usuario_id'];
$carrito = json_decode($_POST['carrito_data'], true);

// 3. Validar Carrito
if (empty($carrito)) {
    $respuesta['mensaje'] = 'No se puede registrar una venta sin productos.';
    echo json_encode($respuesta);
    exit();
}

// 4. Llamar al modelo
$id_nueva_venta = registrarVenta($conexion, $id_usuario, $cliente_datos, $montos, $carrito);

// 5. Preparar la respuesta final (con el ID de venta para el PDF)
if ($id_nueva_venta > 0) {
    $respuesta['exito'] = true;
    $respuesta['mensaje'] = '¡Venta registrada exitosamente!';
    $respuesta['id_venta'] = $id_nueva_venta; 
} else {
    $respuesta['mensaje'] = 'Error al registrar la venta en la base de datos.';
}

echo json_encode($respuesta);
exit();
?>