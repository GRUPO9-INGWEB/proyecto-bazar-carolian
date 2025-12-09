<?php
// controladores/UsuarioControlador.php
require_once __DIR__ . "/../modelos/UsuarioModelo.php";

class UsuarioControlador {

    private $modelo;

    public function __construct() {
        $this->modelo = new UsuarioModelo();
    }

    // Listar usuarios
    public function listar($mensaje = "") {

        // Texto a buscar (desde la URL)
        $buscar = trim($_GET["buscar"] ?? "");

        // Orden seleccionado (ASC o DESC), por defecto DESC = más recientes
        $orden = strtoupper($_GET["orden"] ?? "DESC");
        if ($orden !== "ASC" && $orden !== "DESC") {
            $orden = "DESC";
        }

        // Obtenemos usuarios filtrados y ordenados
        $lista_usuarios = $this->modelo->obtenerTodosUsuarios($buscar, $orden);

        // Roles por si se usan en otra parte (modal, etc.)
        $lista_roles = $this->modelo->obtenerRoles();

        // La vista usará: $lista_usuarios, $lista_roles, $mensaje, $buscar, $orden
        include __DIR__ . "/../vistas/usuarios/listado_usuarios.php";
    }

    // Mostrar formulario de nuevo usuario
    public function formularioNuevo($mensaje = "") {
        $lista_roles = $this->modelo->obtenerRoles();
        $usuario = null; // para reutilizar la vista de formulario
        include __DIR__ . "/../vistas/usuarios/formulario_usuario.php";
    }

    // Guardar nuevo usuario
    public function guardarNuevo() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $datos = [
                "id_rol"         => $_POST["id_rol"],
                "nombre_usuario" => trim($_POST["nombre_usuario"]),
                "nombres"        => trim($_POST["nombres"]),
                "apellidos"      => trim($_POST["apellidos"]),
                "dni"            => trim($_POST["dni"]),
                "correo"         => trim($_POST["correo"]),
                "telefono"       => trim($_POST["telefono"]),
                "estado"         => 1,
            ];

            $clave_plana = $_POST["clave"] ?? "";
            if ($clave_plana === "") {
                $mensaje = "Debe ingresar una contraseña.";
                $this->formularioNuevo($mensaje);
                return;
            }

            $datos["clave"] = password_hash($clave_plana, PASSWORD_DEFAULT);

            if ($this->modelo->registrarUsuario($datos)) {
                $mensaje = "Usuario registrado correctamente.";
            } else {
                $mensaje = "Ocurrió un error al registrar el usuario.";
            }

            $this->listar($mensaje);
        }
    }

    // Mostrar formulario para editar
    public function formularioEditar($id_usuario, $mensaje = "") {
        $usuario = $this->modelo->obtenerUsuarioPorId($id_usuario);
        $lista_roles = $this->modelo->obtenerRoles();
        include __DIR__ . "/../vistas/usuarios/formulario_usuario.php";
    }

    // Guardar cambios de edición
    public function guardarEdicion() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $datos = [
                "id_usuario"     => $_POST["id_usuario"],
                "id_rol"         => $_POST["id_rol"],
                "nombre_usuario" => trim($_POST["nombre_usuario"]),
                "nombres"        => trim($_POST["nombres"]),
                "apellidos"      => trim($_POST["apellidos"]),
                "dni"            => trim($_POST["dni"]),
                "correo"         => trim($_POST["correo"]),
                "telefono"       => trim($_POST["telefono"]),
            ];

            if ($this->modelo->actualizarUsuario($datos)) {

                // Si el formulario trae una nueva clave, la actualizamos
                if (!empty($_POST["clave_nueva"])) {
                    $clave_hash = password_hash($_POST["clave_nueva"], PASSWORD_DEFAULT);
                    $this->modelo->actualizarClave($datos["id_usuario"], $clave_hash);
                }

                $mensaje = "Usuario actualizado correctamente.";
            } else {
                $mensaje = "No se pudo actualizar el usuario.";
            }

            $this->listar($mensaje);
        }
    }

    // Activar / desactivar usuario
    public function cambiarEstado($id_usuario, $nuevo_estado) {
        if ($this->modelo->cambiarEstadoUsuario($id_usuario, $nuevo_estado)) {
            $mensaje = "Estado del usuario actualizado.";
        } else {
            $mensaje = "No se pudo cambiar el estado del usuario.";
        }
        $this->listar($mensaje);
    }
}
