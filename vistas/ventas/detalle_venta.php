<?php
// vistas/ventas/detalle_venta.php
?>
<h3>Detalle de venta #<?php echo $venta["id_venta"]; ?></h3>

<p>
    <strong>Fecha:</strong>
    <?php echo date("d/m/Y H:i", strtotime($venta["fecha_venta"])); ?><br>
    <strong>Comprobante:</strong>
    <?php echo htmlspecialchars($venta["tipo_comprobante"]); ?>
    <?php echo " " . htmlspecialchars($venta["serie_comprobante"] . "-" . $venta["numero_comprobante"]); ?><br>
    <strong>Estado:</strong>
    <?php echo htmlspecialchars($venta["estado"]); ?><br>
    <strong>Tipo de pago:</strong>
    <?php echo htmlspecialchars($venta["tipo_pago"]); ?>
</p>

<h5>Cliente</h5>
<p>
    <?php
    if (!empty($venta["razon_social"])) {
        echo htmlspecialchars($venta["razon_social"]);
    } else {
        echo htmlspecialchars(trim(($venta["nombres"] ?? "") . " " . ($venta["apellidos"] ?? "")));
    }
    if (!empty($venta["doc_cliente"])) {
        echo " (" . htmlspecialchars($venta["doc_cliente"]) . ")";
    }
    ?><br>
    <?php echo htmlspecialchars($venta["direccion"] ?? ""); ?>
</p>

<h5>Detalle</h5>
<div class="table-responsive">
    <table class="table table-sm table-striped">
        <thead class="table-light">
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
            <tr><td colspan="5" class="text-center">No hay detalle registrado.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<a href="panel_admin.php?modulo=ventas" class="btn btn-secondary btn-sm">Volver</a>
