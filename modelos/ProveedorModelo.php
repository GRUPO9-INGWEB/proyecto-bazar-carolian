<?php
// modelos/ProveedorModelo.php
require_once __DIR__ . "/../config/conexion.php";

class ProveedorModelo extends Conexion
{
    private $ultimoError = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function getUltimoError()
    {
        return $this->ultimoError;
    }

    /* =========================================
       LISTAR PROVEEDORES (con búsqueda simple)
       ========================================= */
    public function obtenerProveedores($texto = "")
    {
        $sql = "SELECT *
                FROM tb_proveedores
                WHERE 1 = 1";

        $params = [];

        if ($texto !== "") {
            $sql .= " AND (
                        razon_social LIKE :texto
                        OR nombre_contacto LIKE :texto
                        OR numero_documento LIKE :texto
                        OR telefono LIKE :texto
                        OR correo LIKE :texto
                    )";
            $params[":texto"] = "%{$texto}%";
        }

        $sql .= " ORDER BY razon_social ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================
       OBTENER UNO
       ========================================= */
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM tb_proveedores WHERE id_proveedor = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================================
       INSERTAR
       ========================================= */
    public function insertar($data)
    {
        $this->ultimoError = "";

        try {
            $sql = "INSERT INTO tb_proveedores
                        (tipo_documento, numero_documento,
                         razon_social, nombre_contacto,
                         telefono, correo, direccion, estado)
                    VALUES
                        (:tipo_documento, :numero_documento,
                         :razon_social, :nombre_contacto,
                         :telefono, :correo, :direccion, :estado)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ":tipo_documento"   => $data["tipo_documento"],
                ":numero_documento" => $data["numero_documento"],
                ":razon_social"     => $data["razon_social"],
                ":nombre_contacto"  => $data["nombre_contacto"],
                ":telefono"         => $data["telefono"],
                ":correo"           => $data["correo"],
                ":direccion"        => $data["direccion"],
                ":estado"           => (int)$data["estado"],
            ]);

            return $this->conexion->lastInsertId();
        } catch (\Exception $e) {
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    /* =========================================
       ACTUALIZAR
       ========================================= */
    public function actualizar($id, $data)
    {
        $this->ultimoError = "";

        try {
            $sql = "UPDATE tb_proveedores
                    SET tipo_documento   = :tipo_documento,
                        numero_documento = :numero_documento,
                        razon_social     = :razon_social,
                        nombre_contacto  = :nombre_contacto,
                        telefono         = :telefono,
                        correo           = :correo,
                        direccion        = :direccion,
                        estado           = :estado
                    WHERE id_proveedor   = :id";

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                ":tipo_documento"   => $data["tipo_documento"],
                ":numero_documento" => $data["numero_documento"],
                ":razon_social"     => $data["razon_social"],
                ":nombre_contacto"  => $data["nombre_contacto"],
                ":telefono"         => $data["telefono"],
                ":correo"           => $data["correo"],
                ":direccion"        => $data["direccion"],
                ":estado"           => (int)$data["estado"],
                ":id"               => (int)$id,
            ]);
        } catch (\Exception $e) {
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    /* =========================================
       CAMBIAR ESTADO (activar / desactivar)
       ========================================= */
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE tb_proveedores
                SET estado = :estado
                WHERE id_proveedor = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ":estado" => (int)$estado,
            ":id"     => (int)$id,
        ]);
    }
}
