<?php
include_once "../conexion.php";
include_once "../modelos/producto_modelo.php";
include_once "../modelos/categoria_modelo.php";

if (!isset($_GET['id'])) {
    header("Location: ../vistas/producto.php");
    exit();
}
$id_producto = $_GET['id'];
$producto = obtenerProductoPorID($conexion, $id_producto); // Ya trae 'estado' y 'fecha_caducidad'
$categorias = obtenerCategorias($conexion);

if (!$producto) {
    header("Location: ../vistas/producto.php?status=notfound");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
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
                            if ($categorias->num_rows > 0) {
                                while($fila_cat = $categorias->fetch_assoc()) {
                                    $selected = ($fila_cat['id_categoria'] == $producto['id_categoria']) ? 'selected' : '';
                                    echo "<option value='" . $fila_cat['id_categoria'] . "' $selected>" . $fila_cat['nombre'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="codigo_producto" class="form-label">Código (SKU):</label>
                            <input type="text" class="form-control" id="codigo_producto" name="codigo_producto" value="<?php echo $producto['codigo']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="precio_producto" class="form-label">P. Venta (S/.):</label>
                            <input type="number" step="0.01" class="form-control" id="precio_producto" name="precio_producto" value="<?php echo $producto['precio_venta']; ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_caducidad" class="form-label">Fecha Caduc.:</label>
                            <input type="date" class="form-control" id="fecha_caducidad" name="fecha_caducidad" value="<?php echo $producto['fecha_caducidad']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label">Estado:</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="1" <?php echo ($producto['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                <option value="0" <?php echo ($producto['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="desc_producto" class="form-label">Descripción (Opcional):</label>
                        <textarea class="form-control" id="desc_producto" name="desc_producto" rows="2"><?php echo $producto['descripcion']; ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Actualizar Producto</button>
                    <a href="../vistas/producto.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>