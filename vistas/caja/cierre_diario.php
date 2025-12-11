<?php
// vistas/caja/cierre_diario.php
?>
<h3 class="mb-1">
    <i class="bi bi-cash-stack me-2"></i>Caja / cierre diario
</h3>
<p class="text-muted">
    Resumen de las ventas por rango de fechas y tipo de pago. Desde aquí también
    puede generar el PDF de cierre para el periodo seleccionado.
</p>

<!-- Filtros de fecha -->
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form class="row g-3" method="get" action="panel_admin.php">
            <input type="hidden" name="modulo" value="caja">

            <div class="col-md-3">
                <label for="desde" class="form-label">Desde</label>
                <input
                    type="date"
                    class="form-control"
                    id="desde"
                    name="desde"
                    value="<?php echo htmlspecialchars($fecha_desde); ?>"
                >
            </div>

            <div class="col-md-3">
                <label for="hasta" class="form-label">Hasta</label>
                <input
                    type="date"
                    class="form-control"
                    id="hasta"
                    name="hasta"
                    value="<?php echo htmlspecialchars($fecha_hasta); ?>"
                >
            </div>

            <div class="col-md-6 d-flex align-items-end justify-content-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>

                <!-- Mantengo el mismo comportamiento: el controlador revisa $_GET["limpiar"] -->
                <button
                    type="submit"
                    name="limpiar"
                    value="1"
                    class="btn btn-outline-secondary"
                >
                    Limpiar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Resumen principal -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Resumen de caja</span>

        <?php if (!empty($resumen)): ?>
            <small class="text-muted">
                Del <strong><?php echo htmlspecialchars($fecha_desde); ?></strong>
                al <strong><?php echo htmlspecialchars($fecha_hasta); ?></strong>
            </small>
        <?php endif; ?>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 120px;">Fecha</th>
                    <th style="width: 140px;">Tipo de pago</th>
                    <th class="text-center" style="width: 90px;"># ventas</th>
                    <th class="text-end" style="width: 120px;">Subtotal (S/)</th>
                    <th class="text-end" style="width: 100px;">IGV (S/)</th>
                    <th class="text-end" style="width: 130px;">Total (S/)</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($resumen)): ?>
                <?php foreach ($resumen as $fila): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($fila['tipo_pago']); ?></td>
                        <td class="text-center">
                            <?php echo (int)$fila['cantidad_ventas']; ?>
                        </td>
                        <td class="text-end">
                            S/ <?php echo number_format($fila['total_subtotal'], 2); ?>
                        </td>
                        <td class="text-end">
                            S/ <?php echo number_format($fila['total_igv'], 2); ?>
                        </td>
                        <td class="text-end">
                            S/ <?php echo number_format($fila['total_general'], 2); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">
                        No se encontraron ventas en el rango seleccionado.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>

            <?php if (!empty($resumen)): ?>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="2" class="text-end">TOTALES</td>
                        <td class="text-center">
                            <?php echo (int)$totalCant; ?>
                        </td>
                        <td class="text-end">
                            S/ <?php echo number_format($totalSub, 2); ?>
                        </td>
                        <td class="text-end">
                            S/ <?php echo number_format($totalIgv, 2); ?>
                        </td>
                        <td class="text-end">
                            S/ <?php echo number_format($totalGral, 2); ?>
                        </td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php if (!empty($resumen)): ?>
    <div class="mt-3 d-flex justify-content-end">
        <form
            method="post"
            action="panel_admin.php?modulo=caja&accion=cierre_pdf"
            target="_blank"
        >
            <input
                type="hidden"
                name="fecha_desde"
                value="<?php echo htmlspecialchars($fecha_desde); ?>"
            >
            <input
                type="hidden"
                name="fecha_hasta"
                value="<?php echo htmlspecialchars($fecha_hasta); ?>"
            >

            <button type="submit" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>
                Generar PDF
            </button>
        </form>
    </div>
<?php endif; ?>
