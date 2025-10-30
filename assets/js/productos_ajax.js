// Ubicación: assets/js/productos_ajax.js
// Requiere jQuery (cargado en producto.php)

$(document).ready(function() {
    
    // Obtener la instancia del Modal BS5
    const modalElement = document.getElementById('modalRegistrarProducto');
    // Usamos el ID original de tu modal
    const modalProducto = new bootstrap.Modal(modalElement);

    // --- 1. Configurar Modal para REGISTRAR (btn_nuevo_producto) ---
    $('#btn_nuevo_producto').on('click', function() {
        $('#modalLabelProducto').text('Registrar Nuevo Producto');
        $('#formProducto')[0].reset(); 
        $('#id_producto').val(''); 
        $('#accion_producto').val('registrar'); 
        
        // Mostrar stock y hacerlo obligatorio para el registro inicial
        $('#stock_group').show();
        $('#precio_costo').prop('required', true);
        $('#stock').prop('required', true);
    });

    // --- 2. Abrir Modal y Cargar Datos para EDITAR (btn-editar) ---
    $('#dataTableProductos').on('click', '.btn-editar', function() {
        var id_producto = $(this).data('id');
        
        $('#modalLabelProducto').text('Editar Producto');
        $('#accion_producto').val('editar');
        
        // Ocultar stock en edición
        $('#stock_group').hide();
        $('#precio_costo').prop('required', false); // No forzar en edición si ya tiene valor
        $('#stock').prop('required', false);

        // 2a. AJAX para obtener datos 
        $.ajax({
            type: 'GET',
            url: '../controladores/producto_controlador.php',
            data: { accion: 'obtener_por_id', id_producto: id_producto },
            dataType: 'json',
            success: function(response) {
                if (response.exito) {
                    var datos = response.datos;
                    
                    // 2b. Llenar el formulario con los datos recibidos
                    $('#id_producto').val(datos.id_producto);
                    $('#nombre_producto').val(datos.nombre_producto); 
                    $('#codigo_producto').val(datos.codigo);
                    $('#id_categoria').val(datos.id_categoria); 
                    $('#precio_producto').val(datos.precio_venta); // Mapeo: precio_venta (BD) -> precio_producto (Form)
                    $('#precio_costo').val(datos.precio_costo);
                    $('#fecha_caducidad').val(datos.fecha_caducidad); // Formato YYYY-MM-DD
                    $('#estado').val(datos.estado); // Mapeo: estado (BD) -> estado (Form)
                    $('#desc_producto').val(datos.descripcion);

                } else {
                    Swal.fire('Error', 'Error al cargar datos: ' + response.mensaje, 'error');
                }
            },
            error: function(xhr, status, error) {
                 Swal.fire('Error', 'Error de comunicación al obtener producto: ' + error, 'error');
            }
        });
    });

    // --- 3. Enviar Formulario (Registro o Edición) ---
    $('#formProducto').on('submit', function(e) {
        e.preventDefault(); 
        
        $.ajax({
            type: 'POST',
            url: '../controladores/producto_controlador.php', 
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.exito) {
                    Swal.fire('¡Éxito!', response.mensaje, 'success');
                    modalProducto.hide(); // Ocultar la modal
                    window.location.reload(); // Recargar para actualizar la tabla
                } else {
                    Swal.fire('Error', response.mensaje, 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Error de comunicación al guardar producto: ' + error, 'error');
            }
        });
    });
    
    // La función de eliminación ya está en la vista producto.php, usando el controlador AJAX.
});