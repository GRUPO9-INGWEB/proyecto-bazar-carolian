<?php
// controladores/CajaControlador.php

require_once __DIR__ . "/../modelos/VentaModelo.php";
require_once __DIR__ . "/../modelos/AuditoriaModelo.php";

class CajaControlador
{
    private $ventaModelo;
    private $auditoriaModelo;

    public function __construct()
    {
        $this->ventaModelo     = new VentaModelo();
        $this->auditoriaModelo = new AuditoriaModelo();
    }

    /**
     * Vista de cierre de caja / resumen de ventas por día y tipo de pago
     */
    public function cierreDiario(string $mensaje = "")
    {
        // Rango por defecto: hoy
        $hoy = date('Y-m-d');

        $fecha_desde = $_GET['desde'] ?? $hoy;
        $fecha_hasta = $_GET['hasta'] ?? $hoy;

        // Si el usuario tocó "Limpiar"
        if (isset($_GET['limpiar'])) {
            $fecha_desde = $hoy;
            $fecha_hasta = $hoy;
            $_GET = [];
        }

        // Obtenemos el resumen desde el modelo
        $resumen = $this->ventaModelo->obtenerResumenCaja($fecha_desde, $fecha_hasta);

        // Totales generales
        $totalSub  = 0;
        $totalIgv  = 0;
        $totalGral = 0;
        $totalCant = 0;

        foreach ($resumen as $fila) {
            $totalSub  += (float)$fila['total_subtotal'];
            $totalIgv  += (float)$fila['total_igv'];
            $totalGral += (float)$fila['total_general'];
            $totalCant += (int)$fila['cantidad_ventas'];
        }

        // Auditoría: consulta de cierre
        $this->registrarAuditoriaCaja(
            "CONSULTAR_CIERRE",
            "Consulta de cierre de caja desde $fecha_desde hasta $fecha_hasta"
        );

        include __DIR__ . "/../vistas/caja/cierre_diario.php";
    }

    /**
     * Generar PDF de cierre de caja
     */
    public function pdfCierre(): void
    {
        // 1) Leer filtros desde el formulario
        $hoy = date('Y-m-d');

        $fecha_desde = $_POST['fecha_desde'] ?? $hoy;
        $fecha_hasta = $_POST['fecha_hasta'] ?? $hoy;

        // 2) Traer datos
        $resumen = $this->ventaModelo->obtenerResumenCaja($fecha_desde, $fecha_hasta);

        $totalSub  = 0;
        $totalIgv  = 0;
        $totalGral = 0;
        $totalCant = 0;

        foreach ($resumen as $fila) {
            $totalSub  += (float)$fila['total_subtotal'];
            $totalIgv  += (float)$fila['total_igv'];
            $totalGral += (float)$fila['total_general'];
            $totalCant += (int)$fila['cantidad_ventas'];
        }

        // 3) FPDF
        require_once __DIR__ . "/../libs/fpdf/fpdf.php";

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // Título
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(190, 7, utf8_decode('Cierre de caja / resumen de ventas'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(
            190,
            5,
            utf8_decode("Desde: $fecha_desde   Hasta: $fecha_hasta"),
            0,
            1,
            'C'
        );
        $pdf->Ln(4);

        // Cabecera tabla
        $pdf->SetFont('Arial', 'B', 9);
        // 25 + 35 + 25 + 35 + 30 + 40 = 190
        $pdf->Cell(25, 6, 'Fecha',          1, 0, 'C');
        $pdf->Cell(35, 6, 'Tipo pago',      1, 0, 'C');
        $pdf->Cell(25, 6, '# ventas',       1, 0, 'C');
        $pdf->Cell(35, 6, 'Subtotal (S/)',  1, 0, 'C');
        $pdf->Cell(30, 6, 'IGV (S/)',       1, 0, 'C');
        $pdf->Cell(40, 6, 'Total (S/)',     1, 1, 'C');

        $pdf->SetFont('Arial', '', 8);

        if (!empty($resumen)) {
            foreach ($resumen as $fila) {
                $pdf->Cell(25, 5, $fila['fecha'],                        1, 0, 'L');
                $pdf->Cell(35, 5, utf8_decode($fila['tipo_pago']),       1, 0, 'L');
                $pdf->Cell(25, 5, (int)$fila['cantidad_ventas'],         1, 0, 'C');
                $pdf->Cell(35, 5, number_format($fila['total_subtotal'], 2), 1, 0, 'R');
                $pdf->Cell(30, 5, number_format($fila['total_igv'], 2),       1, 0, 'R');
                $pdf->Cell(40, 5, number_format($fila['total_general'], 2),    1, 1, 'R');
            }

            // Totales
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(60, 6, 'TOTALES', 1, 0, 'R');
            $pdf->Cell(25, 6, $totalCant,                       1, 0, 'C');
            $pdf->Cell(35, 6, number_format($totalSub, 2),      1, 0, 'R');
            $pdf->Cell(30, 6, number_format($totalIgv, 2),      1, 0, 'R');
            $pdf->Cell(40, 6, number_format($totalGral, 2),     1, 1, 'R');
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

        // 4) Auditoría
        $this->registrarAuditoriaCaja(
            "IMPRIMIR_CIERRE_PDF",
            "Impresión de cierre de caja desde $fecha_desde hasta $fecha_hasta"
        );

        // 5) Salida
        $pdf->Output('I', 'cierre_caja.pdf');
    }

    /* ===== Helper para auditoría ===== */

    private function registrarAuditoriaCaja(string $accion, string $descripcion): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = $_SESSION['id_usuario'] ?? null;
        if (!$idUsuario) {
            return;
        }

        $this->auditoriaModelo->registrarEvento(
            $idUsuario,
            'CAJA',
            $accion,
            $descripcion,
            null,
            null
        );
    }
}
