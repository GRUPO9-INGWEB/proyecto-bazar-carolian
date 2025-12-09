<?php
// vistas/ventas/ver_venta.php

// $venta    => cabecera de la venta (tb_ventas + join con cliente, tipo comprobante, etc.)
// $detalles => detalle de productos

// ID de la venta (para enlaces de PDF y correo)
$idVenta = isset($venta["id_venta"]) ? (int)$venta["id_venta"] : 0;

/* =======================
   Nombre y documento del cliente
   ======================= */
$nombreCliente = "—";
if (!empty($venta["razon_social"])) {
    $nombreCliente = $venta["razon_social"];
} elseif (!empty($venta["nombres"]) || !empty($venta["apellidos"])) {
    $nombreCliente = trim(($venta["nombres"] ?? "") . " " . ($venta["apellidos"] ?? ""));
}

$documentoCliente = "";
if (!empty($venta["numero_documento"])) {
    $documentoCliente = trim(($venta["tipo_documento"] ?? "") . " " . $venta["numero_documento"]);
}

// Correo del cliente (viene como alias correo_cliente desde el modelo)
$correoCliente = trim($venta["correo_cliente"] ?? "");

/* =======================
   Texto del comprobante
   ======================= */
$textoComprobante = trim(
    ($venta["nombre_tipo"] ?? "") . " " .
    ($venta["serie_comprobante"] ?? "") . "-" .
    ($venta["numero_comprobante"] ?? "")
);

/* =======================
   Monto recibido y vuelto
   ======================= */
$montoRecibido = isset($venta["monto_recibido"]) ? (float)$venta["monto_recibido"] : null;
$vuelto        = isset($venta["vuelto"]) ? (float)$venta["vuelto"] : null;
?>
<h3>Detalle de venta</h3>
<p class="text-muted">
    Información detallada de la venta y sus productos.
</p>

<!-- Botones arriba: volver, imprimir PDF, enviar correo -->
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="panel_admin.php?modulo=ventas&accion=listar"
       class="btn btn-outline-secondary btn-sm">
        Volver a listado
    </a>

    <?php if ($idVenta > 0): ?>
        <div class="btn-group">
            <a href="panel_admin.php?modulo=ventas&accion=imprimir_pdf&id=<?php echo $idVenta; ?>"
               target="_blank"
               class="btn btn-sm btn-outline-primary">
                Imprimir PDF
            </a>

            <?php if ($correoCliente !== ""): ?>
                <a href="panel_admin.php?modulo=ventas&accion=enviar_correo&id=<?php echo $idVenta; ?>"
                   class="btn btn-sm btn-outline-success">
                    Enviar por correo
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- =======================
     DATOS DEL COMPROBANTE
     ======================= -->
<div class="card mb-4">
    <div class="card-header">
        Datos del comprobante
    </div>
    <div class="card-body row g-3">

        <div class="col-md-4">
            <strong>Comprobante:</strong><br>
            <?php echo htmlspecialchars($textoComprobante); ?>
        </div>

        <div class="col-md-4">
            <strong>Fecha:</strong><br>
            <?php echo htmlspecialchars($venta["fecha_venta"]); ?>
        </div>

        <div class="col-md-4">
            <strong>Tipo de pago:</strong><br>
            <?php echo htmlspecialchars($venta["tipo_pago"]); ?>
        </div>

        <div class="col-md-6 mt-3">
            <strong>Cliente:</strong><br>
            <?php echo htmlspecialchars($nombreCliente); ?>
            <?php if ($documentoCliente !== ""): ?>
                <br>
                <small class="text-muted">
                    <?php echo htmlspecialchars($documentoCliente); ?>
                </small>
            <?php endif; ?>

            <?php if ($correoCliente !== ""): ?>
                <br>
                <small class="text-muted">
                    Correo: <?php echo htmlspecialchars($correoCliente); ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="col-md-3 mt-3">
            <strong>Estado:</strong><br>
            <span class="badge bg-success">
                <?php echo htmlspecialchars($venta["estado"]); ?>
            </span>
        </div>

        <div class="col-md-3 mt-3">
            <strong>Correo enviado:</strong><br>
            <?php if (!empty($venta["correo_enviado"])): ?>
                <span class="badge bg-info text-dark">Sí</span>
            <?php else: ?>
                <span class="badge bg-secondary">No</span>
            <?php endif; ?>
        </div>

        <?php if (
            strtoupper($venta["tipo_pago"] ?? "") === "EFECTIVO"
            && $montoRecibido !== null
        ): ?>
            <div class="col-md-3 mt-3">
                <strong>Monto recibido:</strong><br>
                S/ <?php echo number_format($montoRecibido, 2); ?>
            </div>

            <div class="col-md-3 mt-3">
                <strong>Vuelto:</strong><br>
                S/ <?php echo number_format($vuelto ?? 0, 2); ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- =======================
     PRODUCTOS DE LA VENTA
     ======================= -->
<div class="card mb-4">
    <div class="card-header">
        Productos de la venta
    </div>
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">Precio unitario</th>
                    <th class="text-end">Descuento</th>
                    <th class="text-end">Subtotal</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($detalles)): ?>
                    <?php $i = 1; foreach ($detalles as $d): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($d["nombre_producto"]); ?></td>
                            <td class="text-end"><?php echo (int)$d["cantidad"]; ?></td>
                            <td class="text-end">
                                S/ <?php echo number_format($d["precio_venta"], 2); ?>
                            </td>
                            <td class="text-end">
                                S/ <?php echo number_format($d["descuento"] ?? 0, 2); ?>
                            </td>
                            <td class="text-end">
                                S/ <?php echo number_format($d["subtotal"], 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            No hay detalle registrado para esta venta.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end mt-3">
            <div class="col-md-4">
                <div class="d-flex justify-content-between">
                    <span>Subtotal:</span>
                    <strong>
                        S/ <?php echo number_format($venta["subtotal"], 2); ?>
                    </strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>IGV:</span>
                    <strong>
                        S/ <?php echo number_format($venta["igv"], 2); ?>
                    </strong>
                </div>
                <div class="d-flex justify-content-between fs-5">
                    <span>Total:</span>
                    <strong>
                        S/ <?php echo number_format($venta["total"], 2); ?>
                    </strong>
                </div>
            </div>
        </div>

    </div>
</div>
