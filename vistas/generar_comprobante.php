<?php
// 1. Incluimos las librerías
require('../fpdf/fpdf.php'); // Incluir la librería FPDF
include_once "../conexion.php";
include_once "../modelos/venta_modelo.php";
include_once "../servicios/enviar_email.php"; // ¡INCLUIMOS EL SERVICIO DE EMAIL!

// 2. Verificamos que se haya enviado un ID
if (!isset($_GET['id'])) {
    echo "Error: No se proporcionó ID de venta.";
    exit;
}
$id_venta = $_GET['id'];

// 3. Obtenemos los datos de la venta
$venta = obtenerVentaCompleta($conexion, $id_venta);
$detalles = obtenerDetalleVenta($conexion, $id_venta);

if (!$venta) {
    echo "Error: Venta no encontrada.";
    exit;
}

// 4. CREACIÓN DEL PDF
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial','B',15);
        $this->Cell(80); // Mover a la derecha
        $this->Cell(30,10,'Bazar Carolian',0,0,'C');
        $this->Ln(5); // Salto de línea
        
        $this->SetFont('Arial','',10);
        $this->Cell(80);
        $this->Cell(30,10,utf8_decode('RUC: 10234567891'),0,0,'C');
        $this->Ln(5);
        $this->Cell(80);
        $this->Cell(30,10,utf8_decode('Av. Ejemplo 123, Lima'),0,0,'C');
        $this->Ln(20); // Salto de línea grande
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15); // Posición a 1.5 cm del final
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Gracias por su compra'),0,0,'C');
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo(),0,0,'R');
    }
}

// Inicializamos el PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

// 5. DATOS DEL COMPROBANTE Y CLIENTE
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0, 10, utf8_decode(strtoupper($venta['tipo_comprobante'])), 0, 1, 'C');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(130, 7, utf8_decode('N° Venta: ' . $venta['id_venta']), 0, 0);
$pdf->Cell(60, 7, utf8_decode('Fecha: ' . date('d/m/Y H:i', strtotime($venta['fecha_hora']))), 0, 1);
$pdf->Cell(130, 7, utf8_decode('Vendedor: ' . $venta['nombre_vendedor']), 0, 1);
$pdf->Ln(5);

// Datos del Cliente
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0, 7, utf8_decode('DATOS DEL CLIENTE'), 0, 1, 'L');
$pdf->SetFont('Arial','',10);
if ($venta['id_cliente']) {
    $pdf->Cell(130, 7, utf8_decode('Nombre/Razón Social: ' . $venta['nombre_cliente']), 0, 0);
    $pdf->Cell(60, 7, utf8_decode($venta['documento_tipo'] . ': ' . $venta['documento_numero']), 0, 1);
    $pdf->Cell(130, 7, utf8_decode('Dirección: ' . $venta['direccion_cliente']), 0, 1);
} else {
    $pdf->Cell(130, 7, utf8_decode('Cliente: Varios (Venta simple)'), 0, 1);
}
$pdf->Ln(10); // Salto de línea

// 6. TABLA DE DETALLES DE PRODUCTOS
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(230, 230, 230); // Color de fondo gris claro
$pdf->Cell(20, 7, 'Cant.', 1, 0, 'C', true);
$pdf->Cell(90, 7, 'Producto', 1, 0, 'C', true);
$pdf->Cell(40, 7, 'P. Unitario', 1, 0, 'C', true);
$pdf->Cell(40, 7, 'Importe', 1, 1, 'C', true);

$pdf->SetFont('Arial','',10);
while ($item = $detalles->fetch_assoc()) {
    $importe_item = $item['cantidad'] * $item['precio_unitario'];
    $pdf->Cell(20, 7, $item['cantidad'], 1, 0, 'C');
    $pdf->Cell(90, 7, utf8_decode($item['nombre_producto']), 1, 0, 'L');
    $pdf->Cell(40, 7, 'S/ ' . number_format($item['precio_unitario'], 2), 1, 0, 'R');
    $pdf->Cell(40, 7, 'S/ ' . number_format($importe_item, 2), 1, 1, 'R');
}
$pdf->Ln(10); // Salto de línea

// 7. TOTALES
$pdf->SetFont('Arial','B',12);
$pdf->Cell(130, 8, '', 0, 0); // Espacio vacío
$pdf->Cell(30, 8, 'Subtotal:', 0, 0, 'R');
$pdf->Cell(30, 8, 'S/ ' . number_format($venta['subtotal'], 2), 0, 1, 'R');

$pdf->Cell(130, 8, '', 0, 0);
$pdf->Cell(30, 8, 'IGV (18%):', 0, 0, 'R');
$pdf->Cell(30, 8, 'S/ ' . number_format($venta['igv'], 2), 0, 1, 'R');

$pdf->SetFont('Arial','B',14);
$pdf->Cell(130, 8, '', 0, 0);
$pdf->Cell(30, 8, 'TOTAL:', 0, 0, 'R');
$pdf->Cell(30, 8, 'S/ ' . number_format($venta['total'], 2), 0, 1, 'R');
$pdf->Ln(5);

// 8. Método de pago
$pdf->SetFont('Arial','',10);
$pdf->Cell(0, 7, utf8_decode('Método de Pago: ' . $venta['metodo_pago']), 0, 1, 'L');
if ($venta['metodo_pago'] == 'Efectivo') {
    $pdf->Cell(0, 7, utf8_decode('Recibido: S/ ' . number_format($venta['monto_recibido'], 2)), 0, 1, 'L');
    $pdf->Cell(0, 7, utf8_decode('Vuelto: S/ ' . number_format($venta['vuelto'], 2)), 0, 1, 'L');
}


/* * ======================================================
 * ¡NUEVA SECCIÓN DE ENVÍO DE CORREO!
 * ======================================================
 */
try {
    // 1. Definimos la ruta donde se guardará el PDF
    $nombre_archivo = 'Comprobante_Venta_' . $id_venta . '.pdf';
    // Usamos __DIR__ para obtener la ruta absoluta del directorio actual (vistas)
    // y luego retrocedemos un nivel para entrar a 'comprobantes'
    $ruta_pdf = __DIR__ . '/../comprobantes/' . $nombre_archivo;

    // 2. Guardamos el PDF en el servidor
    $pdf->Output('F', $ruta_pdf); // 'F' = Guardar en archivo
    
    // 3. Intentamos enviar el correo (la función está en servicios/enviar_email.php)
    enviarComprobantePorEmail($venta, $ruta_pdf);
    
    // 4. Opcional: Borramos el archivo PDF del servidor después de enviarlo
    // unlink($ruta_pdf); 
    // (Lo dejamos comentado para poder revisar si se crea bien)

} catch (Exception $e) {
    // Si algo falla con el email, no rompemos la página
    // Opcional: registrar el error $e->getMessage()
}


// 9. Salida del PDF al navegador
// Esto se ejecuta después de intentar enviar el email
$pdf->Output('I', $nombre_archivo); // 'I' envía al navegador
?>