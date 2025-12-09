<?php
// vistas/ventas/formulario_venta.php

if (!isset($mensaje)) {
    $mensaje = "";
}
?>
<h3>Nueva venta</h3>
<p class="text-muted">
    Registre una nueva venta seleccionando el cliente, el comprobante
    y los productos vendidos.
</p>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-warning">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<form method="post"
      action="panel_admin.php?modulo=ventas&accion=guardar_nueva"
      id="form-venta">

    <!-- CABECERA -->
    <div class="card mb-4">
        <div class="card-header">Datos del comprobante</div>
        <div class="card-body row g-3">

            <div class="col-md-3">
                <label class="form-label form-label-sm">Tipo de comprobante</label>
                <select name="id_tipo_comprobante"
                        id="tipoComprobanteSelect"
                        class="form-select form-select-sm" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($tipos_comprobante as $t): ?>
                        <option value="<?php echo $t["id_tipo_comprobante"]; ?>">
                            <?php echo htmlspecialchars($t["nombre_tipo"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label form-label-sm">Serie</label>
                <input type="text" name="serie_comprobante"
                       id="serieComprobante"
                       class="form-control form-control-sm"
                       placeholder="Ej: B001 (automático)"
                       readonly>
            </div>

            <div class="col-md-3">
                <label class="form-label form-label-sm">Número</label>
                <input type="text" name="numero_comprobante"
                       id="numeroComprobante"
                       class="form-control form-control-sm"
                       placeholder="Se generará automáticamente al guardar"
                       readonly>
            </div>

            <div class="col-md-3">
                <label class="form-label form-label-sm">Tipo de pago</label>
                <select name="tipo_pago"
                        id="tipoPagoSelect"
                        class="form-select form-select-sm">
                    <option value="EFECTIVO">Efectivo</option>
                    <option value="TARJETA">Tarjeta</option>
                    <option value="YAPE">Yape/Plin</option>
                    <option value="TRANSFERENCIA">Transferencia</option>
                </select>
            </div>

            <!-- MONTO RECIBIDO (solo para efectivo, el JS lo oculta/muestra) -->
            <div class="col-md-3" id="grupoMontoEfectivo">
                <label class="form-label form-label-sm">Monto recibido (efectivo)</label>
                <input type="number"
                       step="0.01"
                       min="0"
                       name="monto_recibido"
                       id="montoRecibidoInput"
                       class="form-control form-control-sm"
                       placeholder="Ej: 20.00">
                <small class="text-muted">
                    Se usa para calcular el vuelto.
                </small>
            </div>

            <div class="col-md-3 d-flex align-items-end" id="grupoVuelto">
                <div>
                    <strong>Vuelto:</strong><br>
                    S/ <span id="vuelto_general">0.00</span>
                </div>
            </div>

            <!-- Cliente (grupo completo, lo ocultamos para ticket) -->
            <div class="col-md-6" id="grupoCliente">
                <label class="form-label form-label-sm">Cliente</label>
                <select name="id_cliente"
                        class="form-select form-select-sm"
                        id="selectCliente"
                        required>
                    <option value="">Seleccione cliente...</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?php echo $c["id_cliente"]; ?>">
                            <?php
                            $doc = $c["tipo_documento"] . ": " . $c["numero_documento"];
                            if (!empty($c["razon_social"])) {
                                $nom = $c["razon_social"];
                            } else {
                                $nom = trim($c["nombres"] . " " . $c["apellidos"]);
                            }
                            echo htmlspecialchars($doc . " - " . $nom);
                            ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted d-block mt-1">
                    Para <strong>Boleta</strong> suele usarse DNI. Para <strong>Factura</strong>, RUC con razón social.
                </small>
            </div>

        </div>
    </div>

    <!-- DETALLE -->
    <div class="card mb-4">
        <div class="card-header">
            Detalle de productos
        </div>
        <div class="card-body">

            <!-- Buscador de productos -->
            <div class="mb-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">🔍</span>
                    <input type="text"
                           id="buscadorProducto"
                           class="form-control"
                           placeholder="Buscar producto por nombre o código interno...">
                </div>
                <small class="text-muted">
                    Escriba y haga clic en el producto de la lista para agregarlo al detalle.
                </small>

                <!-- Resultados de búsqueda -->
                <div id="resultadosBusquedaProductos"
                     class="list-group list-group-flush mt-1"
                     style="max-height: 220px; overflow-y: auto;">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle" id="tabla-detalle">
                    <thead class="table-light">
                    <tr>
                        <th style="width: 40%;">Producto</th>
                        <th style="width: 10%;">Stock</th>
                        <th style="width: 15%;">Precio</th>
                        <th style="width: 15%;">Cantidad</th>
                        <th style="width: 15%;">Subtotal</th>
                        <th style="width: 10%;">Acción</th>
                    </tr>
                    </thead>
                    <tbody id="detalleTablaBody">
                    <!-- Filas dinámicas -->
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end mt-3">
                <div class="col-md-4">
                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <strong>S/ <span id="subtotal_general">0.00</span></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>IGV (18%):</span>
                        <strong>S/ <span id="igv_general">0.00</span></strong>
                    </div>
                    <div class="d-flex justify-content-between fs-5">
                        <span>Total:</span>
                        <strong>S/ <span id="total_general">0.00</span></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campos ocultos para enviar totales y vuelto al servidor -->
    <input type="hidden" name="total_general" id="total_general_input">
    <input type="hidden" name="igv_general" id="igv_general_input">
    <input type="hidden" name="subtotal_general" id="subtotal_general_input">
    <input type="hidden" name="vuelto" id="vuelto_input">

    <!-- BOTONES -->
    <div class="d-flex justify-content-between">
        <a href="panel_admin.php?modulo=ventas&accion=listar"
           class="btn btn-outline-secondary">
            Volver
        </a>
        <button type="submit" class="btn btn-primary">
            Guardar venta
        </button>
    </div>
</form>

<!-- Template para filas de detalle -->
<template id="filaDetalleTemplate">
    <tr>
        <td>
            <select name="id_producto[]" class="form-select form-select-sm producto-select" required>
                <option value="">Seleccione producto...</option>
                <?php foreach ($productos as $p): ?>
                    <?php
                    $codigo = $p["codigo_interno"] ?? "";
                    $textoProducto = trim(
                        ($codigo !== "" ? $codigo . " - " : "") .
                        $p["nombre_producto"]
                    );
                    ?>
                    <option value="<?php echo $p["id_producto"]; ?>"
                            data-stock="<?php echo $p["stock_actual"]; ?>"
                            data-precio="<?php echo number_format($p["precio_venta"], 2, '.', ''); ?>"
                            data-afecta-igv="<?php echo $p["afecta_igv"]; ?>">
                        <?php echo htmlspecialchars($textoProducto); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="texto-stock">0</td>
        <td>
            S/ <span class="texto-precio">0.00</span>
        </td>
        <td>
            <input type="number"
                   name="cantidad[]"
                   class="form-control form-control-sm input-cantidad"
                   min="1"
                   value="1">
        </td>
        <td>
            S/ <span class="texto-subtotal">0.00</span>
        </td>
        <td>
            <button type="button"
                    class="btn btn-sm btn-outline-danger btn-eliminar-fila">
                Quitar
            </button>
        </td>
    </tr>
</template>

<?php
// catálogos para JS
$clientesCatalogo = [];
foreach ($clientes as $c) {
    $clientesCatalogo[] = [
        'id' => (int)$c['id_cliente'],
        'numero_documento' => $c['numero_documento'],
    ];
}

$productosBusqueda = [];
foreach ($productos as $p) {
    $codigo = $p["codigo_interno"] ?? "";
    $texto = trim(
        ($codigo !== "" ? $codigo . " - " : "") .
        $p["nombre_producto"]
    );
    $productosBusqueda[] = [
        'id'    => (int)$p['id_producto'],
        'texto' => $texto,
    ];
}
?>

<script>
const clientesCatalogo = <?php echo json_encode($clientesCatalogo); ?>;
const productosBusqueda = <?php echo json_encode($productosBusqueda); ?>;

document.addEventListener('DOMContentLoaded', function () {
    /* ====== SERIE SEGÚN TIPO DE COMPROBANTE Y CLIENTE OPCIONAL ====== */
    const selectTipo    = document.getElementById('tipoComprobanteSelect');
    const inputSerie    = document.getElementById('serieComprobante');
    const inputNumero   = document.getElementById('numeroComprobante');
    const grupoCliente  = document.getElementById('grupoCliente');
    const selectCliente = document.getElementById('selectCliente');

    function actualizarSerieYClientePorTipo() {
    const opt = selectTipo.selectedOptions[0];
    if (!opt) {
        inputSerie.value  = "";
        inputNumero.value = "";            // ✅ dejamos vacío, sólo se ve el placeholder
        grupoCliente.style.display = '';
        selectCliente.required = true;
        return;
    }
    const texto = (opt.textContent || "").toUpperCase();

    if (texto.includes('FACTURA')) {
        inputSerie.value = 'F001';
    } else if (texto.includes('BOLETA')) {
        inputSerie.value = 'B001';
    } else {
        // Ticket u otro
        inputSerie.value = 'T001';
    }

    // el número SIEMPRE va vacío, el modelo genera el correlativo
    inputNumero.value = "";                // ✅ importante

    // Si es TICKET, ocultamos cliente
    if (texto.includes('TICKET')) {
        grupoCliente.style.display = 'none';
        selectCliente.required = false;
        selectCliente.value = '';
    } else {
        grupoCliente.style.display = '';
        selectCliente.required = true;
    }
}


    selectTipo.addEventListener('change', actualizarSerieYClientePorTipo);
    actualizarSerieYClientePorTipo();

    /* ====== TIPO DE PAGO: EFECTIVO MUESTRA MONTO Y VUELTO ====== */
    const selectPago         = document.getElementById('tipoPagoSelect');
    const grupoMontoEfectivo = document.getElementById('grupoMontoEfectivo');
    const montoRecibidoInput = document.getElementById('montoRecibidoInput');
    const spanVuelto         = document.getElementById('vuelto_general');
    const inputVueltoHidden  = document.getElementById('vuelto_input');

    function actualizarCamposPago() {
        if (!selectPago || !grupoMontoEfectivo) return;

        if (selectPago.value === 'EFECTIVO') {
            grupoMontoEfectivo.style.display = '';
        } else {
            grupoMontoEfectivo.style.display = 'none';
            if (montoRecibidoInput) montoRecibidoInput.value = '';
            if (spanVuelto) spanVuelto.textContent = '0.00';
            if (inputVueltoHidden) inputVueltoHidden.value = '0.00';
        }
    }

    function recalcularVuelto() {
        if (!selectPago || selectPago.value !== 'EFECTIVO') {
            if (spanVuelto) spanVuelto.textContent = '0.00';
            if (inputVueltoHidden) inputVueltoHidden.value = '0.00';
            return;
        }
        const spanTotal = document.getElementById('total_general');
        const total = parseFloat(spanTotal ? spanTotal.textContent : '0') || 0;
        const recibido = parseFloat(montoRecibidoInput.value || '0') || 0;
        const vuelto = recibido - total;
        const valor = vuelto > 0 ? vuelto.toFixed(2) : '0.00';
        if (spanVuelto) spanVuelto.textContent = valor;
        if (inputVueltoHidden) inputVueltoHidden.value = valor;
    }

    if (selectPago) {
        selectPago.addEventListener('change', function () {
            actualizarCamposPago();
            recalcularVuelto();
        });
        actualizarCamposPago();
    }

    if (montoRecibidoInput) {
        montoRecibidoInput.addEventListener('input', recalcularVuelto);
    }

    /* ====== BUSCADOR DE CLIENTE POR DNI/RUC (si existe en el HTML) ====== */
    const inputDocCliente  = document.getElementById('buscarDocumentoCliente');
    const btnBuscarCliente = document.getElementById('btnBuscarCliente');

    if (inputDocCliente && btnBuscarCliente) {
        btnBuscarCliente.addEventListener('click', function () {
            const doc = (inputDocCliente.value || '').trim();
            if (!doc) {
                alert('Ingrese un DNI o RUC para buscar.');
                return;
            }

            const encontrado = clientesCatalogo.find(c => c.numero_documento === doc);

            if (encontrado && selectCliente) {
                selectCliente.value = String(encontrado.id);
            } else {
                alert('No se encontró un cliente con ese documento.');
                if (selectCliente) selectCliente.value = '';
            }
        });
    }

    /* ====== DETALLE DE PRODUCTOS Y TOTALES ====== */
    const tbody        = document.getElementById('detalleTablaBody');
    const template     = document.getElementById('filaDetalleTemplate');
    const spanSubtotal = document.getElementById('subtotal_general');
    const spanIgv      = document.getElementById('igv_general');
    const spanTotal    = document.getElementById('total_general');

    const inputSubtotalHidden = document.getElementById('subtotal_general_input');
    const inputIgvHidden      = document.getElementById('igv_general_input');
    const inputTotalHidden    = document.getElementById('total_general_input');

    const buscadorProducto = document.getElementById('buscadorProducto');
    const contResultados   = document.getElementById('resultadosBusquedaProductos');

    function agregarProductoAlCarrito(idProd) {
        const clone = template.content.cloneNode(true);
        tbody.appendChild(clone);
        const nuevaFila = tbody.lastElementChild;
        const selectProd = nuevaFila.querySelector('.producto-select');
        if (selectProd) {
            selectProd.value = String(idProd);
            const evento = new Event('change', { bubbles: true });
            selectProd.dispatchEvent(evento);
        }
        recalcularTotales();
    }

    tbody.addEventListener('change', function (e) {
        if (e.target.classList.contains('producto-select')) {
            const fila = e.target.closest('tr');
            const opt = e.target.selectedOptions[0];
            if (!opt) return;
            const stock  = opt.getAttribute('data-stock') || "0";
            const precio = opt.getAttribute('data-precio') || "0";

            fila.querySelector('.texto-stock').textContent  = stock;
            fila.querySelector('.texto-precio').textContent = parseFloat(precio).toFixed(2);

            recalcularFila(fila);
            recalcularTotales();
        }
    });

    tbody.addEventListener('input', function (e) {
        if (e.target.classList.contains('input-cantidad')) {
            const fila = e.target.closest('tr');
            recalcularFila(fila);
            recalcularTotales();
        }
    });

    tbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-eliminar-fila')) {
            const fila = e.target.closest('tr');
            fila.remove();
            recalcularTotales();
        }
    });

    function recalcularFila(fila) {
        const precio = parseFloat(
            fila.querySelector('.texto-precio').textContent || "0"
        );
        const cantidad = parseFloat(
            fila.querySelector('.input-cantidad').value || "0"
        );
        const subtotal = precio * cantidad;
        fila.querySelector('.texto-subtotal').textContent = subtotal.toFixed(2);
    }

    function recalcularTotales() {
        let subtotal = 0;
        let subtotalGravado = 0;

        tbody.querySelectorAll('tr').forEach(function (fila) {
            const selectProd = fila.querySelector('.producto-select');
            if (!selectProd) return;

            const optProducto = selectProd.selectedOptions[0];
            if (!optProducto) return;

            const afectaIgv = parseInt(optProducto.getAttribute('data-afecta-igv') || "0");
            const sub = parseFloat(fila.querySelector('.texto-subtotal').textContent || "0");

            subtotal += sub;
            if (afectaIgv === 1) {
                subtotalGravado += sub;
            }
        });

        const igv   = subtotalGravado * 0.18;
        const total = subtotal + igv;

        spanSubtotal.textContent = subtotal.toFixed(2);
        spanIgv.textContent      = igv.toFixed(2);
        spanTotal.textContent    = total.toFixed(2);

        if (inputSubtotalHidden) inputSubtotalHidden.value = subtotal.toFixed(2);
        if (inputIgvHidden)      inputIgvHidden.value      = igv.toFixed(2);
        if (inputTotalHidden)    inputTotalHidden.value    = total.toFixed(2);

        // actualizar vuelto si es efectivo
        recalcularVuelto();
    }

    /* ====== BUSCADOR DE PRODUCTOS (SUGERENCIAS) ====== */
    function renderResultadosBusqueda(termino) {
        contResultados.innerHTML = '';
        termino = termino.toLowerCase();
        if (!termino) return;

        const coincidencias = productosBusqueda
            .filter(p => p.texto.toLowerCase().includes(termino))
            .slice(0, 10);

        coincidencias.forEach(p => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'list-group-item list-group-item-action';
            btn.textContent = p.texto;
            btn.dataset.productoId = p.id;
            contResultados.appendChild(btn);
        });
    }

    buscadorProducto.addEventListener('input', function () {
        const termino = this.value || '';
        renderResultadosBusqueda(termino);
    });

    contResultados.addEventListener('click', function (e) {
        const btn = e.target.closest('button[data-producto-id]');
        if (!btn) return;
        const idProd = btn.dataset.productoId;
        agregarProductoAlCarrito(idProd);
        buscadorProducto.value = '';
        contResultados.innerHTML = '';
    });
});
</script>
