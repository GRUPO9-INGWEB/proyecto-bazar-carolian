<?php
include_once "../conexion.php";
include_once "../modelos/cliente_modelo.php";
$respuesta = ['exito' => false];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datos = [
        'tipo_doc' => $_POST['modal_tipo_doc'],
        'num_doc' => $_POST['modal_num_doc'],
        'nombre' => $_POST['modal_nombre'],
        'email' => $_POST['modal_email'],
        'direccion' => $_POST['modal_direccion'],
        'telefono' => $_POST['modal_telefono']
    ];
    $nuevo_id = registrarCliente($conexion, $datos);
    if ($nuevo_id) {
        $respuesta['exito'] = true;
        $respuesta['id_cliente'] = $nuevo_id;
        $respuesta['nombre'] = $datos['nombre'];
    } else {
        $respuesta['mensaje'] = "Error al registrar. Es posible que el documento ya exista.";
    }
}
echo json_encode($respuesta);
?>