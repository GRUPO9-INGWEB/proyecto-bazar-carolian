<?php
// vistas/ventas/ver_venta.php

$idVenta = isset($venta["id_venta"]) ? (int)$venta["id_venta"] : 0;

/* Nombre y documento del cliente */
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

$correoCliente = trim($venta["correo_cliente"] ?? "");

/* Texto del comprobante */
$textoComprobante = trim(
    ($venta["nombre_tipo"] ?? "") . " " .
    ($venta["serie_comprobante"] ?? "") . "-" .
    ($venta["numero_comprobante"] ?? "")
);

/* Monto recibido y vuelto */
$montoRecibido = isset($venta["monto_recibido"]) ? (float)$venta["monto_recibido"] : null;
$vuelto        = isset($venta["vuelto"]) ? (float)$venta["vuelto"] : null;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi bi-file-earmark-text me-2"></i>
            Detalle de venta
        </h3>
        <p class="text-muted small mb-0">
            Información detallada de la venta, el cliente y los productos.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="panel_admin.php?modulo=ventas&accion=listar"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver a listado
        </a>

        <?php if ($idVenta > 0): ?>
            <div class="btn-group">
                <a href="panel_admin.php?modulo=ventas&accion=imprimir_pdf&id=<?php echo $idVenta; ?>"
                   target="_blank"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-filetype-pdf me-1"></i> Imprimir PDF
                </a>

                <?php if ($correoCliente !== ""): ?>
                    <a href="panel_admin.php?modulo=ventas&accion=enviar_correo&id=<?php echo $idVenta; ?>"
                       class="btn btn-sm btn-outline-success">
                        <i class="bi bi-envelope me-1"></i> Enviar por correo
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Datos del comprobante -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-receipt-cutoff"></i>
        <span>Datos del comprobante</span>
    </div>
    <div class="card-body row g-3">

        <div class="col-md-4">
            <div class="text-muted extra-small mb-1">Comprobante</div>
            <div class="fw-semibold">
                <?php echo htmlspecialchars($textoComprobante); ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="text-muted extra-small mb-1">Fecha</div>
            <div>
                <i class="bi bi-calendar-date me-1 text-muted"></i>
                <?php echo htmlspecialchars($venta["fecha_venta"]); ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="text-muted extra-small mb-1">Tipo de pago</div>
            <div>
                <?php echo htmlspecialchars($venta["tipo_pago"]); ?>
            </div>
        </div>

        <div class="col-md-6 mt-3">
            <div class="text-muted extra-small mb-1">Cliente</div>
            <div class="fw-semibold">
                <?php echo htmlspecialchars($nombreCliente); ?>
            </div>
            <?php if ($documentoCliente !== ""): ?>
                <small class="text-muted d-block">
                    <?php echo htmlspecialchars($documentoCliente); ?>
                </small>
            <?php endif; ?>

            <?php if ($correoCliente !== ""): ?>
                <small class="text-muted d-block">
                    Correo: <?php echo htmlspecialchars($correoCliente); ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="col-md-3 mt-3">
            <div class="text-muted extra-small mb-1">Estado</div>
            <span class="badge bg-success">
                <?php echo htmlspecialchars($venta["estado"]); ?>
            </span>
        </div>

        <div class="col-md-3 mt-3">
            <div class="text-muted extra-small mb-1">Correo enviado</div>
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
                <div class="text-muted extra-small mb-1">Monto recibido</div>
                <div>S/ <?php echo number_format($montoRecibido, 2); ?></div>
            </div>

            <div class="col-md-3 mt-3">
                <div class="text-muted extra-small mb-1">Vuelto</div>
                <div>S/ <?php echo number_format($vuelto ?? 0, 2); ?></div>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Productos de la venta -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-box-seam"></i>
        <span>Productos de la venta</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm align-middle table-modern">
                <thead>
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
                        <td colspan="6" class="text-center py-4">
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
                    <span class="text-muted">Subtotal:</span>
                    <strong>S/ <?php echo number_format($venta["subtotal"], 2); ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">IGV:</span>
                    <strong>S/ <?php echo number_format($venta["igv"], 2); ?></strong>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fs-5">
                    <span>Total:</span>
                    <strong>S/ <?php echo number_format($venta["total"], 2); ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>
