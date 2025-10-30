<?php
// Ubicación: controladores/categoria_controlador.php
// Objetivo: Manejar CRUD de Categorías por AJAX y devolver JSON.

include_once "../conexion.php";
include_once "../modelos/categoria_modelo.php"; 

$respuesta = ['exito' => false, 'mensaje' => 'Operación no válida o método incorrecto.'];
$accion = $_REQUEST['accion'] ?? '';

switch ($accion) {
    
    // --- OBTENER DATOS PARA EDICIÓN (AJAX GET) ---
    case 'obtener_por_id':
        if (isset($_GET['id_categoria'])) {
            $datos_categoria = obtenerCategoriaPorID($conexion, $_GET['id_categoria']);
            if ($datos_categoria) {
                $respuesta['exito'] = true;
                $respuesta['datos'] = $datos_categoria; 
            } else {
                $respuesta['mensaje'] = 'Categoría no encontrada.';
            }
        }
        break;

    // --- REGISTRAR NUEVA CATEGORÍA (AJAX POST) ---
    case 'registrar':
        if (isset($_POST['nombre'])) {
            $datos = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? ''
            ];
            
            $resultado = registrarCategoria($conexion, $datos);
            
            if ($resultado === true) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Categoría registrada correctamente.';
            } else {
                $respuesta['mensaje'] = $resultado === "name_exists" ? "El nombre de la categoría ya existe." : "Error al registrar: " . $resultado;
            }
        }
        break;

    // --- ACTUALIZAR CATEGORÍA EXISTENTE (AJAX POST) ---
    case 'editar':
        if (isset($_POST['id_categoria']) && isset($_POST['nombre'])) {
            $datos = [
                'id_categoria' => $_POST['id_categoria'],
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? '',
                'estado' => $_POST['estado'] // Asumiendo que la edición incluye el estado
            ];

            $resultado = actualizarCategoria($conexion, $datos);
            
            if ($resultado === true) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = "Categoría actualizada correctamente.";
            } else {
                $respuesta['mensaje'] = $resultado === "name_exists" ? "El nombre de la categoría ya existe." : "Error al actualizar: " . $resultado;
            }
        }
        break;
        
    // --- ELIMINAR CATEGORÍA (AJAX GET) ---
    case 'eliminar':
        if (isset($_GET['id_categoria'])) {
            $id = $_GET['id_categoria'];
            if (eliminarCategoria($conexion, $id)) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'La categoría ha sido eliminada.';
            } else {
                $respuesta['mensaje'] = 'No se pudo eliminar la categoría. Puede que esté asociada a productos.';
            }
        }
        break;
}

header('Content-Type: application/json');
echo json_encode($respuesta);
exit();
?>