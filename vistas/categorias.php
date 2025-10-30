<?php
// Incluimos la conexión y el modelo
include_once "../conexion.php";
include_once "../modelos/categoria_modelo.php";

// Obtenemos todas las categorías para listarlas en la tabla
$categorias = obtenerCategorias($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    
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
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrarCategoria">
                <i class="fas fa-plus"></i> Registrar Nueva Categoría
            </button>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>Categorías Existentes</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th> <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($categorias->num_rows > 0) {
                                while($fila = $categorias->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $fila['id_categoria'] . "</td>";
                                    echo "<td>" . $fila['nombre'] . "</td>";
                                    echo "<td>" . $fila['descripcion'] . "</td>";
                                    
                                    // Mostramos el estado con una insignia
                                    if ($fila['estado'] == 1) {
                                        echo "<td><span class='badge bg-success'>Activo</span></td>";
                                    } else {
                                        echo "<td><span class='badge bg-danger'>Inactivo</span></td>";
                                    }
                                    
                                    echo "<td>
                                            <a href='../vistas/editar_categoria.php?id=" . $fila['id_categoria'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                            <button onclick='confirmarEliminar(" . $fila['id_categoria'] . ")' class='btn btn-danger btn-sm'>Eliminar</button>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>No hay categorías registradas.</td></tr>"; // Colspan 5
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <div class="modal fade" id="modalRegistrarCategoria" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Registrar Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="../controladores/registrar_categoria.php" method="POST">
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label for="nombre_categoria" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="desc_categoria" class="form-label">Descripción (Opcional):</label>
                            <textarea class="form-control" id="desc_categoria" name="desc_categoria" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado:</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="1" selected>Activo</option>
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
    </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Tus funciones JS de 'mostrarAlertas' y 'confirmarEliminar'
        // son perfectas, las reutilizamos.
        
        function mostrarAlertas() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            let title, text, icon;

            if (status === 'success') {
                title = '¡Éxito!'; text = 'Categoría registrada correctamente.'; icon = 'success';
            } else if (status === 'updated') {
                title = '¡Actualizado!'; text = 'Categoría actualizada correctamente.'; icon = 'success';
            } else if (status === 'deleted') {
                title = '¡Eliminado!'; text = 'La categoría se ha eliminado.'; icon = 'success';
            } else if (status === 'error' || status === 'update_error') {
                title = '¡Error!'; text = 'Ocurrió un error al procesar la solicitud.'; icon = 'error';
            } else if (status === 'delete_error') {
                title = '¡Error!'; text = 'No se pudo eliminar. Asegúrese de que no haya productos usando esta categoría.'; icon = 'error';
            } else if (status === 'notfound') {
                title = '¡Error!'; text = 'No se encontró la categoría solicitada.'; icon = 'error';
            }

            if (status) {
                Swal.fire({ title: title, text: text, icon: icon, timer: 2500, showConfirmButton: false });
                window.history.replaceState(null, null, window.location.pathname);
            }
        }
        
        function confirmarEliminar(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto! (Asegúrese de que ningún producto esté usando esta categoría)",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, ¡bórralo!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../controladores/eliminar_categoria.php?id=' + id;
                }
            })
        }
        
        document.addEventListener('DOMContentLoaded', mostrarAlertas);
    </script>
</body>
</html>