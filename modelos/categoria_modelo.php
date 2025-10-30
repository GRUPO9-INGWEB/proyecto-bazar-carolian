<?php
// Ubicación: modelos/categoria_modelo.php

/**
 * Función para registrar una nueva categoría.
 */
function registrarCategoria($conexion, $datos) {
    $stmt = $conexion->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
    $stmt->bind_param("ss", $datos['nombre'], $datos['descripcion']);
    
    try {
        if ($stmt->execute()) { return true; } else { return $stmt->error; } 
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { return "name_exists"; } // Nombre duplicado
        return $e->getMessage();
    }
}

/**
 * Función para obtener TODAS las categorías.
 */
function obtenerCategorias($conexion) {
    $sql = "SELECT id_categoria, nombre, descripcion, estado FROM categorias ORDER BY nombre ASC";
    $resultado = $conexion->query($sql);
    return $resultado;
}

/**
 * Función para obtener una categoría específica por su ID. (CLAVE para la edición AJAX)
 */
function obtenerCategoriaPorID($conexion, $id) {
    $stmt = $conexion->prepare("SELECT id_categoria, nombre, descripcion, estado FROM categorias WHERE id_categoria = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}

/**
 * Función para actualizar una categoría.
 */
function actualizarCategoria($conexion, $datos) {
    $stmt = $conexion->prepare("UPDATE categorias SET nombre = ?, descripcion = ?, estado = ? WHERE id_categoria = ?");
    $stmt->bind_param("ssii", 
        $datos['nombre'], 
        $datos['descripcion'], 
        $datos['estado'], 
        $datos['id_categoria']
    );
    
    try {
        if ($stmt->execute()) { return true; } else { return $stmt->error; }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { return "name_exists"; } // Nombre duplicado
        return $e->getMessage();
    }
}

/**
 * Función para eliminar una categoría.
 */
function eliminarCategoria($conexion, $id) {
    $stmt = $conexion->prepare("DELETE FROM categorias WHERE id_categoria = ?");
    $stmt->bind_param("i", $id);
    
    try {
        if ($stmt->execute()) { return true; } else { return false; }
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}
?>