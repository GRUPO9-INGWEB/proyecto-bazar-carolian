<?php
// modelos/ClienteModelo.php
require_once __DIR__ . "/../config/conexion.php";

class ClienteModelo extends Conexion
{
    public function __construct()
    {
        parent::__construct();
    }

    // Listar clientes con búsqueda y orden (ASC / DESC)
    public function obtenerClientes(string $buscar = "", string $orden = "DESC"): array
    {
        $orden  = strtoupper($orden) === "ASC" ? "ASC" : "DESC";
        $buscar = trim($buscar);

        if ($buscar === "") {
            // Sin filtro de búsqueda
            $sql = "SELECT *
                    FROM tb_clientes
                    ORDER BY id_cliente $orden";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
        } else {
            // Con filtro de búsqueda (usamos ? ? ? para evitar HY093)
            $sql = "SELECT *
                    FROM tb_clientes
                    WHERE
                        numero_documento LIKE ?
                        OR nombres        LIKE ?
                        OR apellidos      LIKE ?
                        OR razon_social   LIKE ?
                        OR correo         LIKE ?
                        OR telefono       LIKE ?
                    ORDER BY id_cliente $orden";

            $stmt = $this->conexion->prepare($sql);

            $patron = '%' . $buscar . '%';

            // 6 ? en el SQL → 6 valores en el array
            $stmt->execute([
                $patron, // numero_documento
                $patron, // nombres
                $patron, // apellidos
                $patron, // razon_social
                $patron, // correo
                $patron, // telefono
            ]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerClientePorId(int $id_cliente): ?array
    {
        $sql = "SELECT *
                FROM tb_clientes
                WHERE id_cliente = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id_cliente, PDO::PARAM_INT);
        $stmt->execute();

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }

    public function registrarCliente(array $datos): bool
    {
        $sql = "INSERT INTO tb_clientes
                (tipo_documento, numero_documento, nombres, apellidos,
                 razon_social, direccion, correo, telefono, estado)
                VALUES
                (:tipo_documento, :numero_documento, :nombres, :apellidos,
                 :razon_social, :direccion, :correo, :telefono, :estado)";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ":tipo_documento"   => $datos["tipo_documento"],
            ":numero_documento" => $datos["numero_documento"],
            ":nombres"          => $datos["nombres"],
            ":apellidos"        => $datos["apellidos"],
            ":razon_social"     => $datos["razon_social"],
            ":direccion"        => $datos["direccion"],
            ":correo"           => $datos["correo"],
            ":telefono"         => $datos["telefono"],
            ":estado"           => $datos["estado"],
        ]);
    }

    public function actualizarCliente(array $datos): bool
    {
        $sql = "UPDATE tb_clientes
                SET tipo_documento   = :tipo_documento,
                    numero_documento = :numero_documento,
                    nombres          = :nombres,
                    apellidos        = :apellidos,
                    razon_social     = :razon_social,
                    direccion        = :direccion,
                    correo           = :correo,
                    telefono         = :telefono
                WHERE id_cliente     = :id_cliente";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ":tipo_documento"   => $datos["tipo_documento"],
            ":numero_documento" => $datos["numero_documento"],
            ":nombres"          => $datos["nombres"],
            ":apellidos"        => $datos["apellidos"],
            ":razon_social"     => $datos["razon_social"],
            ":direccion"        => $datos["direccion"],
            ":correo"           => $datos["correo"],
            ":telefono"         => $datos["telefono"],
            ":id_cliente"       => $datos["id_cliente"],
        ]);
    }

    public function cambiarEstadoCliente(int $id_cliente, int $estado): bool
    {
        $sql = "UPDATE tb_clientes
                SET estado = :estado
                WHERE id_cliente = :id";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ":estado" => $estado,
            ":id"     => $id_cliente,
        ]);
    }

    // Buscar cliente por número de documento (DNI o RUC)
    public function obtenerClientePorDocumento(string $numero_documento): ?array
    {
        $sql = "SELECT *
                FROM tb_clientes
                WHERE numero_documento = :doc
                  AND estado = 1
                LIMIT 1";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':doc', $numero_documento, PDO::PARAM_STR);
        $stmt->execute();

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }
}
