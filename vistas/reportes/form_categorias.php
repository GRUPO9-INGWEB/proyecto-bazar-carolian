<?php
// vistas/reportes/form_categorias.php
// Variables desde el controlador:
// $id_categoria (int), $categorias (lista de categorías activas)

$accionActual = $_GET['accion'] ?? 'categorias';
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
        <h5 class="card-title">Productos por categoría</h5>
        <p class="card-text">
            Genere un PDF con el listado de productos agrupados por categoría.
            Puede elegir una categoría específica o ver todas.
        </p>

        <form method="post"
              action="panel_admin.php?modulo=reportes&accion=categorias_pdf"
              target="_blank">

            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="id_categoria" class="form-label">Categoría</label>
                    <select class="form-select" id="id_categoria" name="id_categoria">
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
                    <button type="submit" class="btn btn-primary w-100 mt-3 mt-md-0">
                        Generar PDF
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
