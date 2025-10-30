<?php
include_once "../conexion.php";
include_once "../modelos/categoria_modelo.php"; 
include_once "../modelos/producto_modelo.php";

$categorias = obtenerCategorias($conexion);
$productos = obtenerProductos($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Bazar Carolian</title>
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
            <h1>Gestión de Inventario (Productos)</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrarProducto">
                <i class="fas fa-plus"></i> Registrar Nuevo Producto
            </button>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>Inventario Actual</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>P. Costo</th>
                                <th>P. Venta</th>
                                <th>F. Caduc.</th> <th>Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($productos->num_rows > 0) {
                                while($fila_prod = $productos->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $fila_prod['id_producto'] . "</td>";
                                    echo "<td>" . $fila_prod['codigo'] . "</td>";
                                    echo "<td>" . $fila_prod['nombre_producto'] . "</td>";
                                    echo "<td>" . $fila_prod['nombre_categoria'] . "</td>"; 
                                    echo "<td>S/ " . number_format($fila_prod['precio_costo'], 2) . "</td>";
                                    echo "<td>S/ " . number_format($fila_prod['precio_venta'], 2) . "</td>";

                                    // --- ¡FORMATO DE FECHA CORREGIDO (DD/MM/YYYY)! ---
                                    $fecha_formateada = 'N/A';
                                    if (!empty($fila_prod['fecha_caducidad'])) {
                                        // Convierte la fecha de la BD (YYYY-MM-DD) al formato (DD/MM/YYYY)
                                        $fecha_formateada = date('d/m/Y', strtotime($fila_prod['fecha_caducidad']));
                                    }
                                    echo "<td>" . $fecha_formateada . "</td>";
                                    
                                    echo "<td>" . $fila_prod['stock'] . "</td>";
                                    
                                    if ($fila_prod['estado'] == 1) {
                                        echo "<td><span class='badge bg-success'>Activo</span></td>";
                                    } else {
                                        echo "<td><span class='badge bg-danger'>Inactivo</span></td>";
                                    }
                                    
                                    echo "<td>
                                            <a href='../vistas/editar_producto.php?id=" . $fila_prod['id_producto'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                            <button onclick='confirmarEliminarProducto(" . $fila_prod['id_producto'] . ")' class='btn btn-danger btn-sm'>Eliminar</button>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10' class='text-center'>No hay productos registrados.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <div class="modal fade" id="modalRegistrarProducto" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Registrar Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="../controladores/registrar_producto.php" method="POST">
                    <div class="modal-body">
                        
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
                                    $categorias->data_seek(0);
                                    while($fila_cat = $categorias->fetch_assoc()) {
                                        echo "<option value='" . $fila_cat['id_categoria'] . "'>" . $fila_cat['nombre'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="codigo_producto" class="form-label">Código (SKU):</label>
                                <input type="text" class="form-control" id="codigo_producto" name="codigo_producto">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="precio_producto" class="form-label">P. Venta (S/.):</label>
                                <input type="number" step="0.01" class="form-control" id="precio_producto" name="precio_producto" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_caducidad" class="form-label">Fecha Caduc.:</label>
                                <input type="date" class="form-control" id="fecha_caducidad" name="fecha_caducidad">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label">Estado:</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="1" selected>Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="desc_producto" class="form-label">Descripción (Opcional):</label>
                            <textarea class="form-control" id="desc_producto" name="desc_producto" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Producto</button>
                    </div>
                </form>

            </div>
        </div>
    </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Tus funciones de 'mostrarAlertas' y 'confirmarEliminarProducto'
        // (Sin cambios)
        function mostrarAlertas() { /* ... */ }
        function confirmarEliminarProducto(id) { /* ... */ }
        document.addEventListener('DOMContentLoaded', mostrarAlertas);
    </script>
</body>
</html>