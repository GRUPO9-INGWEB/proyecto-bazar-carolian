<?php
// Incluimos las clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Incluimos el autoloader de Composer.
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Función para enviar el comprobante por email
 * @param array $venta Datos de la venta (obtenidos del modelo)
 * @param string $ruta_pdf La ruta en el servidor donde se guardó el PDF
 */
function enviarComprobantePorEmail($venta, $ruta_pdf) {
    
    // Verificamos si el cliente tiene un email. Si no, no hacemos nada.
    if (empty($venta['email_cliente'])) {
        return false; 
    }

    $mail = new PHPMailer(true); // True habilita excepciones

    try {
        // --- 1. Configuración del Servidor (SMTP de Gmail) ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        // ¡Asegúrate que estos datos sean los tuyos!
        $mail->Username   = 'drudasma@ucvvirtual.edu.pe'; // Tu email de Gmail
        $mail->Password   = 'rocjbwvhnwesvxdf'; // Tu App Password de 16 letras (sin espacios)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // --- 2. Emisor y Receptor ---
        // ¡CORRECCIÓN 1! Debe ser tu email también
        $mail->setFrom('drudasma@gmail.com', 'Bazar Carolian'); 
        $mail->addAddress($venta['email_cliente'], $venta['nombre_cliente']); // Se envía al cliente

        // --- 3. Contenido del Correo ---
        $mail->isHTML(true); 
        $mail->CharSet = 'UTF-8'; 
        
        // ¡CORRECCIÓN 2! Era un '_' en lugar de '.'
        $mail->Subject = utf8_decode('Comprobante de Venta N°: ' . $venta['id_venta']);
        
        // Cuerpo del email
        $mail->Body    = 'Hola ' . $venta['nombre_cliente'] . ',<br><br>' .
                         'Gracias por tu compra en Bazar Carolian.<br>' .
                         'Adjuntamos tu ' . $venta['tipo_comprobante'] . ' en formato PDF.<br><br>' .
                         'Total Pagado: S/ ' . number_format($venta['total'], 2);
        
        $mail->AltBody = 'Gracias por tu compra. Adjuntamos tu comprobante PDF.'; 

        // --- 4. Adjuntar el PDF ---
        $mail->addAttachment($ruta_pdf);

        // --- 5. Enviar ---
        $mail->send();
        return true; // Éxito

    } catch (Exception $e) {
        // Opcional: Registrar el error $mail->ErrorInfo
        return false; // Error
    }
}
?>