<?php
// Ubicación: controladores/usuario_controlador.php

// 1. Incluir la conexión y el modelo de usuarios
include_once "../conexion.php";
include_once "../modelos/usuario_modelo.php";

$respuesta = [
    'exito' => false,
    'mensaje' => 'Operación no válida.'
];

// Obtener la acción (registrar, editar, eliminar, obtener_por_id)
$accion = $_REQUEST['accion'] ?? '';

switch ($accion) {
    case 'registrar':
        if (
            isset($_POST['nombre_completo']) &&
            isset($_POST['email']) &&
            isset($_POST['password']) &&
            isset($_POST['id_rol'])
        ) {
            // Recoger datos
            $datos = [
                'nombre_completo' => $_POST['nombre_completo'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'id_rol' => $_POST['id_rol'],
                'dni' => $_POST['dni'] ?? '',
                'telefono' => $_POST['telefono'] ?? ''
            ];

            if (registrarUsuario($conexion, $datos)) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Usuario registrado exitosamente.';
            } else {
                $respuesta['mensaje'] = 'Error al registrar el usuario: ' . $conexion->error;
            }
        } else {
            $respuesta['mensaje'] = 'Faltan datos requeridos para el registro.';
        }
        break;

    case 'editar':
        if (
            isset($_POST['id_usuario']) &&
            isset($_POST['nombre_completo']) &&
            isset($_POST['email']) &&
            isset($_POST['id_rol'])
        ) {
            // Recoger datos
            $datos = [
                'id_usuario' => $_POST['id_usuario'],
                'nombre_completo' => $_POST['nombre_completo'],
                'email' => $_POST['email'],
                'password' => $_POST['password'] ?? '', // Puede estar vacío si no se cambia
                'id_rol' => $_POST['id_rol'],
                'dni' => $_POST['dni'] ?? '',
                'telefono' => $_POST['telefono'] ?? ''
            ];

            if (actualizarUsuario($conexion, $datos)) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Usuario actualizado exitosamente.';
            } else {
                $respuesta['mensaje'] = 'Error al actualizar el usuario: ' . $conexion->error;
            }
        } else {
            $respuesta['mensaje'] = 'Faltan datos requeridos para la edición.';
        }
        break;

    case 'eliminar':
        if (isset($_POST['id_usuario'])) {
            $id_usuario = $_POST['id_usuario'];
            
            if (eliminarLogicoUsuario($conexion, $id_usuario)) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Usuario eliminado lógicamente.';
            } else {
                $respuesta['mensaje'] = 'Error al eliminar el usuario: ' . $conexion->error;
            }
        } else {
            $respuesta['mensaje'] = 'ID de usuario no proporcionado.';
        }
        break;

    case 'obtener_por_id':
        if (isset($_GET['id_usuario'])) {
            $id_usuario = $_GET['id_usuario'];
            $datos_usuario = obtenerUsuarioPorId($conexion, $id_usuario);
            
            if ($datos_usuario) {
                $respuesta['exito'] = true;
                $respuesta['datos'] = $datos_usuario;
            } else {
                $respuesta['mensaje'] = 'Usuario no encontrado.';
            }
        } else {
            $respuesta['mensaje'] = 'ID de usuario no proporcionado.';
        }
        break;

    default:
        // Si no es una petición AJAX, el script termina aquí
        exit(); 
}

// Para peticiones AJAX, devolvemos la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($respuesta);
?>