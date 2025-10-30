<?php
// Ubicación: proyecto-bazar-carolian/modelos/usuario_modelo.php

function registrarUsuario($conexion, $datos) {
    $hash_password = password_hash($datos['password'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO usuarios (nombre_completo, email, password, id_rol, estado, dni, telefono) 
            VALUES (?, ?, ?, ?, 1, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssss", $datos['nombre_completo'], $datos['email'], $hash_password, $datos['id_rol'], $datos['dni'], $datos['telefono']);
    return $stmt->execute();
}

function obtenerUsuarios($conexion) {
    // Une usuarios con roles para mostrar el nombre del rol
    $sql = "SELECT u.id_usuario, u.nombre_completo, u.email, r.nombre_rol as rol 
            FROM usuarios u
            INNER JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.estado = 1 
            ORDER BY u.nombre_completo ASC";
    
    return $conexion->query($sql);
}

function obtenerUsuarioPorId($conexion, $id_usuario) {
    $sql = "SELECT id_usuario, nombre_completo, email, dni, telefono, id_rol 
            FROM usuarios 
            WHERE id_usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function actualizarUsuario($conexion, $datos) {
    $id_usuario = $datos['id_usuario'];
    $nombre_completo = $datos['nombre_completo'];
    $email = $datos['email'];
    $id_rol = $datos['id_rol'];
    $password = $datos['password'];
    $dni = $datos['dni'];
    $telefono = $datos['telefono'];

    if (!empty($password)) {
        // Actualizar con contraseña hasheada
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nombre_completo = ?, email = ?, id_rol = ?, password = ?, dni = ?, telefono = ? WHERE id_usuario = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssssi", $nombre_completo, $email, $id_rol, $hash_password, $dni, $telefono, $id_usuario);
    } else {
        // Actualizar sin cambiar la contraseña
        $sql = "UPDATE usuarios SET nombre_completo = ?, email = ?, id_rol = ?, dni = ?, telefono = ? WHERE id_usuario = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssssi", $nombre_completo, $email, $id_rol, $dni, $telefono, $id_usuario);
    }
    
    return $stmt->execute();
}

function eliminarLogicoUsuario($conexion, $id_usuario) {
    $sql = "UPDATE usuarios SET estado = 0 WHERE id_usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    return $stmt->execute();
}

function obtenerRoles($conexion) {
    $sql = "SELECT id_rol, nombre_rol FROM roles WHERE estado = 1";
    return $conexion->query($sql);
}
?>