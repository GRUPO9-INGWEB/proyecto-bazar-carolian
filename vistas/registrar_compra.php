<?php
// Incluimos la conexión y los modelos necesarios
include_once "../conexion.php";
include_once "../modelos/proveedor_modelo.php"; 
// (No incluimos producto_modelo aquí porque los buscaremos con AJAX)

// Obtenemos los proveedores ACTIVOS para el <select>
$proveedores = $conexion->query("SELECT id_proveedor, razon_social FROM proveedores WHERE estado = 1 ORDER BY razon_social ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nueva Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card-header { background-color: #343a40; color: white; }
        .total-line { font-size: 1.2em; font-weight: bold; }
        #lista_productos_compra tr td { vertical-align: middle; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-truck-loading"></i> Registrar Nueva Compra</h1>

        <form id="form_nueva_compra" action="../controladores/procesar_compra.php" method="POST">
            
            <div class="card mb-4">
                <div class="card-header">
                    Datos del Proveedor y Comprobante
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_proveedor" class="form-label">Proveedor:</label>
                            <select class="form-select" id="id_proveedor" name="id_proveedor" required>
                                <option value="">Seleccione un proveedor</option>
                                <?php
                                if ($proveedores->num_rows > 0) {
                                    while($fila_prov = $proveedores->fetch_assoc()) {
                                        echo "<option value='" . $fila_prov['id_proveedor'] . "'>" . $fila_prov['razon_social'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="tipo_comprobante" class="form-label">Tipo Comprobante:</label>
                            <select class="form-select" id="tipo_comprobante" name="tipo_comprobante" required>
                                <option value="Factura">Factura</option>
                                <option value="Boleta">Boleta</option>
                                <option value="Guia Remision">Guía Remisión</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="numero_comprobante" class="form-label">N° Comprobante:</label>
                            <input type="text" class="form-control" id="numero_comprobante" name="numero_comprobante">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    Añadir Productos
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="buscar_producto" class="form-label">Buscar Producto (Por Nombre o Código):</label>
                            <input type="text" class="form-control" id="buscar_producto" placeholder="Escriba para buscar...">
                            <div id="resultados_busqueda" class="list-group mt-2" style="position: absolute; z-index: 1000; width: 95%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    Detalle de la Compra
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th style="width: 120px;">Cantidad</th>
                                    <th style="width: 150px;">Precio Costo (S/.)</th>
                                    <th style="width: 150px;">Subtotal (S/.)</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="lista_productos_compra">
                                <tr id="fila_vacia">
                                    <td colspan="5" class="text-center">No hay productos en la compra.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <strong>S/ <span id="total_subtotal">0.00</span></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>IGV (18%):</span>
                                <strong>S/ <span id="total_igv">0.00</span></strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between total-line">
                                <span>Total a Pagar:</span>
                                <strong>S/ <span id="total_final">0.00</span></strong>
                            </div>
                            
                            <input type="hidden" name="subtotal" id="input_subtotal" value="0">
                            <input type="hidden" name="igv" id="input_igv" value="0">
                            <input type="hidden" name="total" id="input_total" value="0">
                            <input type="hidden" name="carrito" id="input_carrito" value="[]">

                            <button type="submit" class="btn btn-success w-100 mt-3">
                                <i class="fas fa-save"></i> Guardar Compra
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="../assets/js/compras.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            if (status === 'success') {
                Swal.fire('¡Éxito!', 'Compra registrada correctamente. El stock ha sido actualizado.', 'success');
                window.history.replaceState(null, null, window.location.pathname);
            } else if (status === 'error') {
                Swal.fire('¡Error!', 'No se pudo registrar la compra.', 'error');
                window.history.replaceState(null, null, window.location.pathname);
            }
        });
    </script>
</body>
</html>