<?php
include_once "../conexion.php";
include_once "../modelos/cliente_modelo.php";
$respuesta = ['encontrado' => false];
if (isset($_GET['tipo_doc']) && isset($_GET['num_doc'])) {
    $cliente = buscarCliente($conexion, $_GET['tipo_doc'], $_GET['num_doc']);
    if ($cliente) {
        $respuesta['encontrado'] = true;
        $respuesta['cliente'] = $cliente;
    }
}
echo json_encode($respuesta);
?>