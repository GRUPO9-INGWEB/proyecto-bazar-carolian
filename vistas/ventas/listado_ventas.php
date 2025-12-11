<?php
// vistas/ventas/listado_ventas.php

if (!isset($mensaje)) {
    $mensaje = "";
}

$texto_busqueda     = $texto_busqueda     ?? "";
$orden_seleccionado = $orden_seleccionado ?? "recientes";
$tipo_filtro_actual = $tipo_filtro_actual ?? "TODOS";
if (!isset($lista_ventas)) {
    $lista_ventas = [];
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi bi-cash-stack me-2"></i>
            Ventas
        </h3>
        <p class="text-muted small mb-0">
            Consulte las ventas realizadas, filtre por cliente o tipo de comprobante
            y revise los montos registrados.
        </p>
    </div>

    <div class="mt-2 mt-md-0">
        <a href="panel_admin.php?modulo=ventas&accion=nueva"
           class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Nueva venta
        </a>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info mb-3">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Filtros -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end">
            <input type="hidden" name="modulo" value="ventas">
            <input type="hidden" name="accion" value="listar">

            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Buscar</label>
                <input type="text"
                       name="buscar"
                       class="form-control form-control-sm"
                       placeholder="Cliente, documento o comprobante..."
                       value="<?php echo htmlspecialchars($texto_busqueda); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Tipo de comprobante</label>
                <select name="tipo_filtro" class="form-select form-select-sm">
                    <option value="TODOS"  <?php echo ($tipo_filtro_actual === "TODOS")  ? "selected" : ""; ?>>Todos los comprobantes</option>
                    <option value="TICKET" <?php echo ($tipo_filtro_actual === "TICKET") ? "selected" : ""; ?>>Solo Tickets</option>
                    <option value="BOLETA" <?php echo ($tipo_filtro_actual === "BOLETA") ? "selected" : ""; ?>>Solo Boletas</option>
                    <option value="FACTURA"<?php echo ($tipo_filtro_actual === "FACTURA")? "selected" : ""; ?>>Solo Facturas</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Ordenar por</label>
                <select name="orden" class="form-select form-select-sm">
                    <option value="recientes"   <?php echo ($orden_seleccionado === "recientes")   ? "selected" : ""; ?>>Más recientes primero</option>
                    <option value="antiguos"    <?php echo ($orden_seleccionado === "antiguos")    ? "selected" : ""; ?>>Más antiguos primero</option>
                    <option value="monto_mayor" <?php echo ($orden_seleccionado === "monto_mayor") ? "selected" : ""; ?>>Monto mayor primero</option>
                    <option value="monto_menor" <?php echo ($orden_seleccionado === "monto_menor") ? "selected" : ""; ?>>Monto menor primero</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-outline-primary btn-sm flex-grow-1">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
                <a href="panel_admin.php?modulo=ventas&accion=listar"
                   class="btn btn-outline-secondary btn-sm flex-grow-1">
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla -->
<div class="card card-table">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listado de ventas</h5>
        <span class="text-muted extra-small">
            <?php echo !empty($lista_ventas) ? count($lista_ventas) . " registro(s)" : "Sin registros"; ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-sm align-middle mb-0 table-modern">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Comprobante</th>
                    <th>Cliente</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-end">IGV</th>
                    <th class="text-end">Total</th>
                    <th>Pago</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
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
                            <td>
                                <i class="bi bi-calendar-date me-1 text-muted"></i>
                                <?php echo htmlspecialchars($v["fecha_venta"]); ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-receipt-cutoff me-1"></i>
                                    <?php echo htmlspecialchars($textoComprobante); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($nombreCliente); ?></td>
                            <td class="text-end">S/ <?php echo number_format($v["subtotal"], 2); ?></td>
                            <td class="text-end">S/ <?php echo number_format($v["igv"], 2); ?></td>
                            <td class="text-end fw-semibold">S/ <?php echo number_format($v["total"], 2); ?></td>
                            <td><?php echo htmlspecialchars($v["tipo_pago"]); ?></td>
                            <td>
                                <span class="badge bg-success">
                                    <?php echo htmlspecialchars($v["estado"]); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="panel_admin.php?modulo=ventas&accion=ver&id=<?php echo $v["id_venta"]; ?>"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye me-1"></i> Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            No hay ventas registradas.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
