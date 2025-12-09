<?php
// modelos/UsuarioModelo.php
require_once __DIR__ . "/../config/conexion.php";

class UsuarioModelo extends Conexion {

    public function __construct() {
        parent::__construct();
    }

    // Para el login
    public function obtenerUsuarioPorNombre($nombre_usuario) {
        $sql = "SELECT u.*, r.nombre_rol 
                FROM tb_usuarios u
                INNER JOIN tb_roles r ON u.id_rol = r.id_rol
                WHERE u.nombre_usuario = :nombre_usuario
                  AND u.estado = 1";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":nombre_usuario", $nombre_usuario, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener usuarios con búsqueda y orden
     * $buscar: texto a buscar en usuario, nombres, apellidos, dni, correo, teléfono, rol
     * $orden: 'ASC' o 'DESC' (por defecto DESC = más recientes primero)
     */
    public function obtenerTodosUsuarios($buscar = "", $orden = "DESC") {
        // Aseguramos que el orden solo sea ASC o DESC
        $orden = strtoupper($orden) === "ASC" ? "ASC" : "DESC";

        if ($buscar === "") {
            // Sin filtro
            $sql = "SELECT u.id_usuario, u.nombre_usuario, u.nombres, u.apellidos,
                           u.dni, u.correo, u.telefono, u.estado,
                           r.nombre_rol
                    FROM tb_usuarios u
                    INNER JOIN tb_roles r ON u.id_rol = r.id_rol
                    ORDER BY u.id_usuario $orden";
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute();
        } else {
            // Con filtro de búsqueda
            $sql = "SELECT u.id_usuario, u.nombre_usuario, u.nombres, u.apellidos,
                           u.dni, u.correo, u.telefono, u.estado,
                           r.nombre_rol
                    FROM tb_usuarios u
                    INNER JOIN tb_roles r ON u.id_rol = r.id_rol
                    WHERE
                        u.nombre_usuario LIKE :buscar
                        OR u.nombres     LIKE :buscar
                        OR u.apellidos   LIKE :buscar
                        OR u.dni         LIKE :buscar
                        OR u.correo      LIKE :buscar
                        OR u.telefono    LIKE :buscar
                        OR r.nombre_rol  LIKE :buscar
                    ORDER BY u.id_usuario $orden";
            $consulta = $this->conexion->prepare($sql);
            $patron = "%" . $buscar . "%";
            $consulta->bindParam(":buscar", $patron, PDO::PARAM_STR);
            $consulta->execute();
        }

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los roles (para el combo)
    public function obtenerRoles() {
        $sql = "SELECT id_rol, nombre_rol FROM tb_roles WHERE estado = 1";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registrar nuevo usuario
    public function registrarUsuario($datos) {
        $sql = "INSERT INTO tb_usuarios
                (id_rol, nombre_usuario, clave, nombres, apellidos, dni, correo, telefono, estado)
                VALUES
                (:id_rol, :nombre_usuario, :clave, :nombres, :apellidos, :dni, :correo, :telefono, :estado)";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            ":id_rol"         => $datos["id_rol"],
            ":nombre_usuario" => $datos["nombre_usuario"],
            ":clave"          => $datos["clave"],   // ya debe venir encriptada
            ":nombres"        => $datos["nombres"],
            ":apellidos"      => $datos["apellidos"],
            ":dni"            => $datos["dni"],
            ":correo"         => $datos["correo"],
            ":telefono"       => $datos["telefono"],
            ":estado"         => $datos["estado"],
        ]);
    }

    // Obtener un usuario por ID (para editar)
    public function obtenerUsuarioPorId($id_usuario) {
        $sql = "SELECT * FROM tb_usuarios WHERE id_usuario = :id_usuario";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar usuario (sin cambiar clave)
    public function actualizarUsuario($datos) {
        $sql = "UPDATE tb_usuarios
                SET id_rol = :id_rol,
                    nombre_usuario = :nombre_usuario,
                    nombres = :nombres,
                    apellidos = :apellidos,
                    dni = :dni,
                    correo = :correo,
                    telefono = :telefono
                WHERE id_usuario = :id_usuario";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            ":id_rol"         => $datos["id_rol"],
            ":nombre_usuario" => $datos["nombre_usuario"],
            ":nombres"        => $datos["nombres"],
            ":apellidos"      => $datos["apellidos"],
            ":dni"            => $datos["dni"],
            ":correo"         => $datos["correo"],
            ":telefono"       => $datos["telefono"],
            ":id_usuario"     => $datos["id_usuario"],
        ]);
    }

    // Cambiar estado ACTIVO / INACTIVO
    public function cambiarEstadoUsuario($id_usuario, $estado) {
        $sql = "UPDATE tb_usuarios SET estado = :estado WHERE id_usuario = :id_usuario";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            ":estado"     => $estado,
            ":id_usuario" => $id_usuario,
        ]);
    }

    // Opcional: cambiar la contraseña
    public function actualizarClave($id_usuario, $clave_hash) {
        $sql = "UPDATE tb_usuarios SET clave = :clave WHERE id_usuario = :id_usuario";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            ":clave"      => $clave_hash,
            ":id_usuario" => $id_usuario,
        ]);
    }
}
