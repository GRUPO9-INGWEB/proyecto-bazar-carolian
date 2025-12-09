<?php
// vistas/categorias/listado_categorias.php

if (!isset($mensaje)) $mensaje = "";
if (!isset($buscar))  $buscar  = "";
if (!isset($orden))   $orden   = "DESC";
?>
<h3>Categorías</h3>
<p class="text-muted">
    En este módulo puede registrar, editar y activar/desactivar categorías de productos.
</p>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Filtro de búsqueda + orden -->
<form class="row g-2 mb-3" method="get" action="panel_admin.php">
    <input type="hidden" name="modulo" value="categorias">
    <input type="hidden" name="accion" value="listar">

    <!-- Campo de búsqueda -->
    <div class="col-md-4">
        <input type="text"
               name="buscar"
               class="form-control"
               placeholder="Buscar por nombre o descripción..."
               value="<?php echo htmlspecialchars($buscar); ?>">
    </div>

    <!-- Selector de orden -->
    <div class="col-md-3">
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
    <div class="col-md-3">
        <button type="submit" class="btn btn-outline-primary">
            Buscar
        </button>
        <a href="panel_admin.php?modulo=categorias&accion=listar"
           class="btn btn-outline-secondary">
            Limpiar
        </a>
    </div>

    <!-- Nueva categoría -->
    <div class="col-md-2 text-md-end mt-2 mt-md-0">
        <a href="panel_admin.php?modulo=categorias&accion=nuevo" class="btn btn-primary btn-sm">
            Nueva categoría
        </a>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-striped table-sm align-middle">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th style="width: 160px;">Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($lista_categorias)): ?>
            <?php foreach ($lista_categorias as $c): ?>
                <tr>
                    <td><?php echo $c["id_categoria"]; ?></td>
                    <td><?php echo htmlspecialchars($c["nombre_categoria"]); ?></td>
                    <td><?php echo htmlspecialchars($c["descripcion_categoria"]); ?></td>
                    <td>
                        <?php if ($c["estado"] == 1): ?>
                            <span class="badge bg-success">Activa</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactiva</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="panel_admin.php?modulo=categorias&accion=editar&id=<?php echo $c["id_categoria"]; ?>"
                           class="btn btn-sm btn-outline-primary">
                            Editar
                        </a>

                        <?php if ($c["estado"] == 1): ?>
                            <a href="panel_admin.php?modulo=categorias&accion=cambiar_estado&id=<?php echo $c["id_categoria"]; ?>&estado=0"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('¿Desactivar esta categoría?');">
                                Desactivar
                            </a>
                        <?php else: ?>
                            <a href="panel_admin.php?modulo=categorias&accion=cambiar_estado&id=<?php echo $c["id_categoria"]; ?>&estado=1"
                               class="btn btn-sm btn-outline-success">
                                Activar
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center">No hay categorías registradas.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
