// Ubicación: js/clientes.js

$(document).ready(function() {
    // Inicializar DataTables para la tabla de clientes
    $('#dataTableClientes').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        }
    });

    // --- 1. Abrir Modal para Nuevo Cliente ---
    $('#btn_nuevo_cliente').on('click', function() {
        $('#modalClienteLabel').text('Registrar Nuevo Cliente');
        $('#accion').val('registrar');
        $('#id_cliente').val('');
        $('#formCliente')[0].reset(); 
        $('#modalCliente').modal('show');
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
                    alert(response.mensaje);
                    $('#modalCliente').modal('hide');
                    window.location.reload(); 
                } else {
                    alert('Error: ' + response.mensaje);
                }
            },
            error: function() {
                alert('Error de comunicación con el servidor.');
            }
        });
    });

    // --- 3. Abrir Modal para Editar Cliente ---
    $('#dataTableClientes').on('click', '.btn-editar', function() {
        var id_cliente = $(this).data('id');
        
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
                    $('#nombre_completo').val(datos.nombre_completo);
                    $('#documento_tipo').val(datos.documento_tipo); // CORREGIDO
                    $('#documento_numero').val(datos.documento_numero); // CORREGIDO
                    $('#telefono').val(datos.telefono);
                    $('#email').val(datos.email);
                    $('#direccion').val(datos.direccion);
                    $('#modalCliente').modal('show');
                } else {
                    alert(response.mensaje);
                }
            }
        });
    });

    // --- 4. Eliminar Cliente (Eliminación Lógica) ---
    $('#dataTableClientes').on('click', '.btn-eliminar', function() {
        var id_cliente = $(this).data('id');

        if (confirm('¿Está seguro de que desea eliminar este cliente?')) {
            $.ajax({
                type: 'POST',
                url: '../controladores/cliente_controlador.php',
                data: { accion: 'eliminar', id_cliente: id_cliente },
                dataType: 'json',
                success: function(response) {
                    if (response.exito) {
                        alert(response.mensaje);
                        window.location.reload(); 
                    } else {
                        alert('Error: ' + response.mensaje);
                    }
                }
            });
        }
    });
});