<?php
// vistas/compras/ver_compra.php

$idCompra = isset($compra["id_compra"]) ? (int)$compra["id_compra"] : 0;

$textoComprobante = trim(
    ($compra["nombre_tipo"] ?? "") . " " .
    ($compra["serie_comprobante"] ?? "") . "-" .
    ($compra["numero_comprobante"] ?? "")
);
?>
<h3>Detalle de compra</h3>
<p class="text-muted">
    Información detallada de la compra y sus productos.
</p>

<div class="mb-3">
    <a href="panel_admin.php?modulo=compras&accion=listar"
       class="btn btn-outline-secondary btn-sm">
        Volver a listado
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">Datos del comprobante</div>
    <div class="card-body row g-3">

        <div class="col-md-4">
            <strong>Comprobante:</strong><br>
            <?php echo htmlspecialchars($textoComprobante); ?>
        </div>

        <div class="col-md-4">
            <strong>Fecha:</strong><br>
            <?php echo htmlspecialchars($compra["fecha_compra"]); ?>
        </div>

        <div class="col-md-4">
            <strong>Estado:</strong><br>
            <span class="badge bg-success">
                <?php echo htmlspecialchars($compra["estado"]); ?>
            </span>
        </div>

        <div class="col-md-6 mt-3">
            <strong>Proveedor:</strong><br>
            <?php echo htmlspecialchars($compra["razon_social"]); ?><br>
            <small class="text-muted">
                <?php echo htmlspecialchars(($compra["tipo_documento"] ?? '') . " " . ($compra["numero_documento"] ?? '')); ?>
            </small>
        </div>

        <div class="col-md-6 mt-3">
            <strong>Contacto:</strong><br>
            <?php echo htmlspecialchars($compra["nombre_contacto"] ?? ""); ?>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Productos de la compra</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
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
                        <td colspan="5" class="text-center">
                            No hay detalle registrado para esta compra.
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
                    <strong>S/ <?php echo number_format($compra["subtotal"], 2); ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>IGV:</span>
                    <strong>S/ <?php echo number_format($compra["igv"], 2); ?></strong>
                </div>
                <div class="d-flex justify-content-between fs-5">
                    <span>Total:</span>
                    <strong>S/ <?php echo number_format($compra["total"], 2); ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>
