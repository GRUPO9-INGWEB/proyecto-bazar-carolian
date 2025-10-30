<?php
// Ubicación: proyecto-bazar-carolian/modelos/usuario_modelo.php

function registrarUsuario($conexion, $datos) {
    $hash_password = password_hash($datos['password'], PASSWORD_DEFAULT);
    
    // NOTA: El campo estado aquí se fija a 1 (Activo) si usas el registro directo.
    $sql = "INSERT INTO usuarios (nombre_completo, email, password, id_rol, estado, dni, telefono) 
             VALUES (?, ?, ?, ?, 1, ?, ?)";
    
    // TIPOS: ssssi s s (string x5, integer x1)
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssiis", $datos['nombre_completo'], $datos['email'], $hash_password, $datos['id_rol'], $datos['dni'], $datos['telefono']);
    return $stmt->execute();
}

function obtenerUsuarios($conexion) {
    // 🌟 CORRECCIÓN 1: INCLUIR U.ESTADO 🌟
    $sql = "SELECT u.id_usuario, u.nombre_completo, u.email, u.estado, r.nombre_rol as rol 
             FROM usuarios u
             INNER JOIN roles r ON u.id_rol = r.id_rol
             WHERE u.estado IN (0, 1) /* Muestra todos los usuarios (activos y eliminados lógicamente) */
             ORDER BY u.nombre_completo ASC";
    
    return $conexion->query($sql);
}

function obtenerUsuarioPorId($conexion, $id_usuario) {
    // 🌟 CORRECCIÓN 2: INCLUIR ESTADO 🌟
    $sql = "SELECT id_usuario, nombre_completo, email, dni, telefono, id_rol, estado 
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
    $estado = $datos['estado']; // 👈 Leer el nuevo estado del array de datos

    // Mapeo del valor de estado de HTML/JS ('A'/'I') a la base de datos (1/0)
    $estado_db = ($estado == 'A') ? 1 : 0; 

    if (!empty($password)) {
        // 🌟 CORRECCIÓN 3.1: Incluir estado y password 🌟
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nombre_completo = ?, email = ?, id_rol = ?, password = ?, dni = ?, telefono = ?, estado = ? WHERE id_usuario = ?";
        $stmt = $conexion->prepare($sql);
        // TIPOS: s s i s s s i i (string x6, int x2)
        $stmt->bind_param("ssisssii", $nombre_completo, $email, $id_rol, $hash_password, $dni, $telefono, $estado_db, $id_usuario);
    } else {
        // 🌟 CORRECCIÓN 3.2: Incluir solo estado 🌟
        $sql = "UPDATE usuarios SET nombre_completo = ?, email = ?, id_rol = ?, dni = ?, telefono = ?, estado = ? WHERE id_usuario = ?";
        $stmt = $conexion->prepare($sql);
        // TIPOS: s s i s s i i (string x4, int x3)
        $stmt->bind_param("ssisssi", $nombre_completo, $email, $id_rol, $dni, $telefono, $estado_db, $id_usuario);
    }
    
    return $stmt->execute();
}

function eliminarLogicoUsuario($conexion, $id_usuario) {
    // El eliminar lógico lo establece a 0 (Inactivo/Eliminado)
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