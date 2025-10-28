// Variable global para el carrito
let carrito = [];
// Variable global para el ID del cliente seleccionado
let clienteSeleccionadoId = null;

// --- FUNCIÓN AUXILIAR PARA VALIDAR DNI/RUC ---
function actualizarValidadorDocumento() {
    let tipoComprobante = $("#tipo_comprobante").val(); 
    let inputNum = $("#documento_numero");
    let inputTipoTexto = $("#documento_tipo_texto"); 
    let inputTipoOculto = $("#documento_tipo"); 

    if (tipoComprobante === "Boleta") {
        inputTipoTexto.text("DNI");
        inputTipoOculto.val("DNI");
        inputNum.attr("placeholder", "Buscar DNI (8 dígitos)");
        inputNum.attr("maxlength", "8");
        inputNum.attr("pattern", "[0-9]{8}");
    } else if (tipoComprobante === "Factura") {
        inputTipoTexto.text("RUC");
        inputTipoOculto.val("RUC");
        inputNum.attr("placeholder", "Buscar RUC (11 dígitos)");
        inputNum.attr("maxlength", "11");
        inputNum.attr("pattern", "[0-9]{11}");
    }
    inputNum.val("");
}

// Ejecutar todo cuando el documento esté listo
$(document).ready(function() {

    // --- 1. CONFIGURACIÓN DEL AUTOCOMPLETAR PRODUCTOS ---
    $("#buscar_producto").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "../controladores/buscar_producto.php",
                type: "GET",
                dataType: "json",
                data: { term: request.term },
                success: function(data) { response(data); }
            });
        },
        minLength: 2, 
        select: function(event, ui) {
            agregarAlCarrito(ui.item);
            $(this).val("");
            return false; 
        }
    });

    // --- 2. LÓGICA DEL CARRITO ---
    
    function agregarAlCarrito(producto) {
        let productoExistente = carrito.find(item => item.id === producto.id_producto);
        if (productoExistente) {
            if (productoExistente.cantidad < producto.stock) {
                productoExistente.cantidad++;
            } else {
                Swal.fire('Stock Límite', 'No hay más stock disponible.', 'warning');
            }
        } else {
            carrito.push({
                id: producto.id_producto,
                nombre: producto.nombre,
                precio: producto.precio_venta,
                stock: producto.stock,
                cantidad: 1
            });
        }
        actualizarVistaCarrito();
    }

    function actualizarVistaCarrito() {
        const tablaBody = $("#tabla_carrito");
        tablaBody.empty(); 
        if (carrito.length === 0) {
            tablaBody.html('<tr id="fila_vacia"><td colspan="5" class="text-center">El carrito está vacío</td></tr>');
        } else {
            carrito.forEach(producto => {
                let subtotalProducto = (producto.cantidad * producto.precio).toFixed(2);
                let fila = `
                    <tr>
                        <td>${producto.nombre}</td>
                        <td>S/ ${producto.precio}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm" 
                                   style="width: 70px;" value="${producto.cantidad}" 
                                   min="1" max="${producto.stock}" data-id="${producto.id}">
                        </td>
                        <td>S/ ${subtotalProducto}</td>
                        <td>
                            <button class="btn btn-danger btn-sm btn-eliminar-item" data-id="${producto.id}">X</button>
                        </td>
                    </tr>
                `;
                tablaBody.append(fila);
            });
        }
        actualizarTotales();
        $("#carrito_data").val(JSON.stringify(carrito));
    }

    $("#tabla_carrito").on("change", "input[type='number']", function() {
        let idProducto = $(this).data("id");
        let nuevaCantidad = parseInt($(this).val());
        let producto = carrito.find(item => item.id === idProducto);
        
        if (nuevaCantidad > 0 && nuevaCantidad <= producto.stock) {
            producto.cantidad = nuevaCantidad;
        } else if (nuevaCantidad > producto.stock) {
            $(this).val(producto.stock); 
            producto.cantidad = producto.stock;
            Swal.fire('Stock Límite', 'La cantidad no puede superar el stock.', 'warning');
        } else {
            $(this).val(1); 
            producto.cantidad = 1;
        }
        actualizarVistaCarrito();
    });

    $("#tabla_carrito").on("click", ".btn-eliminar-item", function() {
        let idProducto = $(this).data("id");
        carrito = carrito.filter(item => item.id !== idProducto);
        actualizarVistaCarrito();
    });

    // --- 3. LÓGICA DE CÁLCULO DE TOTALES (IGV) ---
    
    function actualizarTotales() {
        let total = 0;
        carrito.forEach(producto => { total += producto.cantidad * producto.precio; });
        
        let subtotal = (total / 1.18).toFixed(2);
        let igv = (total - subtotal).toFixed(2);
        total = total.toFixed(2);
        
        $("#total_pagar").text(`S/ ${total}`);
        $("#texto_subtotal").text(`S/ ${subtotal}`);
        $("#texto_igv").text(`S/ ${igv}`);
        
        $("#input_total").val(total);
        $("#input_subtotal").val(subtotal);
        $("#input_igv").val(igv);
    }

    // --- 4. LÓGICA DE DATOS DEL CLIENTE (DNI/RUC) ---
    
    // Al cargar la página, ajustar el validador
    actualizarValidadorDocumento();

    // Actualizar validador si cambia el tipo de comprobante
    $("#tipo_comprobante").on("change", function() {
        let tipo = $(this).val();
        if (tipo === "Boleta" || tipo === "Factura") {
            $("#campos_cliente").show();
        } else {
            $("#campos_cliente").hide();
            limpiarCamposCliente();
        }
        actualizarValidadorDocumento();
    });

    // Botón Buscar Cliente
    $("#btn_buscar_cliente").on("click", function() {
        let tipo_doc = $("#documento_tipo").val(); 
        let num_doc = $("#documento_numero").val().trim();
        
        if (num_doc === "") {
            Swal.fire('Campo Vacío', 'Ingrese un número de documento.', 'warning');
            return;
        }
        if (tipo_doc === "DNI" && num_doc.length !== 8) {
            Swal.fire('DNI Inválido', 'El DNI debe tener 8 dígitos.', 'warning');
            return;
        }
        if (tipo_doc === "RUC" && num_doc.length !== 11) {
            Swal.fire('RUC Inválido', 'El RUC debe tener 11 dígitos.', 'warning');
            return;
        }

        $.ajax({
            url: "../controladores/buscar_cliente.php",
            type: "GET",
            dataType: "json",
            data: { tipo_doc: tipo_doc, num_doc: num_doc },
            success: function(data) {
                if (data.encontrado) {
                    $("#cliente_nombre").val(data.cliente.nombre_completo);
                    $("#id_cliente_oculto").val(data.cliente.id_cliente);
                    $("#btn_registrar_cliente").hide();
                } else {
                    $("#cliente_nombre").val("Cliente no encontrado");
                    $("#id_cliente_oculto").val("");
                    $("#btn_registrar_cliente").show();
                }
            }
        });
    });

    function limpiarCamposCliente() {
        $("#documento_numero").val("");
        $("#cliente_nombre").val("");
        $("#id_cliente_oculto").val("");
        $("#btn_registrar_cliente").hide();
        clienteSeleccionadoId = null;
    }

    // --- 5. LÓGICA DEL MODAL (POP-UP) DE REGISTRAR CLIENTE ---
    
    // Abrir el modal
    $("#btn_registrar_cliente").on("click", function() {
        let tipo_doc = $("#documento_tipo").val(); 
        let num_doc = $("#documento_numero").val();
        
        $("#form_registrar_cliente")[0].reset();
        $("#modal_tipo_doc_texto").val(tipo_doc);
        $("#modal_tipo_doc").val(tipo_doc);
        $("#modal_num_doc").val(num_doc);
        
        var modal = new bootstrap.Modal(document.getElementById('modalRegistrarCliente'));
        modal.show();
    });

    // Enviar el formulario del modal (con AJAX)
    $("#form_registrar_cliente").on("submit", function(e) {
        e.preventDefault(); 
        let tipo_doc_modal = $("#modal_tipo_doc").val();
        let num_doc_modal = $("#modal_num_doc").val();
        
        if (tipo_doc_modal === "DNI" && num_doc_modal.length !== 8) {
            Swal.fire('DNI Inválido', 'El DNI debe tener 8 dígitos.', 'error');
            return; 
        }
        if (tipo_doc_modal === "RUC" && num_doc_modal.length !== 11) {
            Swal.fire('RUC Inválido', 'El RUC debe tener 11 dígitos.', 'error');
            return; 
        }

        $.ajax({
            url: "../controladores/registrar_cliente_modal.php", 
            type: "POST",
            dataType: "json",
            data: $(this).serialize(), 
            success: function(data) {
                if (data.exito) {
                    $("#cliente_nombre").val(data.nombre);
                    $("#id_cliente_oculto").val(data.id_cliente);
                    $("#btn_registrar_cliente").hide();
                    
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalRegistrarCliente'));
                    modal.hide();
                    Swal.fire('Éxito', 'Cliente registrado correctamente.', 'success');
                } else {
                    Swal.fire('Error', data.mensaje || 'No se pudo registrar al cliente.', 'error');
                }
            }
        });
    });

    // --- 6. LÓGICA DE PAGO (EFECTIVO / TARJETA) ---
    // ¡¡ESTA SECCIÓN HA SIDO COMPLETAMENTE MODIFICADA!!

    // Función Central para Registrar la Venta (vía AJAX)
    function registrarVentaAJAX() {
        // Recoger los datos del formulario principal
        let formData = $("#form_venta").serialize(); 

        $.ajax({
            url: "../controladores/registrar_venta.php",
            type: "POST",
            dataType: "json",
            data: formData,
            success: function(data) {
                if (data.exito) {
                    // ¡ÉXITO!
                    // 1. Abrir PDF en pestaña nueva
                    window.open('../vistas/generar_comprobante.php?id=' + data.id_venta, '_blank');
                    
                    // 2. Mostrar alerta de éxito en la página actual
                    Swal.fire({
                        title: '¡Venta Registrada!',
                        text: data.mensaje,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // 3. Limpiar todo para la siguiente venta
                    carrito = [];
                    limpiarCamposCliente();
                    $("#tipo_comprobante").val("Nota de Venta").trigger("change");
                    actualizarVistaCarrito();
                    
                } else {
                    // ¡ERROR!
                    Swal.fire('Error al Registrar', data.mensaje, 'error');
                }
            },
            error: function() {
                Swal.fire('Error de Conexión', 'No se pudo comunicar con el servidor.', 'error');
            }
        });
    }

    // Botón Pagar con Tarjeta
    $("#btn_pago_tarjeta").on("click", function() {
        if (carrito.length === 0) {
            Swal.fire('Carrito Vacío', 'Debe agregar productos al carrito.', 'warning');
            return;
        }
        
        Swal.fire({
            title: 'Pago con Tarjeta',
            text: '¿Confirmar venta con tarjeta?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Asignar valores y llamar a la función AJAX
                $("#input_metodo_pago").val("Tarjeta");
                $("#input_monto_recibido").val("");
                $("#input_vuelto").val("");
                registrarVentaAJAX(); // ¡CAMBIO!
            }
        });
    });

    // Botón Pagar con Efectivo
    $("#btn_pago_efectivo").on("click", function() {
        if (carrito.length === 0) {
            Swal.fire('Carrito Vacío', 'Debe agregar productos al carrito.', 'warning');
            return;
        }
        
        let total = $("#input_total").val();
        $("#modal_total_pagar").text(`S/ ${total}`);
        $("#modal_monto_recibido").val("");
        $("#modal_vuelto").text("S/ 0.00");
        $("#btn_confirmar_pago_efectivo").prop("disabled", true);
        
        var modal = new bootstrap.Modal(document.getElementById('modalPagoEfectivo'));
        modal.show();
    });

    // Calcular el vuelto en tiempo real
    $("#modal_monto_recibido").on("input", function() {
        let total = parseFloat($("#input_total").val());
        let recibido = parseFloat($(this).val()) || 0;
        
        if (recibido >= total) {
            let vuelto = (recibido - total).toFixed(2);
            $("#modal_vuelto").text(`S/ ${vuelto}`);
            $("#btn_confirmar_pago_efectivo").prop("disabled", false);
        } else {
            $("#modal_vuelto").text("S/ 0.00");
            $("#btn_confirmar_pago_efectivo").prop("disabled", true); 
        }
    });

    // Botón final de Confirmar Venta (Efectivo)
    $("#btn_confirmar_pago_efectivo").on("click", function() {
        // Asignar valores
        $("#input_metodo_pago").val("Efectivo");
        $("#input_monto_recibido").val($("#modal_monto_recibido").val());
        let vueltoText = $("#modal_vuelto").text().replace("S/ ", "");
        $("#input_vuelto").val(vueltoText);
        
        // Cerrar el modal y llamar a la función AJAX
        var modal = bootstrap.Modal.getInstance(document.getElementById('modalPagoEfectivo'));
        modal.hide();
        registrarVentaAJAX(); // ¡CAMBIO!
    });
    
    // --- 7. CANCELAR VENTA ---
    $("#btn_cancelar_venta").on("click", function() {
        Swal.fire({
            title: '¿Cancelar Venta?',
            text: "Se limpiará todo el carrito y los datos del cliente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                carrito = [];
                limpiarCamposCliente();
                $("#tipo_comprobante").val("Nota de Venta").trigger("change");
                actualizarVistaCarrito();
            }
        });
    });
});