<?php
// Ubicación: controladores/producto_controlador.php
// Objetivo: Manejar CRUD de Productos por AJAX y devolver JSON.

include_once "../conexion.php";
include_once "../modelos/producto_modelo.php"; 

$respuesta = ['exito' => false, 'mensaje' => 'Operación no válida o método incorrecto.'];

// Usa $_REQUEST para manejar GET (obtener/eliminar) y POST (registrar/editar)
$accion = $_REQUEST['accion'] ?? '';

switch ($accion) {
    
    // --- OBTENER DATOS PARA EDICIÓN (AJAX GET) ---
    case 'obtener_por_id':
        if (isset($_GET['id_producto'])) {
            $datos_producto = obtenerProductoPorID($conexion, $_GET['id_producto']);
            if ($datos_producto) {
                $respuesta['exito'] = true;
                // Devolvemos los datos tal cual vienen de la BD para que JavaScript los mapee
                $respuesta['datos'] = $datos_producto; 
            } else {
                $respuesta['mensaje'] = 'Producto no encontrado.';
            }
        }
        break;

    // --- REGISTRAR NUEVO PRODUCTO (AJAX POST) ---
    case 'registrar':
        if (isset($_POST['nombre_producto'])) {
            $datos = [
                'id_categoria' => $_POST['id_categoria'],
                'codigo' => $_POST['codigo_producto'],
                'nombre' => $_POST['nombre_producto'],
                'descripcion' => $_POST['desc_producto'] ?? '',
                'precio_producto' => $_POST['precio_producto'], // <--- CLAVE: Usa nombre del form
                'precio_costo' => $_POST['precio_costo'] ?? 0, 
                'stock' => $_POST['stock'] ?? 0, 
                'fecha_caducidad' => empty($_POST['fecha_caducidad']) ? null : $_POST['fecha_caducidad'],
                'estado' => $_POST['estado'] // <--- CLAVE: Usa nombre del form
            ];
            
            $resultado = registrarProducto($conexion, $datos); 
            
            if ($resultado === true) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Producto registrado correctamente.';
            } else {
                $respuesta['mensaje'] = $resultado === "code_exists" ? "El código de producto ya existe." : "Error al registrar: " . $resultado;
            }
        }
        break;

    // --- ACTUALIZAR PRODUCTO EXISTENTE (AJAX POST) ---
    case 'editar':
        if (isset($_POST['id_producto']) && isset($_POST['nombre_producto'])) {
            $datos = [
                'id_producto' => $_POST['id_producto'],
                'id_categoria' => $_POST['id_categoria'],
                'codigo' => $_POST['codigo_producto'],
                'nombre' => $_POST['nombre_producto'],
                'descripcion' => $_POST['desc_producto'] ?? '',
                'precio_producto' => $_POST['precio_producto'], // <--- CLAVE: Usa nombre del form
                'precio_costo' => $_POST['precio_costo'] ?? 0, 
                'fecha_caducidad' => empty($_POST['fecha_caducidad']) ? null : $_POST['fecha_caducidad'],
                'estado' => $_POST['estado'] // <--- CLAVE: Usa nombre del form
            ];

            $resultado = actualizarProducto($conexion, $datos);
            
            if ($resultado === true) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = "Producto actualizado correctamente.";
            } else {
                $respuesta['mensaje'] = $resultado === "code_exists" ? "El código de producto ya existe." : "Error al actualizar: " . $resultado;
            }
        }
        break;
        
    // --- ELIMINAR PRODUCTO (AJAX GET) ---
    case 'eliminar':
        if (isset($_GET['id_producto'])) {
            $id = $_GET['id_producto'];
            if (eliminarProducto($conexion, $id)) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'El producto ha sido eliminado.';
            } else {
                $respuesta['mensaje'] = 'No se pudo eliminar el producto.';
            }
        }
        break;
}

// SIEMPRE responde en formato JSON
header('Content-Type: application/json');
echo json_encode($respuesta);
exit();
?>