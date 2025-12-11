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
        </div>

        <div class="card-body p-0">

            <!-- Buscador de productos (como en ventas) -->
            <div class="p-3 border-bottom">
                <label class="form-label small text-muted mb-1">Buscar producto</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text"
                           id="buscadorProductoCompra"
                           class="form-control"
                           placeholder="Nombre o código interno...">
                </div>
                <div class="text-muted extra-small mt-1">
                    Escriba y haga clic en un producto de la lista para agregarlo al detalle.
                </div>
            </div>

            <!-- Resultados de búsqueda -->
            <div id="resultadosBusquedaCompra"
                 class="list-group list-group-flush small"
                 style="max-height: 220px; overflow-y:auto; display:none;">
            </div>

            <!-- Tabla de detalle -->
            <div class="table-responsive" style="max-height: 260px; overflow-y:auto;">
                <table class="table table-sm align-middle mb-0 table-modern"
                       id="tablaDetalleCompra">
                    <thead>
                    <tr>
                        <th style="width:40%">Producto</th>
                        <th style="width:15%" class="text-end">Cant.</th>
                        <th style="width:20%" class="text-end">Precio compra</th>
                        <th style="width:20%" class="text-end">Subtotal</th>
                        <th style="width:5%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- comienza vacío; las filas se agregan con JS -->
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
// =================== DATOS DE PRODUCTOS DESDE PHP ===================
const PRODUCTOS_COMPRA = <?php
    // vienen del controlador (CompraModelo::obtenerProductosActivos)
    echo json_encode($productos, JSON_UNESCAPED_UNICODE);
?>;

// =================== UTIL ===================
function escapeHtml(str) {
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

(function () {
    const tablaBody   = document.getElementById('tablaDetalleCompra').getElementsByTagName('tbody')[0];
    const totalLabel  = document.getElementById('totalCompra');
    const inputBuscar = document.getElementById('buscadorProductoCompra');
    const contRes     = document.getElementById('resultadosBusquedaCompra');

    // --------- Recalcular total ---------
    function recalcularTotal() {
        let total = 0;
        Array.from(tablaBody.rows).forEach(fila => {
            const subText = fila.querySelector('.subtotal-fila').innerText.replace('S/', '').trim();
            const sub = parseFloat(subText) || 0;
            total += sub;
        });
        if (totalLabel) {
            totalLabel.innerText = 'S/ ' + total.toFixed(2);
        }
    }

    // --------- Recalcular subtotal de una fila ---------
    function recalcularSubtotalFila(fila) {
        const cantInp   = fila.querySelector('input[name^="cantidad"]');
        const precioInp = fila.querySelector('input[name^="precio_compra"]');
        const cant   = parseFloat(cantInp.value) || 0;
        const precio = parseFloat(precioInp.value) || 0;
        const sub    = cant * precio;
        fila.querySelector('.subtotal-fila').innerText = 'S/ ' + sub.toFixed(2);
    }

    // --------- Agregar producto al detalle ---------
    function agregarProductoAlDetalle(prod) {
        const fila = document.createElement('tr');

        fila.innerHTML = `
            <td>
                <div class="fw-semibold small mb-0">${escapeHtml(prod.nombre_producto)}</div>
                <div class="extra-small text-muted">
                    Cod: ${prod.codigo_interno ? escapeHtml(prod.codigo_interno) : '—'}
                    · Stock actual: ${parseInt(prod.stock_actual ?? 0, 10)}
                </div>
                <input type="hidden" name="id_producto[]" value="${prod.id_producto}">
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
        `;

        tablaBody.appendChild(fila);
        recalcularSubtotalFila(fila);
        recalcularTotal();
    }

    // --------- Búsqueda en memoria (como ventas) ---------
    inputBuscar.addEventListener('input', function () {
        const texto = this.value.trim().toLowerCase();
        contRes.innerHTML = '';

        if (texto.length < 2) {
            contRes.style.display = 'none';
            return;
        }

        const filtrados = PRODUCTOS_COMPRA.filter(p => {
            const nombre = (p.nombre_producto || '').toLowerCase();
            const cod    = (p.codigo_interno || '').toLowerCase();
            return nombre.includes(texto) || cod.includes(texto);
        });

        if (!filtrados.length) {
            contRes.style.display = 'none';
            return;
        }

        filtrados.slice(0, 20).forEach(p => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';

            btn.innerHTML = `
                <div>
                    <div class="fw-semibold">${escapeHtml(p.nombre_producto)}</div>
                    <div class="extra-small text-muted">
                        Cod: ${p.codigo_interno ? escapeHtml(p.codigo_interno) : '—'}
                        · Stock: ${parseInt(p.stock_actual ?? 0, 10)}
                    </div>
                </div>
                <span class="badge bg-light text-dark border">ID: ${p.id_producto}</span>
            `;

            btn.addEventListener('click', () => {
                agregarProductoAlDetalle(p);
                inputBuscar.value = '';
                contRes.innerHTML = '';
                contRes.style.display = 'none';
                inputBuscar.focus();
            });

            contRes.appendChild(btn);
        });

        contRes.style.display = 'block';
    });

    // Ocultar lista si se hace click fuera
    document.addEventListener('click', function (e) {
        if (!contRes.contains(e.target) && e.target !== inputBuscar) {
            contRes.style.display = 'none';
        }
    });

    // --------- Eventos delegados en la tabla ---------
    tablaBody.addEventListener('input', function (e) {
        if (
            e.target.name &&
            (e.target.name.startsWith('cantidad') ||
             e.target.name.startsWith('precio_compra'))
        ) {
            const fila = e.target.closest('tr');
            recalcularSubtotalFila(fila);
            recalcularTotal();
        }
    });

    tablaBody.addEventListener('click', function (e) {
        const btn = e.target.closest('.btnEliminarFila');
        if (btn) {
            btn.closest('tr').remove();
            recalcularTotal();
        }
    });

    // total inicial
    recalcularTotal();
})();
</script>
