<?php
// vistas/categorias/listado_categorias.php

if (!isset($mensaje)) $mensaje = "";
if (!isset($buscar))  $buscar  = "";
if (!isset($orden))   $orden   = "DESC";
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi bi-tags me-2"></i>
            Categorías
        </h3>
        <p class="text-muted small mb-0">
            En este módulo puede registrar, editar y activar/desactivar categorías de productos.
        </p>
    </div>

    <div class="mt-2 mt-md-0">
        <a href="panel_admin.php?modulo=categorias&accion=nuevo"
           class="btn btn-primary btn-sm">
            <i class="bi bi-tag-plus me-1"></i> Nueva categoría
        </a>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info mb-3">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Filtro de búsqueda + orden -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="get" action="panel_admin.php">
            <input type="hidden" name="modulo" value="categorias">
            <input type="hidden" name="accion" value="listar">

            <!-- Campo de búsqueda -->
            <div class="col-md-5">
                <label class="form-label small text-muted mb-1">Buscar categoría</label>
                <input type="text"
                       name="buscar"
                       class="form-control"
                       placeholder="Nombre o descripción..."
                       value="<?php echo htmlspecialchars($buscar); ?>">
            </div>

            <!-- Selector de orden -->
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Ordenar por fecha de registro</label>
                <select name="orden" class="form-select">
                    <option value="DESC" <?php if ($orden === "DESC") echo "selected"; ?>>
                        Mostrar primero las más recientes
                    </option>
                    <option value="ASC" <?php if ($orden === "ASC") echo "selected"; ?>>
                        Mostrar primero las más antiguas
                    </option>
                </select>
            </div>

            <!-- Botones -->
            <div class="col-md-3 d-flex gap-2">
                <div class="flex-fill">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> Buscar
                    </button>
                </div>
                <div class="flex-fill">
                    <a href="panel_admin.php?modulo=categorias&accion=listar"
                       class="btn btn-outline-secondary w-100">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de categorías -->
<div class="card card-table">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listado de categorías</h5>
        <span class="text-muted extra-small">
            <?php echo !empty($lista_categorias) ? count($lista_categorias) . " registro(s)" : "Sin registros"; ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive table-wrapper-scroll">
            <table class="table table-striped table-sm align-middle mb-0 table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th style="width: 190px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($lista_categorias)): ?>
                    <?php foreach ($lista_categorias as $c): ?>
                        <tr>
                            <td><?php echo $c["id_categoria"]; ?></td>
                            <td>
                                <i class="bi bi-tag me-1"></i>
                                <?php echo htmlspecialchars($c["nombre_categoria"]); ?>
                            </td>
                            <td><?php echo htmlspecialchars($c["descripcion_categoria"]); ?></td>
                            <td>
                                <?php if ($c["estado"] == 1): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> Activa
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-slash-circle me-1"></i> Inactiva
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="panel_admin.php?modulo=categorias&accion=editar&id=<?php echo $c["id_categoria"]; ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> Editar
                                    </a>

                                    <?php if ($c["estado"] == 1): ?>
                                        <a href="panel_admin.php?modulo=categorias&accion=cambiar_estado&id=<?php echo $c["id_categoria"]; ?>&estado=0"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('¿Desactivar esta categoría?');">
                                            <i class="bi bi-x-circle me-1"></i> Desactivar
                                        </a>
                                    <?php else: ?>
                                        <a href="panel_admin.php?modulo=categorias&accion=cambiar_estado&id=<?php echo $c["id_categoria"]; ?>&estado=1"
                                           class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-check-circle me-1"></i> Activar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            No hay categorías registradas.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
