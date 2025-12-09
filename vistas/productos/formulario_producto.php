<?php
// vistas/productos/formulario_producto.php

$es_edicion = $producto !== null;
$titulo = $es_edicion ? "Editar producto" : "Nuevo producto";
$accion_formulario = $es_edicion
    ? "panel_admin.php?modulo=productos&accion=guardar_edicion"
    : "panel_admin.php?modulo=productos&accion=guardar_nuevo";

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
        <input type="hidden" name="id_producto" value="<?php echo $producto["id_producto"]; ?>">
    <?php endif; ?>

    <!-- FILA 1: CATEGORÍA + CÓDIGOS -->
    <div class="col-md-4">
        <label class="form-label">Categoría</label>
        <select name="id_categoria" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php foreach ($lista_categorias as $cat): ?>
                <option value="<?php echo $cat["id_categoria"]; ?>"
                    <?php
                    if ($es_edicion && $producto["id_categoria"] == $cat["id_categoria"]) {
                        echo "selected";
                    }
                    ?>>
                    <?php echo htmlspecialchars($cat["nombre_categoria"]); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Código interno</label>
        <input type="text"
               name="codigo_interno"
               class="form-control"
               value="<?php echo $es_edicion ? htmlspecialchars($producto["codigo_interno"]) : ""; ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label">Código de barras</label>
        <input type="text"
               name="codigo_barras"
               class="form-control"
               value="<?php echo $es_edicion ? htmlspecialchars($producto["codigo_barras"]) : ""; ?>">
    </div>

    <!-- FILA 2: NOMBRE + STOCKS -->
    <div class="col-md-6">
        <label class="form-label">Nombre del producto</label>
        <input type="text"
               name="nombre_producto"
               class="form-control"
               required
               value="<?php echo $es_edicion ? htmlspecialchars($producto["nombre_producto"]) : ""; ?>">
    </div>

    <div class="col-md-3">
        <label class="form-label">Stock mínimo</label>
        <input type="number"
               name="stock_minimo"
               class="form-control"
               min="0"
               value="<?php echo $es_edicion ? intval($producto["stock_minimo"]) : 0; ?>">
    </div>

    <div class="col-md-3">
        <label class="form-label">Stock actual</label>
        <input type="number"
               name="stock_actual"
               class="form-control"
               min="0"
               value="<?php echo $es_edicion ? intval($producto["stock_actual"]) : 0; ?>">
    </div>

    <!-- FILA 3: FECHA + PRECIOS -->
    <div class="col-md-4">
        <label class="form-label">Fecha de caducidad</label>
        <input type="date"
               name="fecha_caducidad"
               class="form-control"
               value="<?php
                    if ($es_edicion && !empty($producto["fecha_caducidad"])) {
                        echo $producto["fecha_caducidad"]; // YYYY-MM-DD
                    }
               ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label">Precio de compra</label>
        <input type="number"
               step="0.01"
               min="0"
               name="precio_compra"
               class="form-control"
               value="<?php echo $es_edicion ? htmlspecialchars($producto["precio_compra"]) : "0.00"; ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label">Precio de venta</label>
        <input type="number"
               step="0.01"
               min="0"
               name="precio_venta"
               class="form-control"
               value="<?php echo $es_edicion ? htmlspecialchars($producto["precio_venta"]) : "0.00"; ?>">
    </div>

    <!-- FILA 4: DESCRIPCIÓN -->
    <div class="col-md-12">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion_producto"
                  class="form-control"
                  rows="3"><?php
            echo $es_edicion ? htmlspecialchars($producto["descripcion_producto"]) : "";
        ?></textarea>
    </div>

    <!-- FILA 5: IGV -->
    <div class="col-md-4">
        <div class="form-check mt-4">
            <input class="form-check-input"
                   type="checkbox"
                   id="chk_afecta_igv"
                   name="afecta_igv"
                <?php
                if ($es_edicion) {
                    if ($producto["afecta_igv"] == 1) echo "checked";
                } else {
                    echo "checked"; // por defecto sí afecta IGV
                }
                ?>>
            <label class="form-check-label" for="chk_afecta_igv">
                Producto afecta IGV
            </label>
        </div>
    </div>

    <!-- FILA 6: BOTONES -->
    <div class="col-12">
        <a href="panel_admin.php?modulo=productos&accion=listar" class="btn btn-secondary">
            Volver
        </a>
        <button type="submit" class="btn btn-primary">
            Guardar
        </button>
    </div>
</form>
