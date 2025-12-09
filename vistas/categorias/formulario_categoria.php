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
?>
<h3><?php echo $titulo; ?></h3>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-warning">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<form action="<?php echo $accion_formulario; ?>" method="post" class="row g-3">
    <?php if ($es_edicion): ?>
        <input type="hidden" name="id_categoria" value="<?php echo $categoria["id_categoria"]; ?>">
    <?php endif; ?>

    <div class="col-md-6">
        <label class="form-label">Nombre de la categoría</label>
        <input type="text"
               name="nombre_categoria"
               class="form-control"
               required
               value="<?php echo $es_edicion ? htmlspecialchars($categoria["nombre_categoria"]) : ""; ?>">
    </div>

    <div class="col-md-12">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion_categoria"
                  class="form-control"
                  rows="3"><?php
            echo $es_edicion ? htmlspecialchars($categoria["descripcion_categoria"]) : "";
        ?></textarea>
    </div>

    <div class="col-12">
        <a href="panel_admin.php?modulo=categorias&accion=listar" class="btn btn-secondary">
            Volver
        </a>
        <button type="submit" class="btn btn-primary">
            Guardar
        </button>
    </div>
</form>
