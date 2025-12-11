<?php
// vistas/reportes/form_ventas.php
// Variables: $fecha_desde, $fecha_hasta, $tipo_filtro, $tiposComprobante

$accionActual = $_GET['accion'] ?? 'ventas';
?>

<h3 class="mb-1">
    <i class="bi bi-bar-chart-line me-2"></i> Reportes
</h3>
<p class="text-muted small mb-3">
    Centro de reportes del sistema: genere resúmenes en PDF de ventas, productos vendidos,
    compras y categorías.
</p>

<ul class="nav nav-tabs mb-3 small">
    <li class="nav-item">
        <a class="nav-link <?php echo ($accionActual === 'ventas' || $accionActual === 'listar') ? 'active' : ''; ?>"
           href="panel_admin.php?modulo=reportes&accion=ventas">
            <i class="bi bi-receipt-cutoff me-1"></i> Ventas
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($accionActual === 'productos') ? 'active' : ''; ?>"
           href="panel_admin.php?modulo=reportes&accion=productos">
            <i class="bi bi-box-seam me-1"></i> Productos vendidos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($accionActual === 'compras') ? 'active' : ''; ?>"
           href="panel_admin.php?modulo=reportes&accion=compras">
            <i class="bi bi-cart-check me-1"></i> Compras
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($accionActual === 'categorias') ? 'active' : ''; ?>"
           href="panel_admin.php?modulo=reportes&accion=categorias">
            <i class="bi bi-tags me-1"></i> Categorías
        </a>
    </li>
</ul>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title mb-1">
            <i class="bi bi-receipt-cutoff me-1"></i> Reporte de ventas
        </h5>
        <p class="card-text text-muted small mb-4">
            Genere un PDF con el resumen de ventas por rango de fechas y tipo de comprobante.
            Si deja las fechas vacías, se considerará todo el histórico disponible.
        </p>

        <form method="post"
              action="panel_admin.php?modulo=reportes&accion=ventas_pdf"
              target="_blank">

            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date"
                           class="form-control form-control-sm"
                           id="fecha_desde"
                           name="fecha_desde"
                           value="<?php echo htmlspecialchars($fecha_desde); ?>">
                </div>

                <div class="col-md-3">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date"
                           class="form-control form-control-sm"
                           id="fecha_hasta"
                           name="fecha_hasta"
                           value="<?php echo htmlspecialchars($fecha_hasta); ?>">
                </div>

                <div class="col-md-4">
                    <label for="tipo_comprobante" class="form-label">Tipo de comprobante</label>
                    <select class="form-select form-select-sm"
                            id="tipo_comprobante"
                            name="tipo_comprobante">
                        <option value="TODOS">Todos</option>
                        <?php foreach ($tiposComprobante as $tc): ?>
                            <?php
                            $nombre   = strtoupper($tc['nombre_tipo']);
                            $selected = ($tipo_filtro === $nombre) ? 'selected' : '';
                            ?>
                            <option value="<?php echo htmlspecialchars($nombre); ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($tc['nombre_tipo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 text-md-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100 mt-2 mt-md-0">
                        <i class="bi bi-filetype-pdf me-1"></i> Generar PDF
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
