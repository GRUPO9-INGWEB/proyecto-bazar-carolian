<?php
// modelos/CategoriaModelo.php
require_once __DIR__ . "/../config/conexion.php";

class CategoriaModelo extends Conexion {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Listar categorías con búsqueda y orden
     * $buscar: texto a buscar en nombre o descripción
     * $orden: ASC o DESC (por defecto DESC = más recientes primero)
     */
    public function obtenerTodasCategorias($buscar = "", $orden = "DESC") {
        $orden = strtoupper($orden) === "ASC" ? "ASC" : "DESC";

        if ($buscar === "") {
            $sql = "SELECT * FROM tb_categorias ORDER BY id_categoria $orden";
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute();
        } else {
            $sql = "SELECT *
                    FROM tb_categorias
                    WHERE nombre_categoria LIKE :buscar
                       OR descripcion_categoria LIKE :buscar
                    ORDER BY id_categoria $orden";
            $consulta = $this->conexion->prepare($sql);
            $patron = "%" . $buscar . "%";
            $consulta->bindParam(":buscar", $patron, PDO::PARAM_STR);
            $consulta->execute();
        }

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarCategoria($nombre, $descripcion, $estado = 1) {
        $sql = "INSERT INTO tb_categorias (nombre_categoria, descripcion_categoria, estado)
                VALUES (:nombre, :descripcion, :estado)";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            ":nombre"      => $nombre,
            ":descripcion" => $descripcion,
            ":estado"      => $estado,
        ]);
    }

    public function obtenerCategoriaPorId($id_categoria) {
        $sql = "SELECT * FROM tb_categorias WHERE id_categoria = :id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":id", $id_categoria, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarCategoria($id_categoria, $nombre, $descripcion) {
        $sql = "UPDATE tb_categorias
                SET nombre_categoria = :nombre,
                    descripcion_categoria = :descripcion
                WHERE id_categoria = :id";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            ":nombre"      => $nombre,
            ":descripcion" => $descripcion,
            ":id"          => $id_categoria,
        ]);
    }

    public function cambiarEstadoCategoria($id_categoria, $estado) {
        $sql = "UPDATE tb_categorias
                SET estado = :estado
                WHERE id_categoria = :id";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            ":estado" => $estado,
            ":id"     => $id_categoria,
        ]);
    }

    public function obtenerCategoriasActivas() {
    $sql = "SELECT id_categoria, nombre_categoria
            FROM tb_categorias
            WHERE estado = 1
            ORDER BY nombre_categoria ASC";
    $consulta = $this->conexion->prepare($sql);
    $consulta->execute();
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

}
