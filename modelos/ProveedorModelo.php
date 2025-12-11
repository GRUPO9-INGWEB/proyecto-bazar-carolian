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
    public function obtenerProveedores(string $texto = ""): array
    {
        $texto = trim($texto);

        if ($texto === "") {
            // Sin filtro
            $sql = "SELECT *
                    FROM tb_proveedores
                    ORDER BY razon_social ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
        } else {
            // Con filtro de búsqueda (usamos ? ? ? para evitar HY093)
            $sql = "SELECT *
                    FROM tb_proveedores
                    WHERE
                        razon_social     LIKE ?
                        OR nombre_contacto   LIKE ?
                        OR numero_documento  LIKE ?
                        OR telefono          LIKE ?
                        OR correo            LIKE ?
                    ORDER BY razon_social ASC";

            $stmt = $this->conexion->prepare($sql);

            $patron = '%' . $texto . '%';

            // 5 ? en el SQL → 5 valores en el array
            $stmt->execute([
                $patron, // razon_social
                $patron, // nombre_contacto
                $patron, // numero_documento
                $patron, // telefono
                $patron, // correo
            ]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================
       OBTENER UNO
       ========================================= */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT *
                FROM tb_proveedores
                WHERE id_proveedor = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }

    /* =========================================
       INSERTAR
       ========================================= */
    public function insertar(array $data)
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
    public function actualizar(int $id, array $data): bool
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
    public function cambiarEstado(int $id, int $estado): bool
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
