<?php

// Función para registrar una nueva categoría
function registrarCategoria($conexion, $datos) {
    // Preparamos la consulta SQL
    $stmt = $conexion->prepare("INSERT INTO categorias (nombre, descripcion, estado) VALUES (?, ?, ?)");
    
    // "ssi" = string, string, integer (para el estado)
    $stmt->bind_param("ssi", 
        $datos['nombre'], 
        $datos['descripcion'],
        $datos['estado']
    );
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Función para obtener todas las categorías
function obtenerCategorias($conexion) {
    // Añadimos 'estado' al SELECT
    $sql = "SELECT id_categoria, nombre, descripcion, estado FROM categorias ORDER BY nombre ASC";
    $resultado = $conexion->query($sql);
    return $resultado;
}

// Función para eliminar una categoría por su ID
function eliminarCategoria($conexion, $id) {
    $stmt = $conexion->prepare("DELETE FROM categorias WHERE id_categoria = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        return true;
    } else {
        // Podría fallar si hay productos usando esta categoría (Error de Llave Foránea)
        return false;
    }
}

// Función para OBTENER una categoría específica por su ID
function obtenerCategoriaPorID($conexion, $id) {
    // Añadimos 'estado' al SELECT
    $stmt = $conexion->prepare("SELECT id_categoria, nombre, descripcion, estado FROM categorias WHERE id_categoria = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}

// Función para ACTUALIZAR una categoría
function actualizarCategoria($conexion, $datos) {
    // Añadimos 'estado = ?' al UPDATE
    $stmt = $conexion->prepare("UPDATE categorias SET nombre = ?, descripcion = ?, estado = ? WHERE id_categoria = ?");
    
    // "ssii" = string, string, integer, integer
    $stmt->bind_param("ssii", 
        $datos['nombre'], 
        $datos['descripcion'], 
        $datos['estado'],
        $datos['id_categoria']
    );
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

?>