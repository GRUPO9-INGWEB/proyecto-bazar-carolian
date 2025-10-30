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
                   u.id_rol, u.estado, u.password, r.nombre_rol
            FROM usuarios u
            INNER JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.dni = ? AND u.estado = 1
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        // --- CORRECCIÓN DE SEGURIDAD ---
        // Verificar contraseña ÚNICAMENTE con password_verify.
        // Se eliminó la comprobación de texto plano ($password === $usuario['password'])
        if (password_verify($password, $usuario['password'])) {
            
            // Guardar datos en sesión
            $_SESSION['logged_in'] = true;
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
            $_SESSION['usuario_rol'] = $usuario['id_rol']; // Este es el nombre que usamos
            $_SESSION['usuario_rol_nombre'] = $usuario['nombre_rol'];
            $_SESSION['dni'] = $usuario['dni'];

            // --- LÓGICA DE ROL CORREGIDA ---
            // Redirigir según el rol
            if ($usuario['id_rol'] == 1) {
                header("Location: ../vistas/dashboard_admin.php");
                exit;
            } elseif ($usuario['id_rol'] == 2) {
                header("Location: ../vistas/dashboard_vendedor.php");
                exit;
            } else {
                // Si el rol no es 1 o 2, no debe entrar.
                session_destroy(); // Destruimos la sesión creada
                echo "<script>alert('Su usuario no tiene permisos para acceder al sistema'); window.location='../vistas/login.php';</script>";
                exit;
            }

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