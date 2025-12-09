<?php
// vistas/reportes/form_productos.php
// Variables: $fecha_desde, $fecha_hasta, $orden
$accionActual = $_GET['accion'] ?? 'productos';
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
        <h5 class="card-title">Reportes de productos vendidos</h5>
        <p class="card-text">
            Genere un PDF con el resumen de productos vendidos por rango de fechas.
            Puede elegir si desea ver los más vendidos o los menos vendidos.
        </p>

        <form method="post"
              action="panel_admin.php?modulo=reportes&accion=productos_pdf"
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
                    <label for="orden" class="form-label">Orden</label>
                    <select class="form-select" id="orden" name="orden">
                        <option value="MAS"   <?php echo ($orden === 'MAS')   ? 'selected' : ''; ?>>Más vendidos primero</option>
                        <option value="MENOS" <?php echo ($orden === 'MENOS') ? 'selected' : ''; ?>>Menos vendidos primero</option>
                        <option value="TODOS" <?php echo ($orden === 'TODOS') ? 'selected' : ''; ?>>Solo listado (sin ordenar)</option>
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
