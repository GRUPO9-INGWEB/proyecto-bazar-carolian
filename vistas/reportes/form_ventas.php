<?php
// vistas/reportes/form_ventas.php
// Variables que vienen del controlador:
// $fecha_desde, $fecha_hasta, $tipo_filtro, $tiposComprobante

$accionActual = $_GET['accion'] ?? 'ventas';
?>

<h3>Reportes</h3>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link <?php echo ($accionActual === 'ventas' || $accionActual === 'listar') ? 'active' : ''; ?>"
           href="panel_admin.php?modulo=reportes&accion=ventas">
            Ventas
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($accionActual === 'productos') ? 'active' : ''; ?>"
           href="panel_admin.php?modulo=reportes&accion=productos">
            Productos vendidos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($accionActual === 'compras') ? 'active' : ''; ?>"
           href="panel_admin.php?modulo=reportes&accion=compras">
            Compras
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($accionActual === 'categorias') ? 'active' : ''; ?>"
           href="panel_admin.php?modulo=reportes&accion=categorias">
            Categorías
        </a>
    </li>
</ul>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Reportes de ventas</h5>
        <p class="card-text">
            Genere un PDF con el resumen de ventas por rango de fechas y tipo de comprobante.
        </p>

        <form method="post"
              action="panel_admin.php?modulo=reportes&accion=ventas_pdf"
              target="_blank">

            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date"
                           class="form-control"
                           id="fecha_desde"
                           name="fecha_desde"
                           value="<?php echo htmlspecialchars($fecha_desde); ?>">
                </div>

                <div class="col-md-3">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date"
                           class="form-control"
                           id="fecha_hasta"
                           name="fecha_hasta"
                           value="<?php echo htmlspecialchars($fecha_hasta); ?>">
                </div>

                <div class="col-md-4">
                    <label for="tipo_comprobante" class="form-label">Tipo de comprobante</label>
                    <select class="form-select"
                            id="tipo_comprobante"
                            name="tipo_comprobante">
                        <option value="TODOS">Todos</option>
                        <?php foreach ($tiposComprobante as $tc): ?>
                            <?php
                            $nombre = strtoupper($tc['nombre_tipo']);
                            $selected = ($tipo_filtro === $nombre) ? 'selected' : '';
                            ?>
                            <option value="<?php echo htmlspecialchars($nombre); ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($tc['nombre_tipo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 text-md-end">
                    <button type="submit" class="btn btn-primary w-100">
                        Generar PDF
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
