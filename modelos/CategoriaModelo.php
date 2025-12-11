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
    public function obtenerTodasCategorias(string $buscar = "", string $orden = "DESC"): array {
        // Normalizamos orden y texto
        $orden  = strtoupper($orden) === "ASC" ? "ASC" : "DESC";
        $buscar = trim($buscar);

        if ($buscar === "") {
            // Sin filtro
            $sql = "SELECT *
                    FROM tb_categorias
                    ORDER BY id_categoria $orden";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
        } else {
            // Con filtro de búsqueda (usamos ? ? para evitar el HY093)
            $sql = "SELECT *
                    FROM tb_categorias
                    WHERE  nombre_categoria      LIKE ?
                       OR descripcion_categoria LIKE ?
                    ORDER BY id_categoria $orden";

            $stmt = $this->conexion->prepare($sql);

            $patron = '%' . $buscar . '%';
            // Dos ? → dos valores en el array
            $stmt->execute([$patron, $patron]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarCategoria(string $nombre, string $descripcion, int $estado = 1): bool {
        $sql = "INSERT INTO tb_categorias
                    (nombre_categoria, descripcion_categoria, estado)
                VALUES
                    (:nombre, :descripcion, :estado)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':nombre'      => $nombre,
            ':descripcion' => $descripcion,
            ':estado'      => $estado,
        ]);
    }

    public function obtenerCategoriaPorId(int $id_categoria): ?array {
        $sql = "SELECT *
                FROM tb_categorias
                WHERE id_categoria = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_categoria, PDO::PARAM_INT);
        $stmt->execute();

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }

    public function actualizarCategoria(int $id_categoria, string $nombre, string $descripcion): bool {
        $sql = "UPDATE tb_categorias
                SET nombre_categoria      = :nombre,
                    descripcion_categoria = :descripcion
                WHERE id_categoria        = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':nombre'      => $nombre,
            ':descripcion' => $descripcion,
            ':id'          => $id_categoria,
        ]);
    }

    public function cambiarEstadoCategoria(int $id_categoria, int $estado): bool {
        $sql = "UPDATE tb_categorias
                SET estado = :estado
                WHERE id_categoria = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':estado' => $estado,
            ':id'     => $id_categoria,
        ]);
    }

    public function obtenerCategoriasActivas(): array {
        $sql = "SELECT id_categoria, nombre_categoria
                FROM tb_categorias
                WHERE estado = 1
                ORDER BY nombre_categoria ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
