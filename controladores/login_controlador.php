<?php
session_start();
include_once "../conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = trim($_POST['dni']);
    $password = trim($_POST['password']);

    // Consulta por DNI
    $sql = "SELECT u.*, r.nombre_rol 
            FROM usuarios u 
            INNER JOIN roles r ON u.rol_id = r.id_rol 
            WHERE dni = ? AND estado = 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
            $_SESSION['usuario_rol'] = $usuario['rol_id'];
            $_SESSION['nombre_rol'] = $usuario['nombre_rol'];

            header("Location: ../vistas/dashboard.php");
            exit;
        }
    }

    header("Location: ../vistas/login.php?error=1");
    exit;
}
?>
