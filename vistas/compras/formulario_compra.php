<?php
// vistas/compras/formulario_compra.php
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi bi-bag-plus me-2"></i>
            Nueva compra
        </h3>
        <p class="text-muted small mb-0">
            Registra el ingreso de mercadería (compras a proveedores) para actualizar el inventario.
        </p>
    </div>

    <a href="panel_admin.php?modulo=compras&accion=listar"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-danger mb-3">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<form action="panel_admin.php?modulo=compras&accion=guardar_nueva"
      method="post"
      id="formCompra">

    <!-- Datos del comprobante -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-receipt-cutoff"></i>
            <span>Datos del comprobante</span>
        </div>
        <div class="card-body row g-3">

            <!-- Proveedor -->
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Proveedor</label>
                <select name="id_proveedor"
                        class="form-select"
                        required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($proveedores as $p): ?>
                        <option value="<?php echo $p["id_proveedor"]; ?>">
                            <?php
                            echo htmlspecialchars($p["razon_social"])
                                . " (" . htmlspecialchars($p["numero_documento"]) . ")";
                            ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tipo comprobante -->
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Tipo de comprobante</label>
                <select name="id_tipo_comprobante"
                        class="form-select"
                        required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($tipos_comprobante as $t): ?>
                        <option value="<?php echo $t["id_tipo_comprobante"]; ?>">
                            <?php echo htmlspecialchars($t["nombre_tipo"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Serie -->
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Serie</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-hash"></i>
                    </span>
                    <input type="text"
                           name="serie_comprobante"
                           class="form-control border-start-0"
                           placeholder="C001">
                </div>
            </div>

            <!-- Número -->
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Número</label>
                <input type="text"
                       name="numero_comprobante"
                       class="form-control"
                       placeholder="Se generará si se deja vacío">
            </div>
        </div>
    </div>

    <!-- Detalle de productos -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-box-seam"></i>
                <span>Detalle de productos</span>
            </div>
            <button type="button"
                    class="btn btn-sm btn-outline-primary"
                    id="btnAgregarFila">
                <i class="bi bi-plus-circle me-1"></i> Agregar fila
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0 table-modern"
                       id="tablaDetalleCompra">
                    <thead>
                    <tr>
                        <th style="width:35%">Producto</th>
                        <th style="width:15%" class="text-end">Cant.</th>
                        <th style="width:20%" class="text-end">Precio compra</th>
                        <th style="width:20%" class="text-end">Subtotal</th>
                        <th style="width:10%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Fila plantilla -->
                    <tr>
                        <td>
                            <select name="id_producto[]" class="form-select form-select-sm" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($productos as $prod): ?>
                                    <option value="<?php echo $prod["id_producto"]; ?>">
                                        <?php
                                        echo htmlspecialchars($prod["nombre_producto"])
                                            . " (Stock: " . (int)$prod["stock_actual"] . ")";
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="number"
                                   name="cantidad[]"
                                   class="form-control form-control-sm text-end"
                                   value="1"
                                   min="1"
                                   required>
                        </td>
                        <td>
                            <input type="number"
                                   step="0.01"
                                   name="precio_compra[]"
                                   class="form-control form-control-sm text-end"
                                   value="0.00"
                                   min="0"
                                   required>
                        </td>
                        <td class="text-end subtotal-fila">S/ 0.00</td>
                        <td class="text-center">
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger btnEliminarFila"
                                    title="Eliminar fila">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Total estimado visual -->
            <div class="d-flex justify-content-end mt-3 px-3 pb-3">
                <div class="text-end">
                    <div class="text-muted extra-small">Total estimado</div>
                    <div class="fw-semibold fs-5" id="totalCompra">S/ 0.00</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones -->
    <div class="d-flex justify-content-end gap-2 mb-3">
        <a href="panel_admin.php?modulo=compras&accion=listar"
           class="btn btn-outline-secondary">
            Cancelar
        </a>
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check2-circle me-1"></i> Guardar compra
        </button>
    </div>
</form>

<script>
// JS para agregar / eliminar filas y recalcular subtotales + total
(function () {
    const tabla = document
        .getElementById('tablaDetalleCompra')
        .getElementsByTagName('tbody')[0];
    const btnAgregar = document.getElementById('btnAgregarFila');
    const totalLabel = document.getElementById('totalCompra');

    function recalcularTotal() {
        let total = 0;
        Array.from(tabla.rows).forEach(fila => {
            const subText = fila.querySelector('.subtotal-fila').innerText.replace('S/', '').trim();
            const sub = parseFloat(subText) || 0;
            total += sub;
        });
        if (totalLabel) {
            totalLabel.innerText = 'S/ ' + total.toFixed(2);
        }
    }

    btnAgregar.addEventListener('click', function () {
        const filaBase = tabla.rows[0];
        const nueva = filaBase.cloneNode(true);

        // limpiar valores
        nueva.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);
        nueva.querySelectorAll('input').forEach(inp => {
            if (inp.name.startsWith('cantidad')) inp.value = 1;
            if (inp.name.startsWith('precio_compra')) inp.value = 0;
        });
        nueva.querySelector('.subtotal-fila').innerText = 'S/ 0.00';

        tabla.appendChild(nueva);
        recalcularTotal();
    });

    tabla.addEventListener('input', function (e) {
        if (
            e.target.name &&
            (e.target.name.startsWith('cantidad') ||
             e.target.name.startsWith('precio_compra'))
        ) {
            const fila = e.target.closest('tr');
            const cant = parseFloat(fila.querySelector('input[name^=\"cantidad\"]').value) || 0;
            const precio = parseFloat(fila.querySelector('input[name^=\"precio_compra\"]').value) || 0;
            const sub = cant * precio;
            fila.querySelector('.subtotal-fila').innerText = 'S/ ' + sub.toFixed(2);
            recalcularTotal();
        }
    });

    tabla.addEventListener('click', function (e) {
        if (e.target.closest('.btnEliminarFila')) {
            const filas = tabla.rows.length;
            if (filas > 1) {
                e.target.closest('tr').remove();
                recalcularTotal();
            }
        }
    });

    // cálculo inicial
    recalcularTotal();
})();
</script>
