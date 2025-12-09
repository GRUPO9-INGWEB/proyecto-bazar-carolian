<?php
// modelos/ClienteModelo.php
require_once __DIR__ . "/../config/conexion.php";

class ClienteModelo extends Conexion
{
    public function __construct()
    {
        parent::__construct();
    }

    // Ahora acepta un orden opcional: ASC o DESC
    public function obtenerClientes($buscar = "", $orden = "DESC")
    {
        $orden = strtoupper($orden) === "ASC" ? "ASC" : "DESC";

        if ($buscar === "") {
            $sql = "SELECT * FROM tb_clientes ORDER BY id_cliente $orden";
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute();
        } else {
            $sql = "SELECT *
                    FROM tb_clientes
                    WHERE
                        numero_documento LIKE :buscar
                        OR nombres LIKE :buscar
                        OR apellidos LIKE :buscar
                        OR razon_social LIKE :buscar
                        OR correo LIKE :buscar
                        OR telefono LIKE :buscar
                    ORDER BY id_cliente $orden";
            $consulta = $this->conexion->prepare($sql);
            $patron = "%" . $buscar . "%";
            $consulta->bindParam(":buscar", $patron, PDO::PARAM_STR);
            $consulta->execute();
        }

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerClientePorId($id_cliente)
    {
        $sql = "SELECT * FROM tb_clientes WHERE id_cliente = :id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":id", $id_cliente, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarCliente($datos)
    {
        $sql = "INSERT INTO tb_clientes
                (tipo_documento, numero_documento, nombres, apellidos,
                 razon_social, direccion, correo, telefono, estado)
                VALUES
                (:tipo_documento, :numero_documento, :nombres, :apellidos,
                 :razon_social, :direccion, :correo, :telefono, :estado)";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
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

    public function actualizarCliente($datos)
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
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
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

    public function cambiarEstadoCliente($id_cliente, $estado)
    {
        $sql = "UPDATE tb_clientes SET estado = :estado WHERE id_cliente = :id";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            ":estado" => $estado,
            ":id"     => $id_cliente,
        ]);
    }

    // Buscar cliente por número de documento (DNI o RUC)
    public function obtenerClientePorDocumento($numero_documento) {
    $sql = "SELECT * FROM tb_clientes
            WHERE numero_documento = :doc
              AND estado = 1
            LIMIT 1";
    $consulta = $this->conexion->prepare($sql);
    $consulta->bindParam(':doc', $numero_documento, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetch(PDO::FETCH_ASSOC);
    }

}
