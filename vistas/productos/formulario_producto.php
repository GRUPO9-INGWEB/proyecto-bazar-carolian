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

// Icono según contexto
$icono_titulo = $es_edicion ? "bi-pencil-square" : "bi-boxes";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi <?php echo $icono_titulo; ?> me-2"></i>
            <?php echo $titulo; ?>
        </h3>
        <p class="text-muted small mb-0">
            <?php echo $es_edicion
                ? "Actualiza los datos del producto, stock y configuración de precios."
                : "Registra un nuevo producto para el inventario de la botica-bazar."; ?>
        </p>
    </div>

    <a href="panel_admin.php?modulo=productos&accion=listar"
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
                <input type="hidden" name="id_producto" value="<?php echo $producto["id_producto"]; ?>">
            <?php endif; ?>

            <!-- FILA 1: CATEGORÍA + CÓDIGOS -->
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Categoría</label>
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
                <label class="form-label small text-muted mb-1">Código interno</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-upc-scan"></i>
                    </span>
                    <input type="text"
                           name="codigo_interno"
                           class="form-control border-start-0"
                           value="<?php echo $es_edicion ? htmlspecialchars($producto["codigo_interno"]) : ""; ?>">
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Código de barras</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-upc"></i>
                    </span>
                    <input type="text"
                           name="codigo_barras"
                           class="form-control border-start-0"
                           value="<?php echo $es_edicion ? htmlspecialchars($producto["codigo_barras"]) : ""; ?>">
                </div>
            </div>

            <!-- FILA 2: NOMBRE + STOCKS -->
            <div class="col-md-6">
                <label class="form-label small text-muted mb-1">Nombre del producto</label>
                <input type="text"
                       name="nombre_producto"
                       class="form-control"
                       required
                       value="<?php echo $es_edicion ? htmlspecialchars($producto["nombre_producto"]) : ""; ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Stock mínimo</label>
                <input type="number"
                       name="stock_minimo"
                       class="form-control"
                       min="0"
                       value="<?php echo $es_edicion ? intval($producto["stock_minimo"]) : 0; ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Stock actual</label>
                <input type="number"
                       name="stock_actual"
                       class="form-control"
                       min="0"
                       value="<?php echo $es_edicion ? intval($producto["stock_actual"]) : 0; ?>">
            </div>

            <!-- FILA 3: FECHA + PRECIOS -->
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Fecha de caducidad</label>
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
                <label class="form-label small text-muted mb-1">Precio de compra</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        S/
                    </span>
                    <input type="number"
                           step="0.01"
                           min="0"
                           name="precio_compra"
                           class="form-control border-start-0"
                           value="<?php echo $es_edicion ? htmlspecialchars($producto["precio_compra"]) : "0.00"; ?>">
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Precio de venta</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        S/
                    </span>
                    <input type="number"
                           step="0.01"
                           min="0"
                           name="precio_venta"
                           class="form-control border-start-0"
                           value="<?php echo $es_edicion ? htmlspecialchars($producto["precio_venta"]) : "0.00"; ?>">
                </div>
            </div>

            <!-- FILA 4: DESCRIPCIÓN -->
            <div class="col-md-12">
                <label class="form-label small text-muted mb-1">Descripción</label>
                <textarea name="descripcion_producto"
                          class="form-control"
                          rows="3"><?php
                    echo $es_edicion ? htmlspecialchars($producto["descripcion_producto"]) : "";
                ?></textarea>
            </div>

            <!-- FILA 5: IGV -->
            <div class="col-md-4">
                <div class="form-check mt-2">
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
            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a href="panel_admin.php?modulo=productos&accion=listar"
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
