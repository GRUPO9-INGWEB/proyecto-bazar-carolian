<?php
// controladores/VentaControlador.php
require_once __DIR__ . "/../modelos/VentaModelo.php";
require_once __DIR__ . "/../modelos/AuditoriaModelo.php";   // <<< NUEVO

// PHPMailer (librería para enviar correos)
require_once __DIR__ . "/../libs/PHPMailer/src/PHPMailer.php";
require_once __DIR__ . "/../libs/PHPMailer/src/SMTP.php";
require_once __DIR__ . "/../libs/PHPMailer/src/Exception.php";

// Configuración SMTP
require_once __DIR__ . "/../config/correo_config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class VentaControlador
{
    private $modelo;
    private $auditoriaModelo;   // <<< NUEVO

    public function __construct()
    {
        $this->modelo          = new VentaModelo();
        $this->auditoriaModelo = new AuditoriaModelo(); // <<< NUEVO
    }

    /* ================= LISTADO ================= */

    public function listar($mensaje = "")
    {
        $texto  = trim($_GET["buscar"] ?? "");
        $orden  = $_GET["orden"] ?? "recientes";

        // Filtro por tipo de comprobante:
        // valores posibles: TODOS, TICKET, BOLETA, FACTURA
        $tipo_filtro = $_GET["tipo_filtro"] ?? "TODOS";

        $lista_ventas = $this->modelo->obtenerVentas($texto, $orden, $tipo_filtro);

        $texto_busqueda     = $texto;
        $orden_seleccionado = $orden;
        $tipo_filtro_actual = $tipo_filtro;

        // $mensaje queda disponible para la vista
        include __DIR__ . "/../vistas/ventas/listado_ventas.php";
    }

    /* ================= NUEVA VENTA ================= */

    public function formularioNueva($mensaje = "")
    {
        $tipos_comprobante = $this->modelo->obtenerTiposComprobanteActivos();
        $clientes          = $this->modelo->obtenerClientesActivos();
        $productos         = $this->modelo->obtenerProductosActivos();

        include __DIR__ . "/../vistas/ventas/formulario_venta.php";
    }

    public function guardarNueva()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->listar();
            return;
        }

        // Evitar el notice de session_start duplicado
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_usuario = $_SESSION["id_usuario"] ?? 0;

        /* ====== TOTALES DESDE FORMULARIO ====== */
        $subtotal_form = (float)($_POST["subtotal_general"] ?? 0);
        $igv_form      = (float)($_POST["igv_general"] ?? 0);
        $total_form    = (float)($_POST["total_general"] ?? 0);

        $tipo_pago = trim($_POST["tipo_pago"] ?? "EFECTIVO");

        // Limpiamos número por si viene la frase del placeholder
        $numero_comprobante = trim($_POST["numero_comprobante"] ?? "");
        if (
            $numero_comprobante !== "" &&
            stripos($numero_comprobante, "se generará") === 0
        ) {
            // si comienza con "Se generará..." lo tratamos como vacío
            $numero_comprobante = "";
        }

        $cabecera = [
            "id_usuario"          => $id_usuario,
            "id_cliente"          => (int)($_POST["id_cliente"] ?? 0),
            "id_tipo_comprobante" => (int)($_POST["id_tipo_comprobante"] ?? 0),
            "serie_comprobante"   => trim($_POST["serie_comprobante"] ?? ""),
            "numero_comprobante"  => $numero_comprobante,
            "tipo_pago"           => $tipo_pago,
            "subtotal"            => $subtotal_form,
            "igv"                 => $igv_form,
            "total"               => $total_form,
            "monto_recibido"      => null,
            "vuelto"              => null,
        ];

        /* ====== EFECTIVO: MONTO RECIBIDO Y VUELTO ====== */
        if ($tipo_pago === "EFECTIVO") {
            $monto_recibido = (float)($_POST["monto_recibido"] ?? 0);

            // Validamos que el monto recibido alcance para pagar el total
            if ($total_form > 0 && $monto_recibido < $total_form) {
                $mensaje = "El monto recibido (" . number_format($monto_recibido, 2) .
                           ") no puede ser menor al total (" . number_format($total_form, 2) . ").";
                $this->formularioNueva($mensaje);
                return;
            }

            $cabecera["monto_recibido"] = $monto_recibido;
            $cabecera["vuelto"]         = max($monto_recibido - $total_form, 0);
        }

        /* ====== DETALLE ====== */
        $idsProd    = $_POST["id_producto"] ?? [];
        $cantidades = $_POST["cantidad"] ?? [];

        $detalles = [];
        for ($i = 0; $i < count($idsProd); $i++) {
            $idProd = (int)$idsProd[$i];
            $cant   = (int)($cantidades[$i] ?? 0);

            if ($idProd > 0 && $cant > 0) {
                $detalles[] = [
                    "id_producto" => $idProd,
                    "cantidad"    => $cant,
                ];
            }
        }

        /* ====== VALIDACIONES ====== */

        // ⚠ IMPORTANTE:
        // Cambia este valor si el ID de TICKET en tu tabla tb_tipos_comprobante NO es 1
        $ID_TICKET = 1;

        // Cliente obligatorio para Boleta/Factura, pero no para Ticket
        if ($cabecera["id_cliente"] <= 0 && $cabecera["id_tipo_comprobante"] != $ID_TICKET) {
            $mensaje = "Debe seleccionar un cliente para Boleta o Factura.";
            $this->formularioNueva($mensaje);
            return;
        }

        if ($cabecera["id_tipo_comprobante"] <= 0) {
            $mensaje = "Debe seleccionar un tipo de comprobante.";
            $this->formularioNueva($mensaje);
            return;
        }

        if (empty($detalles)) {
            $mensaje = "Debe agregar al menos un producto a la venta.";
            $this->formularioNueva($mensaje);
            return;
        }

        if ($total_form <= 0) {
            $mensaje = "El total de la venta debe ser mayor a cero.";
            $this->formularioNueva($mensaje);
            return;
        }

        /* ====== REGISTRO EN BD ====== */
        $idVenta = $this->modelo->registrarVenta($cabecera, $detalles);

        if ($idVenta === false) {
            $error = $this->modelo->getUltimoError();
            if ($error === "") {
                $error = "No se pudo registrar la venta. Verifique los datos.";
            }
            $this->formularioNueva($error);
            return;
        }

        /* ====== AUDITORÍA: REGISTRO DE VENTA ====== */
        if ($id_usuario > 0) {
            $descripcion = 'Registro de venta ID ' . (int)$idVenta;

            $this->auditoriaModelo->registrarEvento(
                (int)$id_usuario,      // quién hizo la venta
                'VENTAS',              // módulo
                'REGISTRAR',           // acción
                $descripcion,          // descripción
                'tb_ventas',           // tabla afectada
                (int)$idVenta          // id del registro afectado
            );
        }

        $mensaje = "Venta registrada correctamente (ID: $idVenta).";
        $this->listar($mensaje);
    }

    /* ================= VER DETALLE ================= */

    public function ver($id_venta)
    {
        $data = $this->modelo->obtenerVentaCompleta($id_venta);

        if ($data === false) {
            $this->listar("No se encontró la venta indicada.");
            return;
        }

        $venta    = $data["venta"];
        $detalles = $data["detalles"];

        include __DIR__ . "/../vistas/ventas/ver_venta.php";
    }

    /* ================= IMPRIMIR COMPROBANTE EN PDF ================= */

    public function imprimirPdf($id_venta)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1) Traemos los datos de la venta
        $data = $this->modelo->obtenerVentaCompleta($id_venta);
        if ($data === false) {
            echo "No se encontró la venta indicada para imprimir.";
            return;
        }

        $venta    = $data["venta"];
        $detalles = $data["detalles"];

        // ===== AUDITORÍA: IMPRESIÓN DE COMPROBANTE =====
        if (isset($_SESSION['id_usuario'])) {
            $this->auditoriaModelo->registrarEvento(
                (int)$_SESSION['id_usuario'],
                'VENTAS',
                'IMPRIMIR_PDF',
                'Impresión de comprobante de venta ID ' . (int)$id_venta,
                'tb_ventas',
                (int)$id_venta
            );
        }

        // 2) Cargamos FPDF
        require_once __DIR__ . "/../libs/fpdf/fpdf.php";

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // -------- ENCABEZADO --------
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(190, 8, 'Bazar Carolian', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(190, 5, 'Sistema de inventario y ventas', 0, 1, 'C');
        $pdf->Ln(5);

        // Tipo y número de comprobante
        $textoComprobante = trim(
            $venta["nombre_tipo"] . " " .
            $venta["serie_comprobante"] . "-" .
            $venta["numero_comprobante"]
        );

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(190, 6, utf8_decode($textoComprobante), 0, 1, 'C');
        $pdf->Ln(4);

        // -------- DATOS PRINCIPALES --------
        $pdf->SetFont('Arial', '', 10);

        // Fecha y tipo de pago
        $pdf->Cell(95, 5, 'Fecha: ' . $venta["fecha_venta"], 0, 0, 'L');
        $pdf->Cell(95, 5, 'Tipo de pago: ' . $venta["tipo_pago"], 0, 1, 'R');

        // Cliente (si existe)
        $nombreCliente = "—";
        if (!empty($venta["razon_social"])) {
            $nombreCliente = $venta["razon_social"];
        } elseif (!empty($venta["nombres"]) || !empty($venta["apellidos"])) {
            $nombreCliente = trim(($venta["nombres"] ?? "") . " " . ($venta["apellidos"] ?? ""));
        }

        $documentoCliente = "";
        if (!empty($venta["numero_documento"])) {
            $documentoCliente = ($venta["tipo_documento"] ?? "") . " " . $venta["numero_documento"];
        }

        $pdf->Cell(190, 5, 'Cliente: ' . utf8_decode($nombreCliente), 0, 1, 'L');
        if ($documentoCliente !== "") {
            $pdf->Cell(190, 5, 'Documento: ' . $documentoCliente, 0, 1, 'L');
        }

        // Monto recibido y vuelto (solo efectivo, si existen columnas)
        if ($venta["tipo_pago"] === "EFECTIVO") {
            if (isset($venta["monto_recibido"]) && $venta["monto_recibido"] !== null) {
                $pdf->Cell(95, 5, 'Monto recibido: S/ ' . number_format($venta["monto_recibido"], 2), 0, 0, 'L');
            } else {
                $pdf->Cell(95, 5, 'Monto recibido: S/ -', 0, 0, 'L');
            }
            if (isset($venta["vuelto"]) && $venta["vuelto"] !== null) {
                $pdf->Cell(95, 5, 'Vuelto: S/ ' . number_format($venta["vuelto"], 2), 0, 1, 'R');
            } else {
                $pdf->Cell(95, 5, 'Vuelto: S/ -', 0, 1, 'R');
            }
        }

        $pdf->Ln(4);

        // -------- TABLA DE PRODUCTOS --------
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 7, '#', 1, 0, 'C');
        $pdf->Cell(90, 7, 'Producto', 1, 0, 'L');
        $pdf->Cell(20, 7, 'Cant.', 1, 0, 'R');
        $pdf->Cell(35, 7, 'P. Unitario', 1, 0, 'R');
        $pdf->Cell(35, 7, 'Subtotal', 1, 1, 'R');

        $pdf->SetFont('Arial', '', 10);

        $i = 1;
        foreach ($detalles as $d) {
            $pdf->Cell(10, 6, $i++, 1, 0, 'C');
            $pdf->Cell(90, 6, utf8_decode($d["nombre_producto"]), 1, 0, 'L');
            $pdf->Cell(20, 6, (int)$d["cantidad"], 1, 0, 'R');
            $pdf->Cell(35, 6, 'S/ ' . number_format($d["precio_venta"], 2), 1, 0, 'R');
            $pdf->Cell(35, 6, 'S/ ' . number_format($d["subtotal"], 2), 1, 1, 'R');
        }

        // -------- TOTALES --------
        $pdf->Ln(3);
        $pdf->Cell(120, 6, '', 0, 0); // espacio
        $pdf->Cell(35, 6, 'Subtotal:', 0, 0, 'R');
        $pdf->Cell(35, 6, 'S/ ' . number_format($venta["subtotal"], 2), 0, 1, 'R');

        $pdf->Cell(120, 6, '', 0, 0);
        $pdf->Cell(35, 6, 'IGV:', 0, 0, 'R');
        $pdf->Cell(35, 6, 'S/ ' . number_format($venta["igv"], 2), 0, 1, 'R');

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(120, 7, '', 0, 0);
        $pdf->Cell(35, 7, 'Total:', 0, 0, 'R');
        $pdf->Cell(35, 7, 'S/ ' . number_format($venta["total"], 2), 0, 1, 'R');

        // -------- SALIDA --------
        $nombreArchivo = 'COMPROBANTE_' .
            $venta["serie_comprobante"] . '-' .
            $venta["numero_comprobante"] . '.pdf';

        // Mostrar en el navegador
        $pdf->Output('I', $nombreArchivo);
    }

    /* ================= ENVIAR COMPROBANTE POR CORREO ================= */

    public function enviarCorreo($id_venta)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1) Traer venta + cliente + detalle
        $data = $this->modelo->obtenerVentaCompleta($id_venta);
        if ($data === false) {
            $this->listar("No se encontró la venta indicada para enviar por correo.");
            return;
        }

        $venta    = $data["venta"];
        $detalles = $data["detalles"];

        // Correo del cliente (debe venir como alias 'correo_cliente' desde el modelo)
        $correoDestino = trim($venta["correo_cliente"] ?? '');
        if ($correoDestino === '' || !filter_var($correoDestino, FILTER_VALIDATE_EMAIL)) {
            $this->listar("El cliente de esta venta no tiene un correo válido registrado.");
            return;
        }

        // ===== AUDITORÍA: ENVÍO DE CORREO =====
        if (isset($_SESSION['id_usuario'])) {
            $this->auditoriaModelo->registrarEvento(
                (int)$_SESSION['id_usuario'],
                'VENTAS',
                'ENVIAR_CORREO',
                'Envío de comprobante de venta ID ' . (int)$id_venta . ' al correo ' . $correoDestino,
                'tb_ventas',
                (int)$id_venta
            );
        }

        // 2) Generar PDF en memoria
        require_once __DIR__ . "/../libs/fpdf/fpdf.php";

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // -------- ENCABEZADO --------
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(190, 8, 'Bazar Carolian', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(190, 5, utf8_decode('Sistema de inventario y ventas'), 0, 1, 'C');
        $pdf->Ln(5);

        $textoComprobante = trim(
            $venta["nombre_tipo"] . " " .
            $venta["serie_comprobante"] . "-" .
            $venta["numero_comprobante"]
        );

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(190, 6, utf8_decode($textoComprobante), 0, 1, 'C');
        $pdf->Ln(4);

        // Datos principales
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(95, 5, 'Fecha: ' . $venta["fecha_venta"], 0, 0, 'L');
        $pdf->Cell(95, 5, 'Tipo de pago: ' . $venta["tipo_pago"], 0, 1, 'R');

        // Cliente
        $nombreCliente = "—";
        if (!empty($venta["razon_social"])) {
            $nombreCliente = $venta["razon_social"];
        } elseif (!empty($venta["nombres"]) || !empty($venta["apellidos"])) {
            $nombreCliente = trim(($venta["nombres"] ?? "") . " " . ($venta["apellidos"] ?? ""));
        }

        $documentoCliente = "";
        if (!empty($venta["numero_documento"])) {
            $documentoCliente = ($venta["tipo_documento"] ?? "") . " " . $venta["numero_documento"];
        }

        $pdf->Cell(190, 5, 'Cliente: ' . utf8_decode($nombreCliente), 0, 1, 'L');
        if ($documentoCliente !== "") {
            $pdf->Cell(190, 5, 'Documento: ' . $documentoCliente, 0, 1, 'L');
        }

        if ($venta["tipo_pago"] === "EFECTIVO") {
            $montoRecibido = $venta["monto_recibido"] ?? null;
            $vuelto        = $venta["vuelto"] ?? null;

            $textoRecibido = ($montoRecibido !== null)
                ? 'Monto recibido: S/ ' . number_format($montoRecibido, 2)
                : 'Monto recibido: S/ -';

            $textoVuelto = ($vuelto !== null)
                ? 'Vuelto: S/ ' . number_format($vuelto, 2)
                : 'Vuelto: S/ -';

            $pdf->Cell(95, 5, utf8_decode($textoRecibido), 0, 0, 'L');
            $pdf->Cell(95, 5, utf8_decode($textoVuelto), 0, 1, 'R');
        }

        $pdf->Ln(4);

        // Tabla productos
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 7, '#', 1, 0, 'C');
        $pdf->Cell(90, 7, 'Producto', 1, 0, 'L');
        $pdf->Cell(20, 7, 'Cant.', 1, 0, 'R');
        $pdf->Cell(35, 7, 'P. Unitario', 1, 0, 'R');
        $pdf->Cell(35, 7, 'Subtotal', 1, 1, 'R');

        $pdf->SetFont('Arial', '', 10);
        $i = 1;
        foreach ($detalles as $d) {
            $pdf->Cell(10, 6, $i++, 1, 0, 'C');
            $pdf->Cell(90, 6, utf8_decode($d["nombre_producto"]), 1, 0, 'L');
            $pdf->Cell(20, 6, (int)$d["cantidad"], 1, 0, 'R');
            $pdf->Cell(35, 6, 'S/ ' . number_format($d["precio_venta"], 2), 1, 0, 'R');
            $pdf->Cell(35, 6, 'S/ ' . number_format($d["subtotal"], 2), 1, 1, 'R');
        }

        // Totales
        $pdf->Ln(3);
        $pdf->Cell(120, 6, '', 0, 0);
        $pdf->Cell(35, 6, 'Subtotal:', 0, 0, 'R');
        $pdf->Cell(35, 6, 'S/ ' . number_format($venta["subtotal"], 2), 0, 1, 'R');

        $pdf->Cell(120, 6, '', 0, 0);
        $pdf->Cell(35, 6, 'IGV:', 0, 0, 'R');
        $pdf->Cell(35, 6, 'S/ ' . number_format($venta["igv"], 2), 0, 1, 'R');

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(120, 7, '', 0, 0);
        $pdf->Cell(35, 7, 'Total:', 0, 0, 'R');
        $pdf->Cell(35, 7, 'S/ ' . number_format($venta["total"], 2), 0, 1, 'R');

        // Nombre archivo
        $nombreArchivo = 'COMPROBANTE_' .
            $venta["serie_comprobante"] . '-' .
            $venta["numero_comprobante"] . '.pdf';

        // PDF EN MEMORIA (cadena)
        $pdfData = $pdf->Output('S', $nombreArchivo);

        // 3) Enviar correo con PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($correoDestino, $nombreCliente);

            $mail->Subject = 'Comprobante de pago - ' . $textoComprobante;

            $cuerpo = "Estimado(a) " . $nombreCliente . ",\n\n"
                . "Adjuntamos el comprobante de su compra realizada en Bazar Carolian.\n"
                . "Comprobante: " . $textoComprobante . "\n"
                . "Total: S/ " . number_format($venta['total'], 2) . "\n\n"
                . "Gracias por su preferencia.\n\n"
                . "Bazar Carolian";

            $mail->Body    = nl2br($cuerpo);
            $mail->AltBody = $cuerpo;
            $mail->isHTML(true);

            // Adjuntar PDF desde memoria
            $mail->addStringAttachment($pdfData, $nombreArchivo, 'base64', 'application/pdf');

            $mail->send();

            // Marcar en BD si el método existe
            if (method_exists($this->modelo, 'marcarCorreoEnviado')) {
                $this->modelo->marcarCorreoEnviado($id_venta, 1);
            }

            $this->listar("Comprobante enviado correctamente al correo del cliente.");
        } catch (Exception $e) {
            $this->listar("No se pudo enviar el correo: " . $mail->ErrorInfo);
        }
    }
}
