<?php
// modelos/VentaModelo.php
require_once __DIR__ . "/../config/conexion.php";

class VentaModelo extends Conexion
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
       LISTADO DE VENTAS
       ======================================================== */
    public function obtenerVentas($texto = "", $orden = "recientes", $tipo_filtro = "TODOS")
    {
        // Normalizamos un poco
        $texto       = trim((string)$texto);
        $tipo_filtro = trim((string)$tipo_filtro);

        $sql = "SELECT
                    v.*,
                    tc.nombre_tipo,
                    c.numero_documento,
                    c.razon_social,
                    c.nombres,
                    c.apellidos
                FROM tb_ventas v
                INNER JOIN tb_tipos_comprobante tc
                    ON v.id_tipo_comprobante = tc.id_tipo_comprobante
                LEFT JOIN tb_clientes c
                    ON v.id_cliente = c.id_cliente
                WHERE 1 = 1";

        // Usamos SOLO placeholders posicionales (?) para evitar HY093
        $params = [];

        // Búsqueda por texto: cliente, documento o comprobante
        if ($texto !== "") {
            $sql .= " AND (
                        c.numero_documento LIKE ?
                        OR c.razon_social LIKE ?
                        OR CONCAT(c.nombres, ' ', c.apellidos) LIKE ?
                        OR CONCAT(tc.nombre_tipo, ' ', v.serie_comprobante, '-', v.numero_comprobante) LIKE ?
                    )";

            $patron = '%' . $texto . '%';

            // 4 ?  =>  4 valores
            $params[] = $patron; // numero_documento
            $params[] = $patron; // razon_social
            $params[] = $patron; // nombres + apellidos
            $params[] = $patron; // tipo + serie-numero
        }

        // Filtro por tipo de comprobante (TICKET / BOLETA / FACTURA)
        if ($tipo_filtro !== "TODOS" && $tipo_filtro !== "") {
            $sql .= " AND UPPER(tc.nombre_tipo) = ?";
            $params[] = strtoupper($tipo_filtro);
        }

        // Orden
        switch ($orden) {
            case "monto_mayor":
                $sql .= " ORDER BY v.total DESC";
                break;
            case "monto_menor":
                $sql .= " ORDER BY v.total ASC";
                break;
            case "antiguos":
                $sql .= " ORDER BY v.fecha_venta ASC";
                break;
            case "recientes":
            default:
                $sql .= " ORDER BY v.fecha_venta DESC";
                break;
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($params);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ========================================================
       DATOS PARA COMBOS EN NUEVA VENTA
       ======================================================== */

    public function obtenerTiposComprobanteActivos()
    {
        $sql = "SELECT id_tipo_comprobante, nombre_tipo
                FROM tb_tipos_comprobante
                WHERE estado = 1
                ORDER BY nombre_tipo ASC";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerClientesActivos()
    {
        $sql = "SELECT id_cliente, tipo_documento, numero_documento,
                       nombres, apellidos, razon_social
                FROM tb_clientes
                WHERE estado = 1
                ORDER BY razon_social ASC, apellidos ASC, nombres ASC";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosActivos()
    {
        $sql = "SELECT 
                    p.id_producto,
                    p.codigo_interno,
                    p.nombre_producto,
                    p.stock_actual,
                    p.precio_venta,
                    p.afecta_igv
                FROM tb_productos p
                WHERE p.estado = 1
                ORDER BY p.nombre_producto ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ========================================================
       REGISTRAR VENTA (cabecera + detalle + actualización stock)
       ======================================================== */
    public function registrarVenta($cabecera, $detalles)
    {
        // Limpiamos error previo
        $this->ultimoError = "";

        try {
            // Empezamos transacción
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
                    default: // TICKET u otros
                        $cabecera["serie_comprobante"] = "T001";
                        break;
                }
            }

            /* 2) GENERAR NÚMERO CORRELATIVO SI VIENE VACÍO */
            if (empty($cabecera["numero_comprobante"])) {
                $cabecera["numero_comprobante"] = $this->obtenerSiguienteNumero(
                    (int)$cabecera["id_tipo_comprobante"],
                    $cabecera["serie_comprobante"]
                );
            }

            /* 3) CALCULAR TOTALES Y ACTUALIZAR STOCK */

            $subtotal        = 0;
            $subtotalGravado = 0; // solo productos que afectan IGV

            $sqlProd = "SELECT precio_venta, stock_actual, afecta_igv
                        FROM tb_productos
                        WHERE id_producto = :id
                        FOR UPDATE";
            $stmtProd = $this->conexion->prepare($sqlProd);

            $sqlUpdateStock = "UPDATE tb_productos
                               SET stock_actual = :stock
                               WHERE id_producto = :id";
            $stmtUpdateStock = $this->conexion->prepare($sqlUpdateStock);

            foreach ($detalles as $idx => $det) {
                $idProd = (int)$det["id_producto"];
                $cant   = (int)$det["cantidad"];

                $stmtProd->execute([":id" => $idProd]);
                $rowProd = $stmtProd->fetch(PDO::FETCH_ASSOC);

                if (!$rowProd) {
                    throw new \Exception("Producto ID $idProd no encontrado.");
                }

                if ($rowProd["stock_actual"] < $cant) {
                    throw new \Exception("Stock insuficiente para el producto ID $idProd.");
                }

                $precio = (float)$rowProd["precio_venta"];
                $sub    = $precio * $cant;

                $detalles[$idx]["precio_venta"] = $precio;
                $detalles[$idx]["subtotal"]     = $sub;

                $subtotal += $sub;

                if ((int)$rowProd["afecta_igv"] === 1) {
                    $subtotalGravado += $sub;
                }

                $nuevoStock = $rowProd["stock_actual"] - $cant;
                $stmtUpdateStock->execute([
                    ":stock" => $nuevoStock,
                    ":id"    => $idProd,
                ]);
            }

            $igv   = round($subtotalGravado * 0.18, 2);
            $total = round($subtotal + $igv, 2);

            $cabecera["subtotal"] = $subtotal;
            $cabecera["igv"]      = $igv;
            $cabecera["total"]    = $total;

            if (
                isset($cabecera["tipo_pago"]) &&
                $cabecera["tipo_pago"] === "EFECTIVO" &&
                isset($cabecera["monto_recibido"]) &&
                $cabecera["monto_recibido"] !== null
            ) {
                $cabecera["vuelto"] = max((float)$cabecera["monto_recibido"] - $total, 0);
            }

            /* 4) INSERTAR CABECERA DE VENTA */

            $sqlCab = "INSERT INTO tb_ventas
                       (id_usuario, id_cliente, id_tipo_comprobante,
                        serie_comprobante, numero_comprobante,
                        subtotal, igv, total, tipo_pago,
                        monto_recibido, vuelto, estado)
                       VALUES
                       (:id_usuario, :id_cliente, :id_tipo_comprobante,
                        :serie, :numero,
                        :subtotal, :igv, :total, :tipo_pago,
                        :monto_recibido, :vuelto, 'EMITIDA')";

            $stmtCab = $this->conexion->prepare($sqlCab);
            $stmtCab->execute([
                ":id_usuario"          => $cabecera["id_usuario"],
                ":id_cliente"          => !empty($cabecera["id_cliente"]) ? $cabecera["id_cliente"] : null,
                ":id_tipo_comprobante" => $cabecera["id_tipo_comprobante"],
                ":serie"               => $cabecera["serie_comprobante"],
                ":numero"              => $cabecera["numero_comprobante"],
                ":subtotal"            => $cabecera["subtotal"],
                ":igv"                 => $cabecera["igv"],
                ":total"               => $cabecera["total"],
                ":tipo_pago"           => $cabecera["tipo_pago"],
                ":monto_recibido"      => $cabecera["monto_recibido"] ?? null,
                ":vuelto"              => $cabecera["vuelto"] ?? null,
            ]);

            $idVenta = $this->conexion->lastInsertId();

            /* 5) INSERTAR DETALLE */

            $sqlDet = "INSERT INTO tb_detalle_ventas
                       (id_venta, id_producto, cantidad, precio_venta, descuento, subtotal)
                       VALUES
                       (:id_venta, :id_producto, :cantidad, :precio, 0, :subtotal)";
            $stmtDet = $this->conexion->prepare($sqlDet);

            foreach ($detalles as $det) {
                $stmtDet->execute([
                    ":id_venta"    => $idVenta,
                    ":id_producto" => $det["id_producto"],
                    ":cantidad"    => $det["cantidad"],
                    ":precio"      => $det["precio_venta"],
                    ":subtotal"    => $det["subtotal"],
                ]);
            }

            $this->conexion->commit();
            return $idVenta;

        } catch (\Exception $e) {
            $this->conexion->rollBack();
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    /* ====== CORRELATIVO ====== */
    private function obtenerSiguienteNumero(int $idTipo, string $serie): string
    {
        $sql = "SELECT MAX(CAST(numero_comprobante AS UNSIGNED)) AS ultimo
                FROM tb_ventas
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

    /* ====== DETALLE COMPLETO PARA VER / ENVIAR VENTA ====== */
    public function obtenerVentaCompleta($id_venta)
    {
        // CABECERA
        $sqlCab = "SELECT
                        v.*,
                        tc.nombre_tipo,
                        c.tipo_documento,
                        c.numero_documento,
                        c.razon_social,
                        c.nombres,
                        c.apellidos,
                        c.correo AS correo_cliente
                   FROM tb_ventas v
                   INNER JOIN tb_tipos_comprobante tc
                       ON v.id_tipo_comprobante = tc.id_tipo_comprobante
                   LEFT JOIN tb_clientes c
                       ON v.id_cliente = c.id_cliente
                   WHERE v.id_venta = :id";

        $stmtCab = $this->conexion->prepare($sqlCab);
        $stmtCab->bindParam(":id", $id_venta, PDO::PARAM_INT);
        $stmtCab->execute();
        $cabecera = $stmtCab->fetch(PDO::FETCH_ASSOC);

        if (!$cabecera) {
            return false;
        }

        // DETALLE
        $sqlDet = "SELECT
                        d.*,
                        p.nombre_producto
                   FROM tb_detalle_ventas d
                   INNER JOIN tb_productos p
                        ON d.id_producto = p.id_producto
                   WHERE d.id_venta = :id
                   ORDER BY d.id_detalle_venta ASC";

        $stmtDet = $this->conexion->prepare($sqlDet);
        $stmtDet->bindParam(":id", $id_venta, PDO::PARAM_INT);
        $stmtDet->execute();
        $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

        return [
            "venta"    => $cabecera,
            "detalles" => $detalles
        ];
    }

    /* ====== MARCAR QUE EL CORREO SE ENVIÓ ====== */
    public function marcarCorreoEnviado($id_venta, $valor = 1)
    {
        $sql = "UPDATE tb_ventas
                SET correo_enviado = :valor
                WHERE id_venta = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':valor' => (int)$valor,
            ':id'    => (int)$id_venta,
        ]);
    }

    /* ========================================================
       VENTAS PARA REPORTE (por rango de fechas y tipo)
       ======================================================== */
    public function obtenerVentasReporte($fecha_desde, $fecha_hasta, $tipo_filtro = "TODOS")
    {
        $sql = "SELECT
                    v.*,
                    tc.nombre_tipo,
                    c.numero_documento,
                    c.razon_social,
                    c.nombres,
                    c.apellidos
                FROM tb_ventas v
                INNER JOIN tb_tipos_comprobante tc
                    ON v.id_tipo_comprobante = tc.id_tipo_comprobante
                LEFT JOIN tb_clientes c
                    ON v.id_cliente = c.id_cliente
                WHERE DATE(v.fecha_venta) BETWEEN :desde AND :hasta";

        $params = [
            ':desde' => $fecha_desde,
            ':hasta' => $fecha_hasta,
        ];

        // Filtro por tipo de comprobante (TICKET / BOLETA / FACTURA)
        if ($tipo_filtro !== "TODOS" && $tipo_filtro !== "") {
            $sql .= " AND UPPER(tc.nombre_tipo) = :tipo";
            $params[':tipo'] = strtoupper($tipo_filtro);
        }

        $sql .= " ORDER BY v.fecha_venta ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ======================= REPORTE: PRODUCTOS VENDIDOS ======================= */
    public function obtenerProductosVendidosReporte(string $fecha_desde, string $fecha_hasta, string $orden = 'MAS')
    {
        $sql = "SELECT
                    p.id_producto,
                    p.codigo_interno,
                    p.nombre_producto,
                    c.nombre_categoria,
                    SUM(d.cantidad) AS cantidad_vendida,
                    SUM(d.subtotal) AS total_vendido
                FROM tb_detalle_ventas d
                INNER JOIN tb_ventas v
                    ON v.id_venta = d.id_venta
                INNER JOIN tb_productos p
                    ON p.id_producto = d.id_producto
                LEFT JOIN tb_categorias c
                    ON p.id_categoria = c.id_categoria
                WHERE DATE(v.fecha_venta) BETWEEN :desde AND :hasta
                  AND v.estado = 'EMITIDA'
                GROUP BY
                    p.id_producto,
                    p.codigo_interno,
                    p.nombre_producto,
                    c.nombre_categoria";

        // Orden según parámetro
        switch ($orden) {
            case 'MENOS':
                $sql .= " ORDER BY cantidad_vendida ASC";
                break;
            case 'TODOS':
                $sql .= " ORDER BY p.nombre_producto ASC";
                break;
            case 'MAS':
            default:
                $sql .= " ORDER BY cantidad_vendida DESC";
                break;
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':desde' => $fecha_desde,
            ':hasta' => $fecha_hasta,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===================== RESUMEN DE CAJA / CIERRE DIARIO =====================
    public function obtenerResumenCaja(string $fecha_desde, string $fecha_hasta): array
    {
        // Normalizamos fechas (solo parte YYYY-MM-DD)
        $fecha_desde = substr($fecha_desde, 0, 10);
        $fecha_hasta = substr($fecha_hasta, 0, 10);

        $sql = "SELECT
                    DATE(v.fecha_venta)     AS fecha,
                    v.tipo_pago             AS tipo_pago,
                    COUNT(*)                AS cantidad_ventas,
                    SUM(v.subtotal)         AS total_subtotal,
                    SUM(v.igv)              AS total_igv,
                    SUM(v.total)            AS total_general
                FROM tb_ventas v
                WHERE DATE(v.fecha_venta) BETWEEN :desde AND :hasta
                GROUP BY DATE(v.fecha_venta), v.tipo_pago
                ORDER BY fecha ASC, tipo_pago ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':desde', $fecha_desde, PDO::PARAM_STR);
        $stmt->bindParam(':hasta', $fecha_hasta, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
