// Ubicación: assets/js/categorias_ajax.js

$(document).ready(function() {
    
    const modalElement = document.getElementById('modalCategoria');
    const modalCategoria = new bootstrap.Modal(modalElement);

    // --- 1. Configurar Modal para REGISTRAR (btn_nueva_categoria) ---
    $('#btn_nueva_categoria').on('click', function() {
        $('#modalLabelCategoria').text('Registrar Nueva Categoría');
        $('#formCategoria')[0].reset(); 
        $('#id_categoria').val(''); 
        $('#accion_categoria').val('registrar'); 
        
        // Ocultar campo estado en registro
        $('#estado_group').hide();
    });

    // --- 2. Abrir Modal y Cargar Datos para EDITAR (btn-editar) ---
    $('#dataTableCategorias').on('click', '.btn-editar', function() {
        var id_categoria = $(this).data('id');
        
        $('#modalLabelCategoria').text('Editar Categoría');
        $('#accion_categoria').val('editar');
        
        // Mostrar campo estado en edición
        $('#estado_group').show();

        // 2a. AJAX para obtener datos 
        $.ajax({
            type: 'GET',
            url: '../controladores/categoria_controlador.php',
            data: { accion: 'obtener_por_id', id_categoria: id_categoria },
            dataType: 'json',
            success: function(response) {
                if (response.exito) {
                    var datos = response.datos;
                    
                    // 2b. Llenar el formulario
                    $('#id_categoria').val(datos.id_categoria);
                    $('#nombre').val(datos.nombre); 
                    $('#descripcion').val(datos.descripcion);
                    $('#estado').val(datos.estado); 

                } else {
                    Swal.fire('Error', 'Error al cargar datos: ' + response.mensaje, 'error');
                }
            },
            error: function() {
                 Swal.fire('Error', 'Error de comunicación al obtener categoría.', 'error');
            }
        });
    });

    // --- 3. Enviar Formulario (Registro o Edición) ---
    $('#formCategoria').on('submit', function(e) {
        e.preventDefault(); 
        
        $.ajax({
            type: 'POST',
            url: '../controladores/categoria_controlador.php', 
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.exito) {
                    Swal.fire('¡Éxito!', response.mensaje, 'success');
                    modalCategoria.hide(); 
                    window.location.reload(); 
                } else {
                    Swal.fire('Error', response.mensaje, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de comunicación al guardar categoría.', 'error');
            }
        });
    });
});