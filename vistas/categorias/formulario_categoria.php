<?php
// vistas/categorias/formulario_categoria.php

$es_edicion = $categoria !== null;
$titulo = $es_edicion ? "Editar categoría" : "Nueva categoría";
$accion_formulario = $es_edicion
    ? "panel_admin.php?modulo=categorias&accion=guardar_edicion"
    : "panel_admin.php?modulo=categorias&accion=guardar_nuevo";

if (!isset($mensaje)) {
    $mensaje = "";
}

// Icono para el título
$icono_titulo = $es_edicion ? "bi-pencil-square" : "bi-tags";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi <?php echo $icono_titulo; ?> me-2"></i>
            <?php echo $titulo; ?>
        </h3>
        <p class="text-muted small mb-0">
            <?php echo $es_edicion
                ? "Actualiza el nombre y la descripción de la categoría de productos."
                : "Registra una nueva categoría para organizar los productos de la botica-bazar."; ?>
        </p>
    </div>

    <a href="panel_admin.php?modulo=categorias&accion=listar"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-warning mb-3">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="<?php echo $accion_formulario; ?>" method="post" class="row g-3">

            <?php if ($es_edicion): ?>
                <input type="hidden" name="id_categoria" value="<?php echo $categoria["id_categoria"]; ?>">
            <?php endif; ?>

            <div class="col-md-6">
                <label class="form-label small text-muted mb-1">Nombre de la categoría</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-tag"></i>
                    </span>
                    <input type="text"
                           name="nombre_categoria"
                           class="form-control border-start-0"
                           required
                           autocomplete="off"
                           value="<?php echo $es_edicion ? htmlspecialchars($categoria["nombre_categoria"]) : ""; ?>">
                </div>
            </div>

            <div class="col-md-12">
                <label class="form-label small text-muted mb-1">Descripción</label>
                <textarea name="descripcion_categoria"
                          class="form-control"
                          rows="3"
                          placeholder="Uso de la categoría, tipo de productos que agrupa, etc."><?php
                    echo $es_edicion ? htmlspecialchars($categoria["descripcion_categoria"]) : "";
                ?></textarea>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a href="panel_admin.php?modulo=categorias&accion=listar"
                   class="btn btn-outline-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle me-1"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
