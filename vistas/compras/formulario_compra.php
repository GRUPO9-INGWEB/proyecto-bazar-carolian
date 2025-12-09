<?php
// vistas/compras/formulario_compra.php
?>
<h3>Nueva compra</h3>
<p class="text-muted">
    Registra el ingreso de mercadería (compras a proveedores).
</p>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-danger py-2"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<form action="panel_admin.php?modulo=compras&accion=guardar_nueva" method="post" id="formCompra">

    <div class="card mb-3">
        <div class="card-header">Datos del comprobante</div>
        <div class="card-body row g-3">

            <div class="col-md-4">
                <label class="form-label">Proveedor</label>
                <select name="id_proveedor" class="form-select form-select-sm" required>
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

            <div class="col-md-4">
                <label class="form-label">Tipo comprobante</label>
                <select name="id_tipo_comprobante" class="form-select form-select-sm" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($tipos_comprobante as $t): ?>
                        <option value="<?php echo $t["id_tipo_comprobante"]; ?>">
                            <?php echo htmlspecialchars($t["nombre_tipo"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Serie</label>
                <input type="text" name="serie_comprobante"
                       class="form-control form-control-sm"
                       placeholder="C001">
            </div>

            <div class="col-md-2">
                <label class="form-label">Número</label>
                <input type="text" name="numero_comprobante"
                       class="form-control form-control-sm"
                       placeholder="se generará si se deja vacío">
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between">
            <span>Detalle de productos</span>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarFila">
                Agregar fila
            </button>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-sm align-middle" id="tablaDetalleCompra">
                <thead class="table-light">
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
                        <input type="number" name="cantidad[]" class="form-control form-control-sm text-end"
                               value="1" min="1" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="precio_compra[]"
                               class="form-control form-control-sm text-end"
                               value="0.00" min="0" required>
                    </td>
                    <td class="text-end subtotal-fila">S/ 0.00</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btnEliminarFila">
                            &times;
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <button type="submit" class="btn btn-success">
            Guardar compra
        </button>
        <a href="panel_admin.php?modulo=compras&accion=listar" class="btn btn-secondary ms-2">
            Cancelar
        </a>
    </div>
</form>

<script>
// JS muy simple para agregar / eliminar filas y recalcular subtotal visual
(function () {
    const tabla = document.getElementById('tablaDetalleCompra').getElementsByTagName('tbody')[0];
    const btnAgregar = document.getElementById('btnAgregarFila');

    btnAgregar.addEventListener('click', function () {
        const filaBase = tabla.rows[0];
        const nueva = filaBase.cloneNode(true);

        // limpiar valores
        nueva.querySelectorAll('input').forEach(inp => {
            if (inp.name.startsWith('cantidad')) inp.value = 1;
            if (inp.name.startsWith('precio_compra')) inp.value = 0;
        });
        nueva.querySelector('.subtotal-fila').innerText = 'S/ 0.00';

        tabla.appendChild(nueva);
    });

    tabla.addEventListener('input', function (e) {
        if (e.target.name && (e.target.name.startsWith('cantidad') || e.target.name.startsWith('precio_compra'))) {
            const fila = e.target.closest('tr');
            const cant = parseFloat(fila.querySelector('input[name^="cantidad"]').value) || 0;
            const precio = parseFloat(fila.querySelector('input[name^="precio_compra"]').value) || 0;
            const sub = cant * precio;
            fila.querySelector('.subtotal-fila').innerText = 'S/ ' + sub.toFixed(2);
        }
    });

    tabla.addEventListener('click', function (e) {
        if (e.target.classList.contains('btnEliminarFila')) {
            const filas = tabla.rows.length;
            if (filas > 1) {
                e.target.closest('tr').remove();
            }
        }
    });
})();
</script>
