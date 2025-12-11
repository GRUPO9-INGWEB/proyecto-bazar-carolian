<?php
// vistas/reportes/form_categorias.php
// Variables: $id_categoria, $categorias

$accionActual = $_GET['accion'] ?? 'categorias';
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
            <i class="bi bi-tags me-1"></i> Productos por categoría
        </h5>
        <p class="card-text text-muted small mb-4">
            Genere un PDF con el listado de productos agrupados por categoría.
            Puede elegir una categoría específica o incluir todas.
        </p>

        <form method="post"
              action="panel_admin.php?modulo=reportes&accion=categorias_pdf"
              target="_blank">

            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="id_categoria" class="form-label">Categoría</label>
                    <select class="form-select form-select-sm" id="id_categoria" name="id_categoria">
                        <option value="0">Todas las categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo (int)$cat['id_categoria']; ?>"
                                <?php echo ($id_categoria == $cat['id_categoria']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
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
