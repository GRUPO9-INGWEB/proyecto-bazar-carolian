// Variable global para el carrito
let carrito = [];
let clienteSeleccionadoId = null; 
const IGV_RATE = 0.18; // 18%

// --- FUNCIÓN LIMPIAR CAMPOS CLIENTE ---
function limpiarCamposCliente() {
    $("#documento_numero").val("");
    $("#cliente_nombre").val("");
    $("#id_cliente_oculto").val("");
    $("#btn_registrar_cliente").hide();
    $("#observaciones").val(""); 
    clienteSeleccionadoId = null;
    if($("#tipo_comprobante").val() === "Nota de Venta") {
        $("#campos_cliente").hide();
    }
}

// --- FUNCIÓN ACTUALIZAR TOTALES ---
function actualizarTotales() {
    let subtotal = 0;
    carrito.forEach(item => {
        subtotal += item.precio * item.cantidad;
    });

    let igv = subtotal * IGV_RATE;
    let total = subtotal + igv;

    // Actualizar campos ocultos del formulario
    $("#input_subtotal").val(subtotal.toFixed(2));
    $("#input_igv").val(igv.toFixed(2));
    $("#input_total").val(total.toFixed(2));

    // Actualizar vista
    $("#valor_subtotal").text(`S/ ${subtotal.toFixed(2)}`);
    $("#valor_igv").text(`S/ ${igv.toFixed(2)}`);
    $("#total_pagar").text(`S/ ${total.toFixed(2)}`);
    $("#carrito_data").val(JSON.stringify(carrito));
}

// --- FUNCIÓN ACTUALIZAR VISTA CARRITO ---
function actualizarVistaCarrito() {
    const tbody = $("#carrito_tbody");
    tbody.empty();

    if (carrito.length === 0) {
        tbody.append('<tr><td colspan="5" class="text-center">El carrito está vacío.</td></tr>');
    } else {
        carrito.forEach((item, index) => {
            const importe = item.precio * item.cantidad;
            const row = `
                <tr>
                    <td>${item.nombre}</td>
                    <td>S/ ${item.precio.toFixed(2)}</td>
                    <td><input type="number" class="form-control form-control-sm carrito-cantidad" data-id="${item.id}" value="${item.cantidad}" min="1" style="width: 70px;"></td>
                    <td>S/ ${importe.toFixed(2)}</td>
                    <td><button class="btn btn-danger btn-sm btn-remover-item" data-id="${item.id}"><i class="fas fa-times"></i></button></td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    actualizarTotales();
}

// --- FUNCIÓN REGISTRAR VENTA (Con llamada a PDF) ---
function registrarVentaAJAX() {
    let formData = $("#form_venta").serialize(); 

    $.ajax({
        url: "../controladores/registrar_venta.php",
        type: "POST", dataType: "json", data: formData,
        success: function(data) {
            if (data.exito) {
                const modalPago = bootstrap.Modal.getInstance(document.getElementById('modalPagoEfectivo'));
                if(modalPago) modalPago.hide();

                // ¡LLAMADA AL PDF!
                window.open('../vistas/generar_comprobante.php?id=' + data.id_venta, '_blank'); 
                
                Swal.fire({
                    title: '¡Venta Registrada!',
                    text: data.mensaje,
                    icon: 'success', timer: 2000, showConfirmButton: false
                });
                
                // Limpiar
                carrito = [];
                limpiarCamposCliente();
                $("#tipo_comprobante").val("Nota de Venta").trigger("change");
                actualizarVistaCarrito();
                
            } else {
                Swal.fire('Error al Registrar', data.mensaje, 'error');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            Swal.fire('Error de Conexión', 'No se pudo comunicar con el servidor.', 'error');
        }
    });
}


$(document).ready(function() {

    // --- INICIALIZACIÓN ---
    limpiarCamposCliente();
    actualizarVistaCarrito();
    $("#tipo_comprobante").trigger("change"); 

    // --- EVENTOS DEL CLIENTE ---

    $("#tipo_comprobante").on("change", function() {
        let tipo = $(this).val();
        let camposCliente = $("#campos_cliente");
        let inputNum = $("#documento_numero");
        let inputTipoTexto = $("#documento_tipo_texto"); 
        let inputTipoOculto = $("#documento_tipo");

        limpiarCamposCliente(); 

        if (tipo === "Nota de Venta") {
            camposCliente.hide();
        } else {
            camposCliente.show();
            if (tipo === "Boleta") {
                inputTipoTexto.text("DNI");
                inputTipoOculto.val("DNI");
                inputNum.attr("maxlength", "8").attr("placeholder", "Buscar DNI (8 dígitos)...");
            } else if (tipo === "Factura") {
                inputTipoTexto.text("RUC");
                inputTipoOculto.val("RUC");
                inputNum.attr("maxlength", "11").attr("placeholder", "Buscar RUC (11 dígitos)...");
            }
        }
    });
    
    // Búsqueda AJAX de Cliente
    $("#btn_buscar_cliente").on("click", function() {
        let documento_numero = $("#documento_numero").val();
        let documento_tipo = $("#documento_tipo").val();
        
        if (documento_numero.length < 8) {
            Swal.fire('Validación', 'Ingrese un número de documento válido.', 'warning');
            return;
        }

        $.ajax({
            // ¡RUTA CRÍTICA!
            url: "../controladores/buscar_cliente.php",
            type: "GET", 
            dataType: "json", 
            data: { numero: documento_numero, tipo: documento_tipo },
            success: function(data) {
                if (data.id_cliente) {
                    $("#cliente_nombre").val(data.nombre_completo);
                    $("#id_cliente_oculto").val(data.id_cliente);
                    $("#btn_registrar_cliente").hide();
                    clienteSeleccionadoId = data.id_cliente;
                    Swal.fire('Éxito', 'Cliente encontrado.', 'success');
                } else {
                    $("#cliente_nombre").val("Cliente no encontrado");
                    $("#id_cliente_oculto").val("");
                    $("#btn_registrar_cliente").show();
                    clienteSeleccionadoId = null;
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error', 'Error al buscar el cliente. (Revisa la ruta: "../controladores/buscar_cliente.php")', 'error');
            }
        });
    });

    // --- LÓGICA DE MODALES ---
    
    // 1. Modal Registrar Cliente
    $("#btn_registrar_cliente").on("click", function() {
        let tipo_doc = $("#documento_tipo").val(); 
        let num_doc = $("#documento_numero").val();
        
        $("#form_registrar_cliente")[0].reset();
        $("#modal_tipo_doc_texto").val(tipo_doc);
        $("#modal_tipo_doc").val(tipo_doc);
        $("#modal_num_doc").val(num_doc);
        
        const modalCliente = new bootstrap.Modal(document.getElementById('modalRegistrarCliente'));
        modalCliente.show();
    });

    // Subir cliente (Desde el modal)
    $("#form_registrar_cliente").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        
        $.ajax({
            url: "../controladores/registrar_cliente_modal.php",
            type: "POST", data: formData, dataType: "json",
            success: function(data) {
                if (data.exito) {
                    const modalCliente = bootstrap.Modal.getInstance(document.getElementById('modalRegistrarCliente'));
                    modalCliente.hide();
                    
                    $("#documento_numero").val(data.documento_numero);
                    $("#cliente_nombre").val(data.nombre_completo);
                    $("#id_cliente_oculto").val(data.id_cliente);
                    $("#btn_registrar_cliente").hide();
                    clienteSeleccionadoId = data.id_cliente;

                    Swal.fire('Éxito', 'Cliente registrado y seleccionado.', 'success');
                } else {
                    Swal.fire('Error', data.mensaje, 'error');
                }
            }
        });
    });


    // 2. Modal Pago Efectivo
    $("#btn_pago_efectivo").on("click", function() {
        if (carrito.length === 0) {
            Swal.fire('Carrito Vacío', 'Debe agregar productos al carrito.', 'warning');
            return;
        }
        
        let total = parseFloat($("#input_total").val());

        $("#modal_total_pagar").text(`S/ ${total.toFixed(2)}`);
        $("#modal_monto_recibido").val(total.toFixed(2)).focus(); 
        $("#modal_vuelto").text("S/ 0.00");
        $("#btn_confirmar_pago_efectivo").prop("disabled", false); 
        $("#input_vuelto").val(0);
        $("#input_monto_recibido").val(total.toFixed(2));
        
        const modalPago = new bootstrap.Modal(document.getElementById('modalPagoEfectivo'));
        modalPago.show();
    });
    
    // Calcular Vuelto
    $("#modal_monto_recibido").on("input", function() {
        let recibido = parseFloat($(this).val()) || 0;
        let total = parseFloat($("#input_total").val());
        let vuelto = 0;

        if (recibido >= total) {
            vuelto = recibido - total;
            $("#modal_vuelto").text(`S/ ${vuelto.toFixed(2)}`).removeClass('text-danger').addClass('text-primary');
            $("#btn_confirmar_pago_efectivo").prop("disabled", false);
            
            $("#input_monto_recibido").val(recibido.toFixed(2));
            $("#input_vuelto").val(vuelto.toFixed(2));
            
        } else {
            vuelto = total - recibido;
            $("#modal_vuelto").text(`Falta S/ ${vuelto.toFixed(2)}`).removeClass('text-primary').addClass('text-danger');
            $("#btn_confirmar_pago_efectivo").prop("disabled", true);
            $("#input_monto_recibido").val(0);
            $("#input_vuelto").val(0);
        }
    });

    // Confirmar Pago
    $("#btn_confirmar_pago_efectivo").on("click", function() {
        $("#input_metodo_pago").val('Efectivo');
        registrarVentaAJAX();
    });

    // Pago con Tarjeta
    $("#btn_pago_tarjeta").on("click", function() {
        if (carrito.length === 0) {
            Swal.fire('Carrito Vacío', 'Debe agregar productos al carrito.', 'warning');
            return;
        }
        
        $("#input_metodo_pago").val('Tarjeta');
        $("#input_monto_recibido").val($("#input_total").val());
        $("#input_vuelto").val(0);
        
        Swal.fire({
            title: 'Pago con Tarjeta',
            text: `Confirmar pago de S/ ${$("#input_total").val()}`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Sí, confirmar!'
        }).then((result) => {
            if (result.isConfirmed) {
                registrarVentaAJAX();
            }
        });
    });

    // Cancelar Venta
    $("#btn_cancelar_venta").on("click", function() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminarán todos los productos del carrito.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar venta!'
        }).then((result) => {
            if (result.isConfirmed) {
                carrito = [];
                limpiarCamposCliente();
                $("#tipo_comprobante").val("Nota de Venta").trigger("change");
                actualizarVistaCarrito();
                Swal.fire('Cancelada', 'Venta cancelada.', 'info');
            }
        });
    });
    
    // --- LÓGICA DE PRODUCTOS Y CARRITO ---

    // Autocomplete de Productos
    $("#buscador_producto").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "../controladores/buscar_producto.php",
                type: "GET",
                dataType: "json",
                data: { term: request.term },
                success: function(data) {
                    response(data.map(item => ({
                        label: `${item.nombre} (Cod: ${item.codigo}) - S/ ${item.precio_venta}`,
                        value: item.nombre,
                        id: item.id_producto,
                        nombre: item.nombre,
                        precio: parseFloat(item.precio_venta),
                        stock: parseInt(item.stock)
                    })));
                }
            });
        },
        select: function(event, ui) {
            event.preventDefault();
            const producto = ui.item;
            
            const itemIndex = carrito.findIndex(item => item.id === producto.id);
            
            if (itemIndex > -1) {
                const nuevaCantidad = carrito[itemIndex].cantidad + 1;
                
                if (nuevaCantidad > producto.stock) {
                     Swal.fire('Stock Insuficiente', `Solo hay ${producto.stock} unidades de ${producto.nombre}.`, 'warning');
                     return;
                }
                carrito[itemIndex].cantidad = nuevaCantidad;
            } else {
                if (producto.stock < 1) {
                    Swal.fire('Stock Agotado', `${producto.nombre} no tiene stock disponible.`, 'warning');
                    return;
                }
                carrito.push({
                    id: producto.id,
                    nombre: producto.nombre,
                    precio: producto.precio,
                    cantidad: 1,
                    stock: producto.stock 
                });
            }

            $("#buscador_producto").val("");
            actualizarVistaCarrito();
        }
    });

    // Remover item del carrito
    $("#carrito_tbody").on("click", ".btn-remover-item", function() {
        const id = $(this).data('id');
        carrito = carrito.filter(item => item.id !== id);
        actualizarVistaCarrito();
    });

    // Cambiar cantidad en el carrito
    $("#carrito_tbody").on("input", ".carrito-cantidad", function() {
        const id = $(this).data('id');
        let nuevaCantidad = parseInt($(this).val());
        const itemIndex = carrito.findIndex(item => item.id === id);
        
        if (itemIndex > -1) {
            const maxStock = carrito[itemIndex].stock; 
            
            if (isNaN(nuevaCantidad) || nuevaCantidad < 1) {
                nuevaCantidad = 1;
                $(this).val(1);
            }
            
            if (nuevaCantidad > maxStock) {
                Swal.fire('Stock Insuficiente', `Solo hay ${maxStock} unidades de ${carrito[itemIndex].nombre}.`, 'warning');
                nuevaCantidad = maxStock;
                $(this).val(maxStock);
            }
            
            carrito[itemIndex].cantidad = nuevaCantidad;
            actualizarVistaCarrito();
        }
    });
    
});