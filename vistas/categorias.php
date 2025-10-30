<?php
// Ubicación: vistas/categoria.php

include_once "../conexion.php";
include_once "../modelos/categoria_modelo.php"; 

$categorias = obtenerCategorias($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - Bazar Carolian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .badge { font-size: 0.85em; }
        .table-sm th, .table-sm td { padding: 0.5rem; } 
    </style>
</head>
<body>
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestión de Categorías</h1>
            <button id="btn_nueva_categoria" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCategoria">
                <i class="fas fa-plus"></i> Registrar Nueva Categoría
            </button>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>Categorías Existentes</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm" id="dataTableCategorias">
                        
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($categorias->num_rows > 0) {
                                while($fila = $categorias->fetch_assoc()) {
                                    $estado_html = ($fila['estado'] == 1) ? "<span class='badge bg-success'>Activo</span>" : "<span class='badge bg-danger'>Inactivo</span>";

                                    echo "<tr>";
                                    echo "<td>" . $fila['id_categoria'] . "</td>";
                                    echo "<td>" . $fila['nombre'] . "</td>";
                                    echo "<td>" . $fila['descripcion'] . "</td>";
                                    echo "<td>" . $estado_html . "</td>";
                                    
                                    echo "<td>
                                        <button type='button' class='btn btn-warning btn-sm btn-editar me-1' 
                                            data-id='" . $fila['id_categoria'] . "' data-bs-toggle='modal' data-bs-target='#modalCategoria'>
                                            Editar
                                        </button>
                                        <button onclick='confirmarEliminarCategoria(" . $fila['id_categoria'] . ")' class='btn btn-danger btn-sm'>Eliminar</button>
                                    </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>No hay categorías registradas.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
    
    <div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalLabelCategoria" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelCategoria">Registrar Nueva Categoría</h5> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form id="formCategoria"> 
                    <div class="modal-body">
                        
                        <input type="hidden" id="id_categoria" name="id_categoria">
                        <input type="hidden" id="accion_categoria" name="accion" value="registrar"> 
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Categoría:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (Opcional):</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3" id="estado_group" style="display:none;">
                            <label for="estado" class="form-label">Estado:</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Categoría</button>
                    </div>
                </form>

            </div>
        </div>
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    
    <script>
        // Función de eliminación: ADAPTADA para usar AJAX
        function confirmarEliminarCategoria(id) { 
             Swal.fire({
                title: '¿Estás seguro?',
                text: "¡La categoría será eliminada!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, ¡bórrala!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // En lugar de redireccionar a eliminar_categoria.php, usamos AJAX
                    $.ajax({
                        type: 'GET',
                        url: '../controladores/categoria_controlador.php', // Apunta al controlador AJAX
                        data: { accion: 'eliminar', id_categoria: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.exito) {
                                Swal.fire('¡Eliminada!', response.mensaje, 'success');
                                window.location.reload(); 
                            } else {
                                Swal.fire('Error', response.mensaje, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Error de comunicación al eliminar categoría.', 'error');
                        }
                    });
                }
            });
        }
        
        // Eliminamos la función 'mostrarAlertas' porque AJAX maneja las alertas en tiempo real.
    </script>
    
    <script src="../assets/js/categorias_ajax.js"></script>
</body>
</html>