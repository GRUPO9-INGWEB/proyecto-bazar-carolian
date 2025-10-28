<?php
function buscarCliente($conexion, $tipo, $numero) {
    $stmt = $conexion->prepare("SELECT * FROM clientes WHERE documento_tipo = ? AND documento_numero = ?");
    $stmt->bind_param("ss", $tipo, $numero);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}
function registrarCliente($conexion, $datos) {
    $stmt = $conexion->prepare("INSERT INTO clientes (documento_tipo, documento_numero, nombre_completo, email, direccion, telefono) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", 
        $datos['tipo_doc'],
        $datos['num_doc'],
        $datos['nombre'],
        $datos['email'],
        $datos['direccion'],
        $datos['telefono']
    );
    if ($stmt->execute()) {
        return $conexion->insert_id;
    } else {
        return false;
    }
}
?>