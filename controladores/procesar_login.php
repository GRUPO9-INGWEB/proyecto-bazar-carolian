<?php
session_start();
include_once "../conexion.php"; // conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dni = trim($_POST['dni'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($dni === '' || $password === '') {
        echo "<script>alert('Por favor ingresa tu DNI y contraseña'); window.location='../vistas/login.php';</script>";
        exit;
    }

    // Buscar usuario activo por DNI
    $sql = "SELECT u.id_usuario, u.nombre_completo, u.email, u.dni, u.telefono, 
                   u.rol_id, u.estado, u.password, r.nombre_rol
            FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id_rol
            WHERE u.dni = ? AND u.estado = 1
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        // Verificar contraseña (hash o texto plano)
        if (password_verify($password, $usuario['password']) || $password === $usuario['password']) {
            
            // Guardar datos en sesión
            $_SESSION['logged_in'] = true;
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
            $_SESSION['usuario_rol'] = $usuario['rol_id'];
            $_SESSION['usuario_rol_nombre'] = $usuario['nombre_rol'];
            $_SESSION['dni'] = $usuario['dni'];

            // Redirigir según el rol
            if ($usuario['rol_id'] == 1) {
                header("Location: ../vistas/dashboard_admin.php");
            } elseif ($usuario['rol_id'] == 2) {
                header("Location: ../vistas/dashboard_vendedor.php");
            } else {
                header("Location: ../vistas/dashboard.php");
            }
            exit;

        } else {
            echo "<script>alert('Contraseña incorrecta'); window.location='../vistas/login.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('DNI no encontrado o usuario inactivo'); window.location='../vistas/login.php';</script>";
        exit;
    }
} else {
    header("Location: ../vistas/login.php");
    exit;
}
?>
