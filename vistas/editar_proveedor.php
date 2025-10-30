<?php
include_once "../conexion.php";
include_once "../modelos/proveedor_modelo.php";

// Verificamos que venga un ID
if (!isset($_GET['id'])) {
    header("Location: ../vistas/proveedores.php");
    exit();
}
$id_proveedor = $_GET['id'];

// Obtenemos los datos del proveedor
$proveedor = obtenerProveedorPorID($conexion, $id_proveedor);

// Si no existe, lo regresamos
if (!$proveedor) {
    header("Location: ../vistas/proveedores.php?status=notfound");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h1 class="mb-4">Editar Proveedor (ID: <?php echo $proveedor['id_proveedor']; ?>)</h1>
                
                <form action="../controladores/actualizar_proveedor.php" method="POST">
                    <input type="hidden" name="id_proveedor" value="<?php echo $proveedor['id_proveedor']; ?>">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="ruc" class="form-label">RUC:</label>
                            <input type="text" class="form-control" id="ruc" name="ruc" value="<?php echo $proveedor['ruc']; ?>" required maxlength="11">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="razon_social" class="form-label">Razón Social:</label>
                            <input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $proveedor['razon_social']; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección (Opcional):</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $proveedor['direccion']; ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="telefono" class="form-label">Teléfono (Opcional):</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $proveedor['telefono']; ?>" maxlength="15">
                        </div>
                        <div class="col-md-7 mb-3">
                            <label for="email" class="form-label">Email (Opcional):</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $proveedor['email']; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado:</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="1" <?php echo ($proveedor['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo ($proveedor['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">Actualizar Proveedor</button>
                    <a href="../vistas/proveedores.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>