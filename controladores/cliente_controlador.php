<?php
// Ubicación: controladores/cliente_controlador.php

include_once "../conexion.php";
include_once "../modelos/cliente_modelo.php";

$respuesta = [
    'exito' => false,
    'mensaje' => 'Operación no válida.'
];

$accion = $_REQUEST['accion'] ?? '';

switch ($accion) {
    case 'registrar':
    case 'editar':
        // Verifica que todos los campos del formulario estén presentes
        if (
            isset($_POST['nombre_completo']) &&
            isset($_POST['documento_numero']) &&
            isset($_POST['documento_tipo'])
        ) {
            $datos = [
                'id_cliente' => $_POST['id_cliente'] ?? null,
                'nombre_completo' => $_POST['nombre_completo'],
                'documento_tipo' => $_POST['documento_tipo'], // CORREGIDO
                'documento_numero' => $_POST['documento_numero'], // CORREGIDO
                'telefono' => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'email' => $_POST['email'] ?? '' // Añadido
            ];

            if ($accion === 'registrar') {
                $id = registrarCliente($conexion, $datos);
                if ($id > 0) {
                    $respuesta['exito'] = true;
                    $respuesta['mensaje'] = 'Cliente registrado exitosamente.';
                } else {
                    $respuesta['mensaje'] = 'Error al registrar el cliente.';
                }
            } else { // editar
                if (actualizarCliente($conexion, $datos)) {
                    $respuesta['exito'] = true;
                    $respuesta['mensaje'] = 'Cliente actualizado exitosamente.';
                } else {
                    $respuesta['mensaje'] = 'Error al actualizar el cliente.';
                }
            }
        } else {
            $respuesta['mensaje'] = 'Faltan datos requeridos.';
        }
        break;

    case 'eliminar':
        if (isset($_POST['id_cliente'])) {
            $id_cliente = $_POST['id_cliente'];
            if (eliminarLogicoCliente($conexion, $id_cliente)) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Cliente eliminado lógicamente.';
            } else {
                $respuesta['mensaje'] = 'Error al eliminar el cliente.';
            }
        } else {
            $respuesta['mensaje'] = 'ID de cliente no proporcionado.';
        }
        break;

    case 'obtener_por_id':
        if (isset($_GET['id_cliente'])) {
            $id_cliente = $_GET['id_cliente'];
            $datos_cliente = obtenerClientePorId($conexion, $id_cliente);
            
            if ($datos_cliente) {
                $respuesta['exito'] = true;
                $respuesta['datos'] = $datos_cliente;
            } else {
                $respuesta['mensaje'] = 'Cliente no encontrado.';
            }
        } else {
            $respuesta['mensaje'] = 'ID de cliente no proporcionado.';
        }
        break;

    default:
        // Si no es una petición AJAX, el script termina aquí
        exit(); 
}

header('Content-Type: application/json');
echo json_encode($respuesta);
?>