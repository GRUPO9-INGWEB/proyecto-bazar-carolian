<?php

// Función para registrar una nueva categoría
function registrarCategoria($conexion, $nombre, $descripcion) {
    // Preparamos la consulta SQL para evitar inyecciones SQL
    $stmt = $conexion->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
    
    // "ss" significa que estamos pasando dos strings (cadenas de texto)
    $stmt->bind_param("ss", $nombre, $descripcion);
    
    // Ejecutamos la consulta
    if ($stmt->execute()) {
        return true; // Éxito
    } else {
        return false; // Error
    }
}

// Función para obtener todas las categorías
function obtenerCategorias($conexion) {
    $sql = "SELECT id_categoria, nombre, descripcion FROM categorias ORDER BY nombre ASC";
    $resultado = $conexion->query($sql);
    return $resultado; // Devolvemos el objeto de resultado
}

// --- NUEVA FUNCIÓN AÑADIDA ---

// Función para eliminar una categoría por su ID
function eliminarCategoria($conexion, $id) {
    // Preparamos la consulta
    $stmt = $conexion->prepare("DELETE FROM categorias WHERE id_categoria = ?");
    
    // "i" significa que estamos pasando un integer (número)
    $stmt->bind_param("i", $id);
    
    // Ejecutamos
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Función para OBTENER una categoría específica por su ID
function obtenerCategoriaPorID($conexion, $id) {
    // Preparamos la consulta
    $stmt = $conexion->prepare("SELECT id_categoria, nombre, descripcion FROM categorias WHERE id_categoria = ?");
    
    // "i" = integer
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // Obtenemos el resultado
    $resultado = $stmt->get_result();
    
    // Devolvemos la fila (o null si no se encontró)
    return $resultado->fetch_assoc();
}

// Función para ACTUALIZAR una categoría
function actualizarCategoria($conexion, $id, $nombre, $descripcion) {
    // Preparamos la consulta
    $stmt = $conexion->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?");
    
    // "ssi" = string, string, integer
    $stmt->bind_param("ssi", $nombre, $descripcion, $id);
    
    // Ejecutamos
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

?>