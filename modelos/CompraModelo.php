<?php
// modelos/CompraModelo.php
require_once __DIR__ . "/../config/conexion.php";

class CompraModelo extends Conexion
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

    /* ========================================================
       LISTADO DE COMPRAS
       ======================================================== */
    public function obtenerCompras(string $texto = "", string $orden = "recientes", string $tipo_filtro = "TODOS"): array
    {
        $texto = trim($texto);

        $sql = "SELECT
                    co.*,
                    p.razon_social,
                    p.numero_documento,
                    tc.nombre_tipo
                FROM tb_compras co
                INNER JOIN tb_proveedores p
                    ON co.id_proveedor = p.id_proveedor
                INNER JOIN tb_tipos_comprobante tc
                    ON co.id_tipo_comprobante = tc.id_tipo_comprobante
                WHERE 1 = 1";

        // Usaremos SOLO placeholders posicionales (?) para evitar HY093
        $params = [];

        // Búsqueda por texto: proveedor, documento o comprobante
        if ($texto !== "") {
            $sql .= " AND (
                        p.numero_documento LIKE ?
                        OR p.razon_social LIKE ?
                        OR CONCAT(tc.nombre_tipo, ' ', co.serie_comprobante, '-', co.numero_comprobante) LIKE ?
                    )";

            $patron = '%' . $texto . '%';
            // 3 ? -> 3 valores
            $params[] = $patron; // numero_documento
            $params[] = $patron; // razon_social
            $params[] = $patron; // comprobante concatenado
        }

        // Filtro por tipo de comprobante
        if ($tipo_filtro !== "TODOS" && $tipo_filtro !== "") {
            $sql .= " AND UPPER(tc.nombre_tipo) = ?";
            $params[] = strtoupper($tipo_filtro);
        }

        // Orden
        switch ($orden) {
            case "monto_mayor":
                $sql .= " ORDER BY co.total DESC";
                break;
            case "monto_menor":
                $sql .= " ORDER BY co.total ASC";
                break;
            case "antiguas":
                $sql .= " ORDER BY co.fecha_compra ASC";
                break;
            case "recientes":
            default:
                $sql .= " ORDER BY co.fecha_compra DESC";
                break;
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ========================================================
       DATOS PARA FORMULARIO NUEVA COMPRA
       ======================================================== */

    public function obtenerProveedoresActivos()
    {
        $sql = "SELECT id_proveedor,
                       tipo_documento,
                       numero_documento,
                       razon_social,
                       nombre_contacto
                FROM tb_proveedores
                WHERE estado = 1
                ORDER BY razon_social ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTiposComprobanteActivos()
    {
        $sql = "SELECT id_tipo_comprobante, nombre_tipo
                FROM tb_tipos_comprobante
                WHERE estado = 1
                ORDER BY nombre_tipo ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosActivos()
    {
        $sql = "SELECT
                    id_producto,
                    codigo_interno,
                    nombre_producto,
                    stock_actual,
                    afecta_igv
                FROM tb_productos
                WHERE estado = 1
                ORDER BY nombre_producto ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ========================================================
       REGISTRAR COMPRA (cabecera + detalle + actualización stock)
       ======================================================== */
    public function registrarCompra($cabecera, $detalles)
    {
        $this->ultimoError = "";

        try {
            $this->conexion->beginTransaction();

            /* 1) SERIE POR DEFECTO */
            if (empty($cabecera["serie_comprobante"])) {
                switch ((int)$cabecera["id_tipo_comprobante"]) {
                    case 3: // FACTURA
                        $cabecera["serie_comprobante"] = "F001";
                        break;
                    case 2: // BOLETA
                        $cabecera["serie_comprobante"] = "B001";
                        break;
                    default: // OTROS
                        $cabecera["serie_comprobante"] = "C001";
                        break;
                }
            }

            /* 2) GENERAR CORRELATIVO SI FALTA */
            if (empty($cabecera["numero_comprobante"])) {
                $cabecera["numero_comprobante"] = $this->obtenerSiguienteNumero(
                    (int)$cabecera["id_tipo_comprobante"],
                    $cabecera["serie_comprobante"]
                );
            }

            /* 3) CALCULAR TOTALES + ACTUALIZAR STOCK (SUMA) */

            $subtotal        = 0;
            $subtotalGravado = 0;

            $sqlProd = "SELECT stock_actual, afecta_igv
                        FROM tb_productos
                        WHERE id_producto = :id
                        FOR UPDATE";
            $stmtProd = $this->conexion->prepare($sqlProd);

            $sqlUpdateStock = "UPDATE tb_productos
                               SET stock_actual = :stock
                               WHERE id_producto = :id";
            $stmtUpdateStock = $this->conexion->prepare($sqlUpdateStock);

            foreach ($detalles as $idx => $det) {
                $idProd        = (int)$det["id_producto"];
                $cant          = (int)$det["cantidad"];
                $precioCompra  = (float)$det["precio_compra"];

                if ($idProd <= 0 || $cant <= 0 || $precioCompra < 0) {
                    throw new \Exception("Detalle de compra inválido.");
                }

                // Obtenemos stock y si afecta IGV
                $stmtProd->execute([":id" => $idProd]);
                $rowProd = $stmtProd->fetch(PDO::FETCH_ASSOC);

                if (!$rowProd) {
                    throw new \Exception("Producto ID $idProd no encontrado.");
                }

                $sub = $precioCompra * $cant;

                $detalles[$idx]["subtotal"] = $sub;

                $subtotal += $sub;

                if ((int)$rowProd["afecta_igv"] === 1) {
                    $subtotalGravado += $sub;
                }

                // Sumamos al stock
                $nuevoStock = $rowProd["stock_actual"] + $cant;
                $stmtUpdateStock->execute([
                    ":stock" => $nuevoStock,
                    ":id"    => $idProd,
                ]);
            }

            // IGV sobre lo gravado
            $igv   = round($subtotalGravado * 0.18, 2);
            $total = round($subtotal + $igv, 2);

            $cabecera["subtotal"] = $subtotal;
            $cabecera["igv"]      = $igv;
            $cabecera["total"]    = $total;

            /* 4) INSERTAR CABECERA */

            $sqlCab = "INSERT INTO tb_compras
                       (id_usuario, id_proveedor, fecha_compra,
                        id_tipo_comprobante, serie_comprobante, numero_comprobante,
                        subtotal, igv, total, estado)
                       VALUES
                       (:id_usuario, :id_proveedor, NOW(),
                        :id_tipo_comprobante, :serie, :numero,
                        :subtotal, :igv, :total, 'EMITIDA')";

            $stmtCab = $this->conexion->prepare($sqlCab);
            $stmtCab->execute([
                ":id_usuario"          => $cabecera["id_usuario"],
                ":id_proveedor"        => $cabecera["id_proveedor"],
                ":id_tipo_comprobante" => $cabecera["id_tipo_comprobante"],
                ":serie"               => $cabecera["serie_comprobante"],
                ":numero"              => $cabecera["numero_comprobante"],
                ":subtotal"            => $cabecera["subtotal"],
                ":igv"                 => $cabecera["igv"],
                ":total"               => $cabecera["total"],
            ]);

            $idCompra = $this->conexion->lastInsertId();

            /* 5) DETALLE */

            $sqlDet = "INSERT INTO tb_detalle_compras
                       (id_compra, id_producto, id_lote, cantidad, precio_compra, subtotal)
                       VALUES
                       (:id_compra, :id_producto, NULL, :cantidad, :precio_compra, :subtotal)";
            $stmtDet = $this->conexion->prepare($sqlDet);

            foreach ($detalles as $det) {
                $stmtDet->execute([
                    ":id_compra"     => $idCompra,
                    ":id_producto"   => $det["id_producto"],
                    ":cantidad"      => $det["cantidad"],
                    ":precio_compra" => $det["precio_compra"],
                    ":subtotal"      => $det["subtotal"],
                ]);
            }

            $this->conexion->commit();
            return $idCompra;

        } catch (\Exception $e) {
            $this->conexion->rollBack();
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    /* ====== CORRELATIVO PARA COMPRAS ====== */
    private function obtenerSiguienteNumero(int $idTipo, string $serie): string
    {
        $sql = "SELECT MAX(CAST(numero_comprobante AS UNSIGNED)) AS ultimo
                FROM tb_compras
                WHERE id_tipo_comprobante = :id_tipo
                  AND serie_comprobante = :serie";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_tipo" => $idTipo,
            ":serie"   => $serie,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $ultimo    = ($row && $row["ultimo"] !== null) ? (int)$row["ultimo"] : 0;
        $siguiente = $ultimo + 1;

        return str_pad($siguiente, 8, "0", STR_PAD_LEFT);
    }

    /* ====== OBTENER COMPRA COMPLETA ====== */
    public function obtenerCompraCompleta($id_compra)
    {
        $sqlCab = "SELECT
                        co.*,
                        p.tipo_documento,
                        p.numero_documento,
                        p.razon_social,
                        p.nombre_contacto,
                        tc.nombre_tipo
                   FROM tb_compras co
                   INNER JOIN tb_proveedores p
                        ON co.id_proveedor = p.id_proveedor
                   INNER JOIN tb_tipos_comprobante tc
                        ON co.id_tipo_comprobante = tc.id_tipo_comprobante
                   WHERE co.id_compra = :id";

        $stmtCab = $this->conexion->prepare($sqlCab);
        $stmtCab->execute([":id" => $id_compra]);
        $cabecera = $stmtCab->fetch(PDO::FETCH_ASSOC);

        if (!$cabecera) {
            return false;
        }

        $sqlDet = "SELECT
                        d.*,
                        p.nombre_producto
                   FROM tb_detalle_compras d
                   INNER JOIN tb_productos p
                        ON d.id_producto = p.id_producto
                   WHERE d.id_compra = :id
                   ORDER BY d.id_detalle_compra ASC";

        $stmtDet = $this->conexion->prepare($sqlDet);
        $stmtDet->execute([":id" => $id_compra]);
        $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

        return [
            "compra"   => $cabecera,
            "detalles" => $detalles,
        ];
    }

    public function obtenerComprasReporte(string $fecha_desde, string $fecha_hasta, string $tipo_filtro = "TODOS")
    {
        $sql = "SELECT
                    c.*,
                    tc.nombre_tipo,
                    p.numero_documento,
                    p.razon_social
                FROM tb_compras c
                INNER JOIN tb_tipos_comprobante tc
                    ON c.id_tipo_comprobante = tc.id_tipo_comprobante
                INNER JOIN tb_proveedores p
                    ON c.id_proveedor = p.id_proveedor
                WHERE DATE(c.fecha_compra) BETWEEN :desde AND :hasta";

        $params = [
            ':desde' => $fecha_desde,
            ':hasta' => $fecha_hasta,
        ];

        if ($tipo_filtro !== "TODOS" && $tipo_filtro !== "") {
            $sql .= " AND UPPER(tc.nombre_tipo) = :tipo";
            $params[':tipo'] = strtoupper($tipo_filtro);
        }

        $sql .= " ORDER BY c.fecha_compra ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
