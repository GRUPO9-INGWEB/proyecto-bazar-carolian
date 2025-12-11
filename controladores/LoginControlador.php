<?php
// controladores/LoginControlador.php
require_once __DIR__ . "/../modelos/UsuarioModelo.php";
require_once __DIR__ . "/../modelos/AuditoriaModelo.php";

class LoginControlador
{
    private $modeloUsuario;
    private $auditoriaModelo;

    public function __construct()
    {
        $this->modeloUsuario   = new UsuarioModelo();
        $this->auditoriaModelo = new AuditoriaModelo();
    }

    /**
     * Inicia la sesión solo si aún no está iniciada
     */
    private function iniciarSesionSiNoExiste(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Muestra el formulario de inicio de sesión
     */
    public function mostrarFormulario(string $mensaje = ""): void
    {
        // La variable $mensaje se usa en la vista para mostrar errores o avisos
        include __DIR__ . "/../vistas/login/formulario_login.php";
    }

    /**
     * Procesa el formulario de login
     */
    public function validar(): void
    {
        $this->iniciarSesionSiNoExiste();

        // Solo debe llegar por POST; si viene por GET, lo mando al login
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: index.php");
            exit;
        }

        $usuario = trim($_POST["nombre_usuario"] ?? "");
        $clave   = $_POST["clave"] ?? "";

        if ($usuario === "" || $clave === "") {
            $mensaje = "Debe ingresar el usuario y la contraseña.";
            include __DIR__ . "/../vistas/login/formulario_login.php";
            return;
        }

        // Buscar usuario en la base de datos
        $datosUsuario = $this->modeloUsuario->obtenerUsuarioPorNombre($usuario);

        // Verificar clave
        if ($datosUsuario && password_verify($clave, $datosUsuario["clave"])) {

            // Guardar datos básicos en la sesión
            $_SESSION["id_usuario"]     = $datosUsuario["id_usuario"];
            $_SESSION["nombre_usuario"] = $datosUsuario["nombre_usuario"];
            $_SESSION["nombres"]        = $datosUsuario["nombres"];
            $_SESSION["apellidos"]      = $datosUsuario["apellidos"];
            $_SESSION["id_rol"]         = $datosUsuario["id_rol"];
            $_SESSION["nombre_rol"]     = $datosUsuario["nombre_rol"];

            // ===== AUDITORÍA: INICIO DE SESIÓN =====
            $this->auditoriaModelo->registrarEvento(
                (int)$datosUsuario["id_usuario"],
                'LOGIN',                 // módulo
                'INGRESO',               // acción
                'Inicio de sesión del usuario ' . $datosUsuario["nombre_usuario"],
                null,                    // tabla_afectada
                null                     // id_registro_afectado
            );

            // Redirigir según el rol
            if ($datosUsuario["nombre_rol"] === "ADMINISTRADORA") {
                header("Location: vistas/panel/panel_admin.php");
            } else {
                header("Location: vistas/panel/panel_vendedora.php");
            }
            exit;
        }

        // Si no pasó la validación:
        $mensaje = "Usuario o contraseña incorrectos.";
        include __DIR__ . "/../vistas/login/formulario_login.php";
    }

    /**
     * Cerrar sesión
     */
    public function cerrarSesion(): void
    {
        $this->iniciarSesionSiNoExiste();

        // Guardamos datos antes de destruir la sesión
        $id_usuario     = $_SESSION["id_usuario"]     ?? 0;
        $nombre_usuario = $_SESSION["nombre_usuario"] ?? '';

        // ===== AUDITORÍA: CIERRE DE SESIÓN =====
        if ($id_usuario) {
            $this->auditoriaModelo->registrarEvento(
                (int)$id_usuario,
                'LOGIN',                 // módulo
                'SALIDA',                // acción
                'Cierre de sesión del usuario ' . $nombre_usuario,
                null,
                null
            );
        }

        // Limpiar y destruir la sesión
        $_SESSION = [];
        session_unset();
        session_destroy();

        header("Location: ../index.php");
        exit;
    }
}
