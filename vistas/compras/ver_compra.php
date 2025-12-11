<?php
// vistas/compras/ver_compra.php

$idCompra = isset($compra["id_compra"]) ? (int)$compra["id_compra"] : 0;

$textoComprobante = trim(
    ($compra["nombre_tipo"] ?? "") . " " .
    ($compra["serie_comprobante"] ?? "") . "-" .
    ($compra["numero_comprobante"] ?? "")
);

if (!isset($detalles)) {
    $detalles = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi bi-file-earmark-text me-2"></i>
            Detalle de compra
        </h3>
        <p class="text-muted small mb-0">
            Información detallada del comprobante y los productos asociados.
        </p>
    </div>

    <a href="panel_admin.php?modulo=compras&accion=listar"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
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
            <div class="text-muted extra-small mb-1">Comprobante</div>
            <div class="fw-semibold">
                <?php echo htmlspecialchars($textoComprobante); ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="text-muted extra-small mb-1">Fecha</div>
            <div>
                <i class="bi bi-calendar-date me-1 text-muted"></i>
                <?php echo htmlspecialchars($compra["fecha_compra"]); ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="text-muted extra-small mb-1">Estado</div>
            <?php
            $estadoTexto = htmlspecialchars($compra["estado"]);
            $esActivo = (mb_strtoupper(trim($estadoTexto)) === "REGISTRADA"
                || mb_strtoupper(trim($estadoTexto)) === "ACTIVA");
            ?>
            <span class="badge <?php echo $esActivo ? 'bg-success' : 'bg-secondary'; ?>">
                <?php echo $estadoTexto; ?>
            </span>
        </div>

        <div class="col-md-6 mt-3">
            <div class="text-muted extra-small mb-1">Proveedor</div>
            <div class="fw-semibold">
                <?php echo htmlspecialchars($compra["razon_social"]); ?>
            </div>
            <small class="text-muted">
                <?php echo htmlspecialchars(($compra["tipo_documento"] ?? '') . " " . ($compra["numero_documento"] ?? '')); ?>
            </small>
        </div>

        <div class="col-md-6 mt-3">
            <div class="text-muted extra-small mb-1">Contacto</div>
            <div>
                <?php echo htmlspecialchars($compra["nombre_contacto"] ?? ""); ?>
            </div>
        </div>
    </div>
</div>

<!-- Productos de la compra -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-box-seam"></i>
        <span>Productos de la compra</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm align-middle table-modern">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">Precio compra</th>
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
                                S/ <?php echo number_format($d["precio_compra"], 2); ?>
                            </td>
                            <td class="text-end">
                                S/ <?php echo number_format($d["subtotal"], 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            No hay detalle registrado para esta compra.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="row justify-content-end mt-3">
            <div class="col-md-4">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Subtotal:</span>
                    <strong>S/ <?php echo number_format($compra["subtotal"], 2); ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">IGV:</span>
                    <strong>S/ <?php echo number_format($compra["igv"], 2); ?></strong>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fs-5">
                    <span>Total:</span>
                    <strong>S/ <?php echo number_format($compra["total"], 2); ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>
