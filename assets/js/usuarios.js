// Ubicación: assets/js/usuarios.js (VERSIÓN FINAL COMPLETA CON ESTADO)

$(document).ready(function() {
    
    // Obtener la instancia de la Modal de BS5
    const modalElement = document.getElementById('modalUsuario');
    const modalUsuario = new bootstrap.Modal(modalElement);

    // Inicializar DataTables
    if ($.fn.DataTable) {
        $('#dataTableUsuarios').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });
    }

    // --- 1. Abrir Modal para Nuevo Usuario ---
    $('#btn_nuevo_usuario').on('click', function() {
        $('#modalUsuarioLabel').text('Registrar Nuevo Usuario');
        $('#accion').val('registrar');
        $('#id_usuario').val('');
        $('#formUsuario')[0].reset(); 
        $('#password').attr('required', true).val(''); 
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
                    Swal.fire('¡Éxito!', response.mensaje, 'success');
                    modalUsuario.hide(); 
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

    // --- 3. Abrir Modal para Editar Usuario (Carga de Datos y Estado) ---
    modalElement.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; 
        
        if (button && $(button).hasClass('btn-editar')) {
            var id_usuario = $(button).data('id');
            
            // 1. Configuración de Edición
            $('#modalUsuarioLabel').text('Editar Usuario'); 
            $('#accion').val('editar');
            $('#id_usuario').val(id_usuario);
            $('#password').removeAttr('required').val(''); 

            // 2. AJAX para cargar datos
            $.ajax({
                type: 'GET',
                url: '../controladores/usuario_controlador.php',
                data: { accion: 'obtener_por_id', id_usuario: id_usuario },
                dataType: 'json',
                success: function(response) {
                    if (response.exito) {
                        var datos = response.datos;
                        
                        // 3. ASIGNACIÓN DE DATOS CON ESTADO
                        $('#nombre_completo').val(datos.nombre_completo); 
                        $('#email').val(datos.email);     
                        $('#id_rol').val(datos.id_rol);   
                        $('#dni').val(datos.dni);
                        $('#telefono').val(datos.telefono);
                        $('#estado').val(datos.estado); // 👈 Asigna el valor al nuevo select
                        
                    } else {
                        modalUsuario.hide();
                        Swal.fire('Error', response.mensaje, 'error');
                    }
                },
                 error: function() {
                    modalUsuario.hide();
                    Swal.fire('Error', 'Error al cargar los datos. Revisa la ruta AJAX.', 'error');
                }
            });
        } 
    });

    // --- 4. Eliminar Usuario (Eliminación Lógica) ---
    $('#dataTableUsuarios').on('click', '.btn-eliminar', function() {
        var id_usuario = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "El usuario será deshabilitado (eliminación lógica).",
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
                    url: '../controladores/usuario_controlador.php',
                    data: { accion: 'eliminar', id_usuario: id_usuario },
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
                        Swal.fire('Error', 'Error de comunicación al eliminar usuario.', 'error');
                    }
                });
            }
        });
    });
});