<?php
// 1. Incluimos los archivos necesarios
include_once "../conexion.php";
include_once "../modelos/producto_modelo.php";
include_once "../modelos/categoria_modelo.php"; // (Para el dropdown)

// 2. Verificamos que se haya enviado un ID
if (!isset($_GET['id'])) {
    header("Location: ../vistas/inventario.php");
    exit();
}

$id_producto = $_GET['id'];

// 3. Obtenemos los datos del producto
$producto = obtenerProductoPorID($conexion, $id_producto);

// 4. Si el producto no existe, redirigimos
if (!$producto) {
    header("Location: ../vistas/inventario.php?status=notfound");
    exit();
}

// 5. Obtenemos TODAS las categorías para el dropdown
$categorias = obtenerCategorias($conexion);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - Bazar Carolian</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h1 class="mb-4">Editar Producto (ID: <?php echo $producto['id_producto']; ?>)</h1>
                
                <form action="../controladores/actualizar_producto.php" method="POST">
                    
                    <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">

                    <div class="mb-3">
                        <label for="nombre_producto" class="form-label">Nombre del Producto:</label>
                        <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" value="<?php echo $producto['nombre']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="id_categoria" class="form-label">Categoría:</label>
                        <select class="form-select" id="id_categoria" name="id_categoria" required>
                            <option value="">Seleccione una categoría</option>
                            <?php
                            // Llenamos el dropdown y marcamos la categoría actual
                            if ($categorias->num_rows > 0) {
                                while($fila_cat = $categorias->fetch_assoc()) {
                                    // Comparamos el ID de la categoría con el ID de la categoría del producto
                                    $selected = ($fila_cat['id_categoria'] == $producto['id_categoria']) ? 'selected' : '';
                                    echo "<option value='" . $fila_cat['id_categoria'] . "' $selected>" . $fila_cat['nombre'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="desc_producto" class="form-label">Descripción (Opcional):</label>
                        <textarea class="form-control" id="desc_producto" name="desc_producto" rows="2"><?php echo $producto['descripcion']; ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="precio_producto" class="form-label">Precio (S/.):</label>
                            <input type="number" step="0.01" class="form-control" id="precio_producto" name="precio_producto" value="<?php echo $producto['precio_venta']; ?>" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="stock_producto" class="form-label">Stock (Uds.):</label>
                            <input type="number" class="form-control" id="stock_producto" name="stock_producto" value="<?php echo $producto['stock']; ?>" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Actualizar Producto</button>
                    <a href="../vistas/inventario.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>