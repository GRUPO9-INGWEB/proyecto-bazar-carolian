<?php
// controladores/ReporteControlador.php

require_once __DIR__ . "/../modelos/VentaModelo.php";
require_once __DIR__ . "/../modelos/CompraModelo.php";
require_once __DIR__ . "/../modelos/ProductoModelo.php";
require_once __DIR__ . "/../modelos/CategoriaModelo.php";

class ReporteControlador
{
    private $ventaModelo;
    private $compraModelo;
    private $productoModelo;
    private $categoriaModelo;

    public function __construct()
    {
        $this->ventaModelo     = new VentaModelo();
        $this->compraModelo    = new CompraModelo();
        $this->productoModelo  = new ProductoModelo();
        $this->categoriaModelo = new CategoriaModelo();
    }

    /* ================= FORMULARIO DE REPORTES DE VENTAS ================= */

    public function formularioVentas()
    {
        // Valores por defecto: hoy
        $hoy = date('Y-m-d');

        $fecha_desde = $_GET['desde'] ?? $hoy;
        $fecha_hasta = $_GET['hasta'] ?? $hoy;
        $tipo_filtro = $_GET['tipo']  ?? 'TODOS';

        // Para el combo de tipos de comprobante
        $tiposComprobante = $this->ventaModelo->obtenerTiposComprobanteActivos();

        include __DIR__ . "/../vistas/reportes/form_ventas.php";
    }

    /* ================= GENERAR PDF DE VENTAS ================= */

    public function pdfVentas()
    {
        // 1) Leer filtros del formulario
        $fecha_desde      = $_POST['fecha_desde']      ?? date('Y-m-d');
        $fecha_hasta      = $_POST['fecha_hasta']      ?? date('Y-m-d');
        $tipo_comprobante = $_POST['tipo_comprobante'] ?? 'TODOS';

        // 2) Traer datos desde el modelo
        $ventas = $this->ventaModelo->obtenerVentasReporte(
            $fecha_desde,
            $fecha_hasta,
            $tipo_comprobante
        );

        // 3) Cargar FPDF
        require_once __DIR__ . "/../libs/fpdf/fpdf.php";

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // -------- TÍTULO --------
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(190, 7, utf8_decode('Reporte de ventas'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(
            190,
            5,
            utf8_decode("Desde: $fecha_desde   Hasta: $fecha_hasta"),
            0,
            1,
            'C'
        );

        if ($tipo_comprobante !== 'TODOS') {
            $pdf->Cell(
                190,
                5,
                utf8_decode("Tipo de comprobante: $tipo_comprobante"),
                0,
                1,
                'C'
            );
        }

        $pdf->Ln(4);

        // -------- CABECERA DE TABLA --------
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(28, 6, 'Fecha',       1, 0, 'C');
        $pdf->Cell(48, 6, 'Comprobante', 1, 0, 'C');
        $pdf->Cell(54, 6, 'Cliente',     1, 0, 'C');
        $pdf->Cell(25, 6, 'T. pago',     1, 0, 'C');
        $pdf->Cell(35, 6, 'Total (S/)',  1, 1, 'C');

        $pdf->SetFont('Arial', '', 8);

        $totalGeneral = 0;

        if (!empty($ventas)) {
            foreach ($ventas as $v) {
                // Nombre cliente
                $cliente = '—';
                if (!empty($v['razon_social'])) {
                    $cliente = $v['razon_social'];
                } elseif (!empty($v['nombres']) || !empty($v['apellidos'])) {
                    $cliente = trim(($v['nombres'] ?? '') . ' ' . ($v['apellidos'] ?? ''));
                }

                $textoComp = trim(
                    $v['nombre_tipo'] . ' ' .
                    $v['serie_comprobante'] . '-' .
                    $v['numero_comprobante']
                );

                $pdf->Cell(28, 5, $v['fecha_venta'],                    1, 0, 'L');
                $pdf->Cell(48, 5, utf8_decode($textoComp),              1, 0, 'L');
                $pdf->Cell(54, 5, utf8_decode(substr($cliente, 0, 35)), 1, 0, 'L');
                $pdf->Cell(25, 5, $v['tipo_pago'],                      1, 0, 'C');
                $pdf->Cell(35, 5, number_format($v['total'], 2),        1, 1, 'R');

                $totalGeneral += (float)$v['total'];
            }
        } else {
            $pdf->Cell(
                190,
                6,
                utf8_decode('No se encontraron ventas en el rango indicado.'),
                1,
                1,
                'C'
            );
        }

        // -------- TOTAL GENERAL --------
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(155, 6, 'TOTAL GENERAL', 1, 0, 'R');
        $pdf->Cell(35, 6, number_format($totalGeneral, 2), 1, 1, 'R');

        // 4) Salida
        $pdf->Output('I', 'reporte_ventas.pdf');
    }

    /* ================= FORMULARIO DE REPORTES DE PRODUCTOS ================= */

    public function formularioProductos()
    {
        $hoy = date('Y-m-d');

        $fecha_desde = $_GET['desde'] ?? $hoy;
        $fecha_hasta = $_GET['hasta'] ?? $hoy;
        // Valor por defecto para que NO salga undefined
        $orden       = $_GET['orden'] ?? 'MAS'; // MAS, MENOS, TODOS

        include __DIR__ . "/../vistas/reportes/form_productos.php";
    }

    /* ================= GENERAR PDF DE PRODUCTOS ================= */

    public function pdfProductos()
    {
        $fecha_desde = $_POST['fecha_desde'] ?? date('Y-m-d');
        $fecha_hasta = $_POST['fecha_hasta'] ?? date('Y-m-d');
        $orden       = $_POST['orden']       ?? 'MAS';

        // 1) Obtener datos reales desde el modelo
        $productos = $this->ventaModelo->obtenerProductosVendidosReporte(
            $fecha_desde,
            $fecha_hasta,
            $orden
        );

        // 2) Generar PDF
        require_once __DIR__ . "/../libs/fpdf/fpdf.php";

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // ----- TÍTULO -----
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(190, 7, utf8_decode('Reporte de productos vendidos'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(
            190,
            5,
            utf8_decode("Desde: $fecha_desde   Hasta: $fecha_hasta"),
            0,
            1,
            'C'
        );

        $textoOrden = 'Más vendidos primero';
        if ($orden === 'MENOS') {
            $textoOrden = 'Menos vendidos primero';
        } elseif ($orden === 'TODOS') {
            $textoOrden = 'Sin ordenar';
        }

        $pdf->Cell(
            190,
            5,
            utf8_decode("Orden: $textoOrden"),
            0,
            1,
            'C'
        );

        $pdf->Ln(4);

        // ----- CABECERA TABLA -----
        $pdf->SetFont('Arial', 'B', 9);
        // 22 + 80 + 40 + 18 + 30 = 190
        $pdf->Cell(22, 6, utf8_decode('Código'),    1, 0, 'C');
        $pdf->Cell(80, 6, 'Producto',               1, 0, 'C');
        $pdf->Cell(40, 6, utf8_decode('Categoría'), 1, 0, 'C');
        $pdf->Cell(18, 6, 'Cant.',                  1, 0, 'C');
        $pdf->Cell(30, 6, 'Total (S/)',             1, 1, 'C');

        $pdf->SetFont('Arial', '', 8);

        $totalCant  = 0;
        $totalMonto = 0;

        if (!empty($productos)) {
            foreach ($productos as $p) {
                $codigo    = $p['codigo_interno']   ?? '';
                $nombre    = $p['nombre_producto']  ?? '';
                $categoria = $p['nombre_categoria'] ?? '';

                $cant  = (int)$p['cantidad_vendida'];
                $monto = (float)$p['total_vendido'];

                $pdf->Cell(22, 5, utf8_decode(substr($codigo, 0, 12)),    1, 0, 'L');
                $pdf->Cell(80, 5, utf8_decode(substr($nombre, 0, 45)),    1, 0, 'L');
                $pdf->Cell(40, 5, utf8_decode(substr($categoria, 0, 22)), 1, 0, 'L');
                $pdf->Cell(18, 5, $cant,                                   1, 0, 'C');
                $pdf->Cell(30, 5, number_format($monto, 2),                1, 1, 'R');

                $totalCant  += $cant;
                $totalMonto += $monto;
            }
        } else {
            $pdf->Cell(
                190,
                6,
                utf8_decode('No se encontraron productos vendidos en el rango indicado.'),
                1,
                1,
                'C'
            );
        }

        // ----- TOTALES -----
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 9);

        // 22 + 80 + 40 = 142
        $pdf->Cell(142, 6, 'TOTAL GENERAL', 1, 0, 'R');
        $pdf->Cell(18,  6, $totalCant,      1, 0, 'C');
        $pdf->Cell(30,  6, number_format($totalMonto, 2), 1, 1, 'R');

        $pdf->Output('I', 'reporte_productos.pdf');
    }

    /* ================= FORMULARIO DE REPORTES DE COMPRAS ================= */

    public function formularioCompras()
    {
        $hoy = date('Y-m-d');

        $fecha_desde = $_GET['desde'] ?? $hoy;
        $fecha_hasta = $_GET['hasta'] ?? $hoy;
        $tipo_filtro = $_GET['tipo']  ?? 'TODOS';

        // Reusamos los tipos de comprobante ya existentes
        $tiposComprobante = $this->ventaModelo->obtenerTiposComprobanteActivos();

        include __DIR__ . "/../vistas/reportes/form_compras.php";
    }

    /* ================= GENERAR PDF DE COMPRAS (MERCADERÍA INGRESADA) ================= */

    public function pdfCompras()
    {
        $fecha_desde      = $_POST['fecha_desde']      ?? date('Y-m-d');
        $fecha_hasta      = $_POST['fecha_hasta']      ?? date('Y-m-d');
        $tipo_comprobante = $_POST['tipo_comprobante'] ?? 'TODOS';

        // Datos desde el modelo de compras
        $compras = $this->compraModelo->obtenerComprasReporte(
            $fecha_desde,
            $fecha_hasta,
            $tipo_comprobante
        );

        require_once __DIR__ . "/../libs/fpdf/fpdf.php";

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // ----- TÍTULO -----
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(190, 7, utf8_decode('Reporte de compras (mercadería ingresada)'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(
            190,
            5,
            utf8_decode("Desde: $fecha_desde   Hasta: $fecha_hasta"),
            0,
            1,
            'C'
        );

        if ($tipo_comprobante !== 'TODOS') {
            $pdf->Cell(
                190,
                5,
                utf8_decode("Tipo de comprobante: $tipo_comprobante"),
                0,
                1,
                'C'
            );
        }

        $pdf->Ln(4);

        // ----- CABECERA TABLA -----
        $pdf->SetFont('Arial', 'B', 9);
        // 28 + 60 + 67 + 35 = 190
        $pdf->Cell(28, 6, 'Fecha',        1, 0, 'C');
        $pdf->Cell(60, 6, 'Comprobante',  1, 0, 'C');
        $pdf->Cell(67, 6, 'Proveedor',    1, 0, 'C');
        $pdf->Cell(35, 6, 'Total (S/)',   1, 1, 'C');

        $pdf->SetFont('Arial', '', 8);

        $totalGeneral = 0;

        if (!empty($compras)) {
            foreach ($compras as $c) {
                $proveedor = $c['razon_social'] ?? '—';

                $textoComp = trim(
                    $c['nombre_tipo'] . ' ' .
                    $c['serie_comprobante'] . '-' .
                    $c['numero_comprobante']
                );

                $pdf->Cell(28, 5, $c['fecha_compra'],                         1, 0, 'L');
                $pdf->Cell(60, 5, utf8_decode($textoComp),                    1, 0, 'L');
                $pdf->Cell(67, 5, utf8_decode(substr($proveedor, 0, 35)),     1, 0, 'L');
                $pdf->Cell(35, 5, number_format($c['total'], 2),              1, 1, 'R');

                $totalGeneral += (float)$c['total'];
            }
        } else {
            $pdf->Cell(
                190,
                6,
                utf8_decode('No se encontraron compras en el rango indicado.'),
                1,
                1,
                'C'
            );
        }

        // ----- TOTAL GENERAL -----
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(155, 6, 'TOTAL GENERAL', 1, 0, 'R');
        $pdf->Cell(35, 6, number_format($totalGeneral, 2), 1, 1, 'R');

        $pdf->Output('I', 'reporte_compras.pdf');
    }

    /* ================= FORMULARIO DE REPORTES POR CATEGORÍAS ================= */

    public function formularioCategorias()
    {
        // categoría seleccionada (0 = todas)
        $id_categoria = isset($_GET['id_categoria']) ? (int)$_GET['id_categoria'] : 0;

        // listado de categorías activas para el combo
        $categorias = $this->categoriaModelo->obtenerCategoriasActivas();

        include __DIR__ . "/../vistas/reportes/form_categorias.php";
    }

    /* ================= GENERAR PDF: PRODUCTOS POR CATEGORÍA ================= */

    public function pdfCategorias()
    {
        $id_categoria = isset($_POST['id_categoria']) ? (int)$_POST['id_categoria'] : 0;

        // 1) Productos filtrados por categoría (0 = todas)
        $productos = $this->productoModelo->obtenerProductosPorCategoriaReporte($id_categoria);

        // 2) Obtener nombre de la categoría (si se eligió una)
        $nombreCategoria = 'Todas';
        if ($id_categoria > 0) {
            $categoria = $this->categoriaModelo->obtenerCategoriaPorId($id_categoria);
            if (!empty($categoria)) {
                $nombreCategoria = $categoria['nombre_categoria'] ?? ('ID ' . $id_categoria);
            }
        }

        // 3) Generar PDF
        require_once __DIR__ . "/../libs/fpdf/fpdf.php";

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // ----- TÍTULO -----
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(190, 7, utf8_decode('Reporte de productos por categoría'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(
            190,
            5,
            utf8_decode("Categoría: " . $nombreCategoria),
            0,
            1,
            'C'
        );

        $pdf->Ln(4);

        // ----- CABECERA TABLA -----
        $pdf->SetFont('Arial', 'B', 9);
        // 22 + 80 + 40 + 24 + 24 = 190
        $pdf->Cell(22, 6, utf8_decode('Código'),    1, 0, 'C');
        $pdf->Cell(80, 6, 'Producto',               1, 0, 'C');
        $pdf->Cell(40, 6, utf8_decode('Categoría'), 1, 0, 'C');
        $pdf->Cell(24, 6, 'Stock',                  1, 0, 'C');
        $pdf->Cell(24, 6, 'P. venta',               1, 1, 'C');

        $pdf->SetFont('Arial', '', 8);

        $totalProductos = 0;

        if (!empty($productos)) {
            foreach ($productos as $p) {
                $codigo    = $p['codigo_interno']   ?? '';
                $nombre    = $p['nombre_producto']  ?? '';
                $categoria = $p['nombre_categoria'] ?? '';
                $stock     = isset($p['stock_actual']) ? (int)$p['stock_actual'] : 0;
                $precio    = isset($p['precio_venta']) ? (float)$p['precio_venta'] : 0.0;

                $pdf->Cell(22, 5, utf8_decode(substr($codigo, 0, 12)),    1, 0, 'L');
                $pdf->Cell(80, 5, utf8_decode(substr($nombre, 0, 45)),    1, 0, 'L');
                $pdf->Cell(40, 5, utf8_decode(substr($categoria, 0, 22)), 1, 0, 'L');
                $pdf->Cell(24, 5, $stock,                                  1, 0, 'C');
                $pdf->Cell(24, 5, number_format($precio, 2),               1, 1, 'R');

                $totalProductos++;
            }
        } else {
            $pdf->Cell(
                190,
                6,
                utf8_decode('No se encontraron productos para la categoría seleccionada.'),
                1,
                1,
                'C'
            );
        }

        // ----- TOTAL PRODUCTOS -----
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 9);

        // 22 + 80 + 40 + 24 = 166
        $pdf->Cell(166, 6, 'TOTAL PRODUCTOS', 1, 0, 'R');
        $pdf->Cell(24,  6, $totalProductos,   1, 1, 'C');

        $pdf->Output('I', 'reporte_categorias.pdf');
    }
}
