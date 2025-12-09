<?php
// vistas/ventas/listado_ventas.php

if (!isset($mensaje)) {
    $mensaje = "";
}

$texto_busqueda     = $texto_busqueda ?? "";
$orden_seleccionado = $orden_seleccionado ?? "recientes";
$tipo_filtro_actual = $tipo_filtro_actual ?? "TODOS";
?>
<h3>Ventas</h3>
<p class="text-muted">
    En este módulo puede consultar las ventas realizadas, filtrar por cliente,
    comprobante o documento y ordenar por fecha o monto.
</p>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<form method="get" class="row g-2 mb-3">
    <input type="hidden" name="modulo" value="ventas">
    <input type="hidden" name="accion" value="listar">

    <div class="col-md-4">
        <input type="text"
               name="buscar"
               class="form-control form-control-sm"
               placeholder="Buscar por cliente, documento o comprobante..."
               value="<?php echo htmlspecialchars($texto_busqueda); ?>">
    </div>

    <div class="col-md-3">
        <select name="tipo_filtro" class="form-select form-select-sm">
            <option value="TODOS"  <?php echo ($tipo_filtro_actual === "TODOS")  ? "selected" : ""; ?>>Todos los comprobantes</option>
            <option value="TICKET" <?php echo ($tipo_filtro_actual === "TICKET") ? "selected" : ""; ?>>Solo Tickets</option>
            <option value="BOLETA" <?php echo ($tipo_filtro_actual === "BOLETA") ? "selected" : ""; ?>>Solo Boletas</option>
            <option value="FACTURA"<?php echo ($tipo_filtro_actual === "FACTURA")? "selected" : ""; ?>>Solo Facturas</option>
        </select>
    </div>

    <div class="col-md-3">
        <select name="orden" class="form-select form-select-sm">
            <option value="recientes"   <?php echo ($orden_seleccionado === "recientes")   ? "selected" : ""; ?>>Más recientes primero</option>
            <option value="antiguos"    <?php echo ($orden_seleccionado === "antiguos")    ? "selected" : ""; ?>>Más antiguos primero</option>
            <option value="monto_mayor" <?php echo ($orden_seleccionado === "monto_mayor") ? "selected" : ""; ?>>Monto mayor primero</option>
            <option value="monto_menor" <?php echo ($orden_seleccionado === "monto_menor") ? "selected" : ""; ?>>Monto menor primero</option>
        </select>
    </div>

    <div class="col-md-2 d-flex gap-1">
        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
            Buscar
        </button>
        <a href="panel_admin.php?modulo=ventas&accion=listar"
           class="btn btn-outline-secondary btn-sm flex-grow-1">
            Limpiar
        </a>
    </div>
</form>

<div class="mb-3">
    <a href="panel_admin.php?modulo=ventas&accion=nueva" class="btn btn-success btn-sm">
        Nueva venta
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm align-middle">
        <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Comprobante</th>
            <th>Cliente</th>
            <th>Subtotal</th>
            <th>IGV</th>
            <th>Total</th>
            <th>Pago</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($lista_ventas)): ?>
            <?php
            $contador = 1;
            foreach ($lista_ventas as $v):
                $nombreCliente = "—";
                if (!empty($v["razon_social"])) {
                    $nombreCliente = $v["razon_social"];
                } elseif (!empty($v["nombres"]) || !empty($v["apellidos"])) {
                    $nombreCliente = trim(($v["nombres"] ?? "") . " " . ($v["apellidos"] ?? ""));
                }

                $textoComprobante = trim(
                    $v["nombre_tipo"] . " " .
                    $v["serie_comprobante"] . "-" .
                    $v["numero_comprobante"]
                );
            ?>
                <tr>
                    <td><?php echo $contador++; ?></td>
                    <td><?php echo htmlspecialchars($v["fecha_venta"]); ?></td>
                    <td><?php echo htmlspecialchars($textoComprobante); ?></td>
                    <td><?php echo htmlspecialchars($nombreCliente); ?></td>
                    <td>S/ <?php echo number_format($v["subtotal"], 2); ?></td>
                    <td>S/ <?php echo number_format($v["igv"], 2); ?></td>
                    <td>S/ <?php echo number_format($v["total"], 2); ?></td>
                    <td><?php echo htmlspecialchars($v["tipo_pago"]); ?></td>
                    <td>
                        <span class="badge bg-success">
                            <?php echo htmlspecialchars($v["estado"]); ?>
                        </span>
                    </td>
                    <td>
                        <!-- Aquí luego podemos poner ver PDF, reenviar correo, etc. -->
                        <a href="panel_admin.php?modulo=ventas&accion=ver&id=<?php echo $v["id_venta"]; ?>"
                           class="btn btn-sm btn-outline-secondary">
                            Ver
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="10" class="text-center">
                    No hay ventas registradas.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
