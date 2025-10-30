// Ubicación: js/usuarios.js

$(document).ready(function() {
    // Inicializar DataTables (Esto le da el estilo a la tabla)
    $('#dataTableUsuarios').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        }
    });

    // --- 1. Abrir Modal para Nuevo Usuario ---
    $('#btn_nuevo_usuario').on('click', function() {
        $('#modalUsuarioLabel').text('Nuevo Usuario');
        $('#accion').val('registrar');
        $('#id_usuario').val('');
        $('#formUsuario')[0].reset(); 
        $('#password').prop('required', true); 
        $('#passHelpText').text('');
    });

    // --- 2. Registrar/Editar Usuario (Envío del Formulario) ---
    $('#formUsuario').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: '../controladores/usuario_controlador.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.exito) {
                    alert(response.mensaje);
                    $('#modalUsuario').modal('hide');
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

    // --- 3. Abrir Modal para Editar Usuario ---
    $('#dataTableUsuarios').on('click', '.btn-editar', function() {
        var id_usuario = $(this).data('id');
        
        $('#modalUsuarioLabel').text('Editar Usuario');
        $('#accion').val('editar');
        $('#id_usuario').val(id_usuario);
        $('#password').prop('required', false);
        $('#passHelpText').text('Dejar en blanco para mantener la contraseña actual.');

        // Obtener datos del usuario por AJAX
        $.ajax({
            type: 'GET',
            url: '../controladores/usuario_controlador.php',
            data: { accion: 'obtener_por_id', id_usuario: id_usuario },
            dataType: 'json',
            success: function(response) {
                if (response.exito) {
                    var datos = response.datos;
                    $('#nombre_completo').val(datos.nombre_completo);
                    $('#email').val(datos.email);
                    $('#dni').val(datos.dni);
                    $('#telefono').val(datos.telefono);
                    $('#id_rol').val(datos.id_rol); 
                    $('#modalUsuario').modal('show');
                } else {
                    alert(response.mensaje);
                }
            }
        });
    });

    // --- 4. Eliminar Usuario (Eliminación Lógica) ---
    $('#dataTableUsuarios').on('click', '.btn-eliminar', function() {
        var id_usuario = $(this).data('id');

        if (confirm('¿Está seguro de que desea eliminar este usuario?')) {
            $.ajax({
                type: 'POST',
                url: '../controladores/usuario_controlador.php',
                data: { accion: 'eliminar', id_usuario: id_usuario },
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