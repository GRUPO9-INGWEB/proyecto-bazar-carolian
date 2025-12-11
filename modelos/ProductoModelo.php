<?php
// modelos/ProductoModelo.php
require_once __DIR__ . "/../config/conexion.php";

class ProductoModelo extends Conexion {

    public function __construct() {
        parent::__construct();
    }

    // Listar productos con el nombre de la categoría (con búsqueda y orden)
public function obtenerTodosProductos($buscar = "", $orden = "DESC") {
    // Aseguramos que el orden solo sea ASC o DESC
    $orden = strtoupper($orden) === "ASC" ? "ASC" : "DESC";

    // Limpiamos espacios
    $buscar = trim($buscar);

    if ($buscar === "") {
        // Sin filtro de búsqueda
        $sql = "SELECT p.*, c.nombre_categoria
                FROM tb_productos p
                INNER JOIN tb_categorias c ON p.id_categoria = c.id_categoria
                ORDER BY p.id_producto $orden";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
    } else {
        // Con filtro de búsqueda – usamos placeholders distintos
        $sql = "SELECT p.*, c.nombre_categoria
                FROM tb_productos p
                INNER JOIN tb_categorias c ON p.id_categoria = c.id_categoria
                WHERE
                    p.codigo_interno         LIKE :b1
                    OR p.codigo_barras       LIKE :b2
                    OR p.nombre_producto     LIKE :b3
                    OR p.descripcion_producto LIKE :b4
                    OR c.nombre_categoria    LIKE :b5
                ORDER BY p.id_producto $orden";

        $consulta = $this->conexion->prepare($sql);

        $patron = '%' . $buscar . '%';

        $params = [
            ':b1' => $patron,
            ':b2' => $patron,
            ':b3' => $patron,
            ':b4' => $patron,
            ':b5' => $patron,
        ];

        $consulta->execute($params);
    }

    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}


    // Categorías activas (para el combo)
    public function obtenerCategoriasActivas() {
        $sql = "SELECT id_categoria, nombre_categoria
                FROM tb_categorias
                WHERE estado = 1
                ORDER BY nombre_categoria ASC";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registrar producto (incluye stock_actual y fecha_caducidad)
    public function registrarProducto($datos) {
        $sql = "INSERT INTO tb_productos
                (id_categoria, codigo_interno, codigo_barras, nombre_producto,
                 descripcion_producto, fecha_caducidad,
                 stock_actual, stock_minimo,
                 precio_compra, precio_venta, afecta_igv, estado)
                VALUES
                (:id_categoria, :codigo_interno, :codigo_barras, :nombre_producto,
                 :descripcion_producto, :fecha_caducidad,
                 :stock_actual, :stock_minimo,
                 :precio_compra, :precio_venta, :afecta_igv, :estado)";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            ":id_categoria"         => $datos["id_categoria"],
            ":codigo_interno"       => $datos["codigo_interno"],
            ":codigo_barras"        => $datos["codigo_barras"],
            ":nombre_producto"      => $datos["nombre_producto"],
            ":descripcion_producto" => $datos["descripcion_producto"],
            ":fecha_caducidad"      => $datos["fecha_caducidad"],
            ":stock_actual"         => $datos["stock_actual"],
            ":stock_minimo"         => $datos["stock_minimo"],
            ":precio_compra"        => $datos["precio_compra"],
            ":precio_venta"         => $datos["precio_venta"],
            ":afecta_igv"           => $datos["afecta_igv"],
            ":estado"               => $datos["estado"],
        ]);
    }

    public function obtenerProductoPorId($id_producto) {
        $sql = "SELECT * FROM tb_productos WHERE id_producto = :id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":id", $id_producto, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar producto (incluye stock_actual y fecha_caducidad)
    public function actualizarProducto($datos) {
        $sql = "UPDATE tb_productos
                SET id_categoria = :id_categoria,
                    codigo_interno = :codigo_interno,
                    codigo_barras = :codigo_barras,
                    nombre_producto = :nombre_producto,
                    descripcion_producto = :descripcion_producto,
                    fecha_caducidad = :fecha_caducidad,
                    stock_actual = :stock_actual,
                    stock_minimo = :stock_minimo,
                    precio_compra = :precio_compra,
                    precio_venta = :precio_venta,
                    afecta_igv = :afecta_igv
                WHERE id_producto = :id_producto";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            ":id_categoria"         => $datos["id_categoria"],
            ":codigo_interno"       => $datos["codigo_interno"],
            ":codigo_barras"        => $datos["codigo_barras"],
            ":nombre_producto"      => $datos["nombre_producto"],
            ":descripcion_producto" => $datos["descripcion_producto"],
            ":fecha_caducidad"      => $datos["fecha_caducidad"],
            ":stock_actual"         => $datos["stock_actual"],
            ":stock_minimo"         => $datos["stock_minimo"],
            ":precio_compra"        => $datos["precio_compra"],
            ":precio_venta"         => $datos["precio_venta"],
            ":afecta_igv"           => $datos["afecta_igv"],
            ":id_producto"          => $datos["id_producto"],
        ]);
    }

    public function cambiarEstadoProducto($id_producto, $estado) {
        $sql = "UPDATE tb_productos SET estado = :estado WHERE id_producto = :id";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            ":estado" => $estado,
            ":id"     => $id_producto,
        ]);
    }

    /* ==========================
       MÉTODOS PARA ALERTAS
       ========================== */

    // Productos con stock bajo (stock_actual <= stock_minimo)
    public function obtenerProductosStockBajo() {
        $sql = "SELECT p.*, c.nombre_categoria
                FROM tb_productos p
                INNER JOIN tb_categorias c ON p.id_categoria = c.id_categoria
                WHERE p.estado = 1
                  AND p.stock_minimo > 0
                  AND p.stock_actual <= p.stock_minimo
                ORDER BY p.stock_actual ASC";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Productos por vencer en N días (por defecto 30)
    public function obtenerProductosPorVencer($dias = 30) {
        $sql = "SELECT p.*, c.nombre_categoria
                FROM tb_productos p
                INNER JOIN tb_categorias c ON p.id_categoria = c.id_categoria
                WHERE p.estado = 1
                  AND p.fecha_caducidad IS NOT NULL
                  AND p.fecha_caducidad >= CURDATE()
                  AND DATEDIFF(p.fecha_caducidad, CURDATE()) <= :dias
                ORDER BY p.fecha_caducidad ASC";
        $consulta = $this->conexion->prepare($sql);
        $dias = (int)$dias;
        $consulta->bindParam(":dias", $dias, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Productos ya vencidos
    public function obtenerProductosVencidos() {
        $sql = "SELECT p.*, c.nombre_categoria
                FROM tb_productos p
                INNER JOIN tb_categorias c ON p.id_categoria = c.id_categoria
                WHERE p.estado = 1
                  AND p.fecha_caducidad IS NOT NULL
                  AND p.fecha_caducidad < CURDATE()
                ORDER BY p.fecha_caducidad ASC";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================= CATEGORÍAS ACTIVAS PARA EL COMBO =================
// ============ REPORTE: PRODUCTOS POR CATEGORÍA ======================
public function obtenerProductosPorCategoriaReporte(int $id_categoria = 0): array
{
    $sql = "SELECT
                p.id_producto,
                p.codigo_interno,
                p.nombre_producto,
                p.stock_actual,
                p.precio_venta,
                p.estado,
                c.nombre_categoria
            FROM tb_productos p
            INNER JOIN tb_categorias c
                ON p.id_categoria = c.id_categoria
            WHERE 1 = 1";

    $params = [];

    if ($id_categoria > 0) {
        $sql .= " AND p.id_categoria = :id_cat";
        $params[':id_cat'] = $id_categoria;
    }

    $sql .= " ORDER BY c.nombre_categoria ASC, p.nombre_producto ASC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}
