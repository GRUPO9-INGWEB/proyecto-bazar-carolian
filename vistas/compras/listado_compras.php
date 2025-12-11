<?php
// vistas/compras/listado_compras.php

$texto       = $_GET["buscar"]      ?? "";
$orden       = $_GET["orden"]       ?? "recientes";
$tipo_filtro = $_GET["tipo_filtro"] ?? "TODOS";

if (!isset($mensaje)) {
    $mensaje = "";
}
if (!isset($lista_compras)) {
    $lista_compras = [];
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi bi-basket3 me-2"></i>
            Compras (ingreso de mercadería)
        </h3>
        <p class="text-muted small mb-0">
            Consulte las compras realizadas, filtre por proveedor o tipo de comprobante
            y revise los montos registrados.
        </p>
    </div>

    <div class="mt-2 mt-md-0">
        <a href="panel_admin.php?modulo=compras&accion=nueva"
           class="btn btn-primary btn-sm">
            <i class="bi bi-bag-plus me-1"></i> Nueva compra
        </a>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info mb-3">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Filtros de búsqueda -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="get" action="panel_admin.php">
            <input type="hidden" name="modulo" value="compras">
            <input type="hidden" name="accion" value="listar">

            <div class="col-lg-4">
                <label class="form-label small text-muted mb-1">Buscar</label>
                <input type="text"
                       name="buscar"
                       class="form-control"
                       placeholder="Proveedor, documento o comprobante..."
                       value="<?php echo htmlspecialchars($texto); ?>">
            </div>

            <div class="col-lg-3">
                <label class="form-label small text-muted mb-1">Tipo de comprobante</label>
                <select name="tipo_filtro" class="form-select">
                    <option value="TODOS"<?php echo $tipo_filtro === "TODOS" ? " selected" : ""; ?>>
                        Todos los comprobantes
                    </option>
                    <option value="TICKET"<?php echo $tipo_filtro === "TICKET" ? " selected" : ""; ?>>
                        Solo tickets
                    </option>
                    <option value="BOLETA"<?php echo $tipo_filtro === "BOLETA" ? " selected" : ""; ?>>
                        Solo boletas
                    </option>
                    <option value="FACTURA"<?php echo $tipo_filtro === "FACTURA" ? " selected" : ""; ?>>
                        Solo facturas
                    </option>
                </select>
            </div>

            <div class="col-lg-3">
                <label class="form-label small text-muted mb-1">Ordenar por</label>
                <select name="orden" class="form-select">
                    <option value="recientes"<?php echo $orden === "recientes" ? " selected" : ""; ?>>
                        Más recientes primero
                    </option>
                    <option value="antiguas"<?php echo $orden === "antiguas" ? " selected" : ""; ?>>
                        Más antiguas primero
                    </option>
                    <option value="monto_mayor"<?php echo $orden === "monto_mayor" ? " selected" : ""; ?>>
                        Monto mayor primero
                    </option>
                    <option value="monto_menor"<?php echo $orden === "monto_menor" ? " selected" : ""; ?>>
                        Monto menor primero
                    </option>
                </select>
            </div>

            <div class="col-lg-2 d-flex gap-2">
                <button class="btn btn-outline-primary w-100" type="submit">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
                <a href="panel_admin.php?modulo=compras&accion=listar"
                   class="btn btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de compras -->
<div class="card card-table">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listado de compras</h5>
        <span class="text-muted extra-small">
            <?php echo !empty($lista_compras) ? count($lista_compras) . " registro(s)" : "Sin registros"; ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0 table-modern">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Comprobante</th>
                    <th>Proveedor</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-end">IGV</th>
                    <th class="text-end">Total</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($lista_compras)): ?>
                    <?php $i = 1; foreach ($lista_compras as $c): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td>
                                <i class="bi bi-calendar-date me-1 text-muted"></i>
                                <?php echo htmlspecialchars($c["fecha_compra"]); ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-receipt-cutoff me-1"></i>
                                    <?php
                                    echo htmlspecialchars(
                                        $c["nombre_tipo"] . " " .
                                        $c["serie_comprobante"] . "-" .
                                        $c["numero_comprobante"]
                                    );
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($c["razon_social"]); ?><br>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($c["numero_documento"]); ?>
                                </small>
                            </td>
                            <td class="text-end">S/ <?php echo number_format($c["subtotal"], 2); ?></td>
                            <td class="text-end">S/ <?php echo number_format($c["igv"], 2); ?></td>
                            <td class="text-end fw-semibold">
                                S/ <?php echo number_format($c["total"], 2); ?>
                            </td>
                            <td>
                                <?php
                                $estadoTexto = htmlspecialchars($c["estado"]);
                                $esActivo = (mb_strtoupper(trim($estadoTexto)) === "REGISTRADA"
                                    || mb_strtoupper(trim($estadoTexto)) === "ACTIVA");
                                ?>
                                <span class="badge <?php echo $esActivo ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $estadoTexto; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="panel_admin.php?modulo=compras&accion=ver&id=<?php echo (int)$c['id_compra']; ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            No se han registrado compras aún.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
