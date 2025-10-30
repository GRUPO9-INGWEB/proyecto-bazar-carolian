<?php
// Incluimos la conexión y el modelo
include_once "../conexion.php";
include_once "../modelos/proveedor_modelo.php"; 

// Obtenemos todos los proveedores para la tabla
$proveedores = obtenerProveedores($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores</title>
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
            <h1>Gestión de Proveedores</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrarProveedor">
                <i class="fas fa-plus"></i> Registrar Nuevo Proveedor
            </button>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Razón Social</th>
                                <th>RUC</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($proveedores->num_rows > 0) {
                                while($fila_prov = $proveedores->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $fila_prov['id_proveedor'] . "</td>";
                                    echo "<td>" . $fila_prov['razon_social'] . "</td>";
                                    echo "<td>" . $fila_prov['ruc'] . "</td>";
                                    echo "<td>" . ($fila_prov['telefono'] ? $fila_prov['telefono'] : 'N/A') . "</td>";
                                    echo "<td>" . ($fila_prov['email'] ? $fila_prov['email'] : 'N/A') . "</td>";
                                    
                                    if ($fila_prov['estado'] == 1) {
                                        echo "<td><span class='badge bg-success'>Activo</span></td>";
                                    } else {
                                        echo "<td><span class='badge bg-danger'>Inactivo</span></td>";
                                    }
                                    
                                    echo "<td>
                                            <a href='../vistas/editar_proveedor.php?id=" . $fila_prov['id_proveedor'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                            <button onclick='confirmarEliminar(" . $fila_prov['id_proveedor'] . ")' class='btn btn-danger btn-sm'>Eliminar</button>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No hay proveedores registrados.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <div class="modal fade" id="modalRegistrarProveedor" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Registrar Nuevo Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="../controladores/registrar_proveedor.php" method="POST">
                    <div class="modal-body">
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="ruc" class="form-label">RUC:</label>
                                <input type="text" class="form-control" id="ruc" name="ruc" required maxlength="11">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="razon_social" class="form-label">Razón Social:</label>
                                <input type="text" class="form-control" id="razon_social" name="razon_social" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección (Opcional):</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>

                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="telefono" class="form-label">Teléfono (Opcional):</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" maxlength="15">
                            </div>
                            <div class="col-md-7 mb-3">
                                <label for="email" class="form-label">Email (Opcional):</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
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
                        <button type="submit" class="btn btn-primary">Guardar Proveedor</button>
                    </div>
                </form>

            </div>
        </div>
    </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Función para mostrar alertas de SweetAlert
        function mostrarAlertas() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            let title, text, icon;

            if (status === 'success') {
                title = '¡Éxito!'; text = 'Proveedor registrado correctamente.'; icon = 'success';
            } else if (status === 'updated') {
                title = '¡Actualizado!'; text = 'Proveedor actualizado correctamente.'; icon = 'success';
            } else if (status === 'deleted') {
                title = '¡Eliminado!'; text = 'El proveedor se ha eliminado.'; icon = 'success';
            } else if (status === 'error' || status === 'update_error' || status === 'delete_error') {
                title = '¡Error!'; text = 'Ocurrió un error al procesar la solicitud.'; icon = 'error';
            } else if (status === 'ruc_exists') {
                title = '¡Error!'; text = 'El RUC ingresado ya existe en otro proveedor.'; icon = 'error';
            } else if (status === 'notfound') {
                title = '¡Error!'; text = 'No se encontró el proveedor solicitado.'; icon = 'error';
            }

            if (status) {
                Swal.fire({ title: title, text: text, icon: icon, timer: 2500, showConfirmButton: false });
                // Limpiamos la URL para que la alerta no se repita al recargar
                window.history.replaceState(null, null, window.location.pathname);
            }
        }
        
        // Función para confirmar la eliminación
        function confirmarEliminar(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, ¡bórralo!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigimos al controlador de eliminación
                    window.location.href = '../controladores/eliminar_proveedor.php?id=' + id;
                }
            })
        }
        
        // Ejecutar la función de alertas cuando el DOM esté cargado
        document.addEventListener('DOMContentLoaded', mostrarAlertas);
    </script>
</body>
</html>