<?php
// vistas/ventas/detalle_venta.php
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi bi-file-earmark-text me-2"></i>
            Detalle de venta #<?php echo $venta["id_venta"]; ?>
        </h3>
        <p class="text-muted small mb-0">
            Resumen del comprobante, el cliente y los productos vendidos.
        </p>
    </div>

    <a href="panel_admin.php?modulo=ventas"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<!-- Datos del comprobante -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-receipt-cutoff"></i>
        <span>Datos del comprobante</span>
    </div>
    <div class="card-body row g-3">

        <div class="col-md-4">
            <div class="text-muted extra-small mb-1">Fecha</div>
            <div>
                <i class="bi bi-calendar-date me-1 text-muted"></i>
                <?php echo date("d/m/Y H:i", strtotime($venta["fecha_venta"])); ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="text-muted extra-small mb-1">Comprobante</div>
            <div class="fw-semibold">
                <?php echo htmlspecialchars($venta["tipo_comprobante"]); ?>
                <?php echo " " . htmlspecialchars($venta["serie_comprobante"] . "-" . $venta["numero_comprobante"]); ?>
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
            <div>
                <?php
                if (!empty($venta["razon_social"])) {
                    echo htmlspecialchars($venta["razon_social"]);
                } else {
                    echo htmlspecialchars(
                        trim(($venta["nombres"] ?? "") . " " . ($venta["apellidos"] ?? ""))
                    );
                }
                if (!empty($venta["doc_cliente"])) {
                    echo " (" . htmlspecialchars($venta["doc_cliente"]) . ")";
                }
                ?>
            </div>
            <?php if (!empty($venta["direccion"])): ?>
                <small class="text-muted d-block">
                    <?php echo htmlspecialchars($venta["direccion"]); ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="col-md-3 mt-3">
            <div class="text-muted extra-small mb-1">Estado</div>
            <span class="badge bg-success">
                <?php echo htmlspecialchars($venta["estado"]); ?>
            </span>
        </div>
    </div>
</div>

<!-- Detalle de productos -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-box-seam"></i>
        <span>Detalle de productos</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle table-modern">
                <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">P. venta</th>
                    <th class="text-end">Descuento</th>
                    <th class="text-end">Subtotal</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($detalles)): ?>
                    <?php foreach ($detalles as $d): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($d["nombre_producto"]); ?></td>
                            <td class="text-end"><?php echo (int)$d["cantidad"]; ?></td>
                            <td class="text-end">S/ <?php echo number_format($d["precio_venta"], 2); ?></td>
                            <td class="text-end">S/ <?php echo number_format($d["descuento"], 2); ?></td>
                            <td class="text-end">S/ <?php echo number_format($d["subtotal"], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-3">No hay detalle registrado.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
