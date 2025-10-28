<?php
// Incluimos la conexión y el modelo
include_once "../conexion.php";
include_once "../modelos/categoria_modelo.php";

// --- LÓGICA PARA MOSTRAR DATOS ---
// Obtenemos todas las categorías para listarlas en la tabla
$categorias = obtenerCategorias($conexion);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - Bazar Carolian</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Gestión de Categorías</h1>

        <div class="row">
            <div class="col-md-4">
                <h3>Registrar Nueva Categoría</h3>
                
                <form action="../controladores/registrar_categoria.php" method="POST">
                    <div class="mb-3">
                        <label for="nombre_categoria" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" required>
                    </div>
                    <div class="mb-3">
                        <label for="desc_categoria" class="form-label">Descripción (Opcional):</label>
                        <textarea class="form-control" id="desc_categoria" name="desc_categoria" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Categoría</button>
                </form>
            </div>

            <div class="col-md-8">
                <h3>Categorías Existentes</h3>
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Iteramos sobre los resultados de la base de datos
                        if ($categorias->num_rows > 0) {
                            while($fila = $categorias->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $fila['id_categoria'] . "</td>";
                                echo "<td>" . $fila['nombre'] . "</td>";
                                echo "<td>" . $fila['descripcion'] . "</td>";
                                echo "<td>
                                        <a href='../vistas/editar_categoria.php?id=" . $fila['id_categoria'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                        
                                        <button onclick='confirmarEliminar(" . $fila['id_categoria'] . ")' class='btn btn-danger btn-sm'>Eliminar</button>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No hay categorías registradas.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Función para mostrar alertas de Éxito o Error
        function mostrarAlertas() {
            // Obtenemos los parámetros de la URL
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');

            let title, text, icon;

            if (status === 'success') {
                title = '¡Éxito!';
                text = 'Categoría registrada correctamente.';
                icon = 'success';
            } else if (status === 'updated') {
                title = '¡Actualizado!';
                text = 'Categoría actualizada correctamente.';
                icon = 'success';
            } else if (status === 'error') {
                title = '¡Error!';
                text = 'Ocurrió un error al procesar la solicitud.';
                icon = 'error';
            } else if (status === 'update_error') {
                title = '¡Error!';
                text = 'Ocurrió un error al actualizar.';
                icon = 'error';
            } else if (status === 'notfound') {
                title = '¡Error!';
                text = 'No se encontró la categoría solicitada.';
                icon = 'error';
            }

            // Si hay un 'status', mostramos la alerta
            if (status) {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    timer: 2500, // Se cierra después de 2.5 segundos
                    showConfirmButton: false
                });
                
                // Limpiamos la URL para que no vuelva a salir la alerta si recarga
                window.history.replaceState(null, null, window.location.pathname);
            }
        }

        // Función para mostrar confirmación de "Eliminar"
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
                    // Si confirma, redirigimos al controlador de eliminar
                    window.location.href = '../controladores/eliminar_categoria.php?id=' + id;
                }
            })
        }

        // Ejecutamos la función de alertas al cargar la página
        document.addEventListener('DOMContentLoaded', mostrarAlertas);
    </script>
</body>
</html>