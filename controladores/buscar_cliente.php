<?php
include_once "../conexion.php";
header('Content-Type: application/json');

// Respuesta por defecto si no se encuentra nada
$respuesta = [
    'id_cliente' => null, 
    'nombre_completo' => null,
    'documento_numero' => null,
    'documento_tipo' => null,
    'direccion' => null,
    'email' => null
];

if (isset($_GET['numero']) && isset($_GET['tipo'])) {
    $numero = trim($_GET['numero']);
    $tipo = trim($_GET['tipo']);
    
    // 1. Prepara la consulta usando los dos campos
    $sql = "SELECT id_cliente, nombre_completo, documento_numero, documento_tipo, direccion, email 
            FROM clientes 
            WHERE documento_numero = ? AND documento_tipo = ? AND estado = 1";
    
    $stmt = $conexion->prepare($sql);
    
    // Asigna parámetros: 'ss' (string, string)
    if ($stmt) {
        $stmt->bind_param("ss", $numero, $tipo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($cliente = $resultado->fetch_assoc()) {
            // Si encuentra el cliente, devuelve todos sus datos
            $respuesta = $cliente; 
        }
        $stmt->close();
    }
}

echo json_encode($respuesta);
?>