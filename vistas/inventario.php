<?php
// Incluimos la conexión y los modelos
include_once "../conexion.php";
include_once "../modelos/categoria_modelo.php"; 
include_once "../modelos/producto_modelo.php";

// Obtenemos los datos para los dropdowns y la tabla
$categorias = obtenerCategorias($conexion);
$productos = obtenerProductos($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario - Bazar Carolian</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Gestión de Inventario (Productos)</h1>

        <div class="row">
            <div class="col-md-4">
                <h3>Registrar Nuevo Producto</h3>
                
                <form action="../controladores/registrar_producto.php" method="POST">
                    
                    <div class="mb-3">
                        <label for="codigo_producto" class="form-label">Código (SKU / Barras):</label>
                        <input type="text" class="form-control" id="codigo_producto" name="codigo_producto">
                    </div>
                    
                    <div class="mb-3">
                        <label for="nombre_producto" class="form-label">Nombre del Producto:</label>
                        <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="id_categoria" class="form-label">Categoría:</label>
                        <select class="form-select" id="id_categoria" name="id_categoria" required>
                            <option value="">Seleccione una categoría</option>
                            <?php
                            if ($categorias->num_rows > 0) {
                                // Reiniciamos el puntero para re-usar la variable $categorias
                                $categorias->data_seek(0);
                                while($fila_cat = $categorias->fetch_assoc()) {
                                    echo "<option value='" . $fila_cat['id_categoria'] . "'>" . $fila_cat['nombre'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="desc_producto" class="form-label">Descripción (Opcional):</label>
                        <textarea class="form-control" id="desc_producto" name="desc_producto" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="precio_producto" class="form-label">Precio (S/.):</label>
                            <input type="number" step="0.01" class="form-control" id="precio_producto" name="precio_producto" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="stock_producto" class="form-label">Stock (Uds.):</label>
                            <input type="number" class="form-control" id="stock_producto" name="stock_producto" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Guardar Producto</button>
                </form>
            </div>

            <div class="col-md-8">
                <h3>Inventario Actual</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th> <th>Precio</th>   <th>Stock</th>    <th>Acciones</th> </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Iteramos sobre los productos
                            if ($productos->num_rows > 0) {
                                while($fila_prod = $productos->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $fila_prod['id_producto'] . "</td>";
                                    echo "<td>" . $fila_prod['codigo'] . "</td>";
                                    echo "<td>" . $fila_prod['nombre_producto'] . "</td>";
                                    
                                    // ¡AQUÍ ESTÁ LA CELDA CORREGIDA QUE FALTABA!
                                    echo "<td>" . $fila_prod['nombre_categoria'] . "</td>"; 
                                    
                                    echo "<td>S/ " . number_format($fila_prod['precio_venta'], 2) . "</td>";
                                    echo "<td>" . $fila_prod['stock'] . "</td>";
                                    echo "<td>
                                            <a href='../vistas/editar_producto.php?id=" . $fila_prod['id_producto'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                            <button onclick='confirmarEliminarProducto(" . $fila_prod['id_producto'] . ")' class='btn btn-danger btn-sm'>Eliminar</button>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No hay productos registrados.</td></tr>"; // <-- Colspan es 7
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function mostrarAlertas() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            let title, text, icon;

            if (status === 'success') {
                title = '¡Éxito!'; text = 'Producto registrado correctamente.'; icon = 'success';
            } else if (status === 'updated') {
                title = '¡Actualizado!'; text = 'Producto actualizado correctamente.'; icon = 'success';
            } else if (status === 'deleted') {
                title = '¡Eliminado!'; text = 'El producto se ha eliminado.'; icon = 'success';
            } else if (status === 'error' || status === 'update_error' || status === 'delete_error') {
                title = '¡Error!'; text = 'Ocurrió un error al procesar la solicitud.'; icon = 'error';
            } else if (status === 'code_exists') {
                title = '¡Error!'; text = 'El código ingresado ya existe en otro producto.'; icon = 'error';
            } else if (status === 'notfound') {
                title = '¡Error!'; text = 'No se encontró el producto solicitado.'; icon = 'error';
            }

            if (status) {
                Swal.fire({ title: title, text: text, icon: icon, timer: 2500, showConfirmButton: false });
                window.history.replaceState(null, null, window.location.pathname);
            }
        }
        
        function confirmarEliminarProducto(id) {
            Swal.fire({
                title: '¿Estás seguro?', text: "¡No podrás revertir esto!", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, ¡bórralo!', cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../controladores/eliminar_producto.php?id=' + id;
                }
            })
        }
        document.addEventListener('DOMContentLoaded', mostrarAlertas);
    </script>
</body>
</html>