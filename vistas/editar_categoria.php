<?php
// 1. Incluimos los archivos necesarios
include_once "../conexion.php";
include_once "../modelos/categoria_modelo.php";

// 2. Verificamos que se haya enviado un ID
if (!isset($_GET['id'])) {
    // Si no hay ID, redirigimos a la lista principal
    header("Location: ../vistas/categorias.php");
    exit();
}

$id_categoria = $_GET['id'];

// 3. Obtenemos los datos de la categoría
$categoria = obtenerCategoriaPorID($conexion, $id_categoria);

// 4. Si la categoría no existe, redirigimos
if (!$categoria) {
    header("Location: ../vistas/categorias.php?status=notfound");
    exit();
}

// (El resto es el HTML del formulario)
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-g">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoría - Bazar Carolian</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h1 class="mb-4">Editar Categoría (ID: <?php echo $categoria['id_categoria']; ?>)</h1>
                
                <form action="../controladores/actualizar_categoria.php" method="POST">
                    
                    <input type="hidden" name="id_categoria" value="<?php echo $categoria['id_categoria']; ?>">

                    <div class="mb-3">
                        <label for="nombre_categoria" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" value="<?php echo $categoria['nombre']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="desc_categoria" class="form-label">Descripción (Opcional):</label>
                        <textarea class="form-control" id="desc_categoria" name="desc_categoria" rows="3"><?php echo $categoria['descripcion']; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Actualizar Cambios</button>
                    <a href="../vistas/categorias.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>