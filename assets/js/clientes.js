// Ubicación: js/clientes.js (FINAL CON ESTADO)

$(document).ready(function() {
    
    // Obtener la instancia de la Modal de BS5
    const modalElement = document.getElementById('modalCliente');
    const modalCliente = new bootstrap.Modal(modalElement); 

    // Inicializar DataTables
    if ($.fn.DataTable) {
        $('#dataTableClientes').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });
    }

    // --- 1. Abrir Modal para Nuevo Cliente ---
    $('#btn_nuevo_cliente').on('click', function() {
        $('#modalClienteLabel').text('Registrar Nuevo Cliente');
        $('#accion').val('registrar');
        $('#id_cliente').val('');
        $('#formCliente')[0].reset(); 
    });

    // --- 2. Registrar/Editar Cliente (Envío del Formulario) ---
    $('#formCliente').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            type: 'POST',
            url: '../controladores/cliente_controlador.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.exito) {
                    Swal.fire('¡Éxito!', response.mensaje, 'success'); 
                    modalCliente.hide();
                    window.location.reload(); 
                } else {
                    Swal.fire('Error', 'Error: ' + response.mensaje, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
            }
        });
    });

    // --- 3. Abrir Modal para Editar Cliente (Carga de Datos incluyendo Estado) ---
    modalElement.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; 
        
        if (button && $(button).hasClass('btn-editar')) {
            var id_cliente = $(button).data('id');
            
            $('#modalClienteLabel').text('Editar Cliente');
            $('#accion').val('editar');
            $('#id_cliente').val(id_cliente);

            // Obtener datos del cliente por AJAX
            $.ajax({
                type: 'GET',
                url: '../controladores/cliente_controlador.php',
                data: { accion: 'obtener_por_id', id_cliente: id_cliente },
                dataType: 'json',
                success: function(response) {
                    if (response.exito) {
                        var datos = response.datos;
                        
                        // Llenar formulario con los nombres exactos de tu modelo/controlador
                        $('#nombre_completo').val(datos.nombre_completo); 
                        $('#documento_tipo').val(datos.documento_tipo);   
                        $('#documento_numero').val(datos.documento_numero); 
                        $('#telefono').val(datos.telefono);
                        $('#email').val(datos.email);
                        $('#direccion').val(datos.direccion);
                        
                        // 🌟 CORRECCIÓN CRÍTICA: CARGA DEL ESTADO 🌟
                        // Mapeo inverso de BD (1/0) a Formulario (A/I)
                        var estado_form = (datos.estado == 1) ? 'A' : 'I';
                        $('#estado').val(estado_form);
                        
                    } else {
                        modalCliente.hide();
                        Swal.fire('Error', response.mensaje, 'error');
                    }
                },
                 error: function() {
                    modalCliente.hide();
                    Swal.fire('Error', 'Error al intentar cargar los datos del cliente. Revise la ruta AJAX.', 'error');
                }
            });
        } else if (button && $(button).attr('id') === 'btn_nuevo_cliente') {
             // Si es el botón de "Nuevo Cliente", reinicia el formulario
             $('#formCliente')[0].reset(); 
        }
    });

    // --- 4. Eliminar Cliente (Eliminación Lógica) ---
    $('#dataTableClientes').on('click', '.btn-eliminar', function() {
        var id_cliente = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "El cliente será deshabilitado (eliminación lógica).",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, ¡deshabilitar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '../controladores/cliente_controlador.php',
                    data: { accion: 'eliminar', id_cliente: id_cliente },
                    dataType: 'json',
                    success: function(response) {
                        if (response.exito) {
                            Swal.fire('¡Deshabilitado!', response.mensaje, 'success'); 
                            window.location.reload(); 
                        } else {
                            Swal.fire('Error', 'Error: ' + response.mensaje, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error de comunicación al eliminar cliente.', 'error');
                    }
                });
            }
        });
    });
});