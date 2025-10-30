<?php
// Incluir seguridad al inicio (necesario para $_SESSION['usuario_id'])
include_once "../includes/seguridad.php"; 
include_once "../conexion.php";

$id_usuario_actual = $_SESSION['usuario_id'] ?? 0; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta - Bazar Carolian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <h1 class="mb-4">Punto de Venta (POS)</h1>

        <div class="row">
            <div class="col-md-7">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="buscador_producto" placeholder="Escriba el nombre o código...">
                </div>

                <form id="form_venta" action="../controladores/registrar_venta.php" method="POST">
                    <input type="hidden" name="id_usuario" value="<?php echo $id_usuario_actual; ?>">
                    <input type="hidden" name="carrito_data" id="carrito_data">
                    <input type="hidden" name="subtotal" id="input_subtotal">
                    <input type="hidden" name="igv" id="input_igv">
                    <input type="hidden" name="total" id="input_total">

                    <input type="hidden" name="id_cliente_oculto" id="id_cliente_oculto">
                    <input type="hidden" name="documento_tipo" id="documento_tipo" value="DNI">
                    
                    <input type="hidden" name="metodo_pago" id="input_metodo_pago">
                    <input type="hidden" name="monto_recibido" id="input_monto_recibido">
                    <input type="hidden" name="vuelto" id="input_vuelto">
                </form>

                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5>Carrito de Venta</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cant.</th>
                                    <th>Importe</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="carrito_tbody">
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        
                        <h5 class="card-title">Datos del Cliente y Venta</h5>
                        <div class="mb-3">
                            <label for="tipo_comprobante" class="form-label">Comprobante:</label>
                            <select class="form-select" id="tipo_comprobante" name="tipo_comprobante" form="form_venta">
                                <option value="Nota de Venta">Nota de Venta (Simple)</option>
                                <option value="Boleta">Boleta (Simulada)</option>
                                <option value="Factura">Factura (Simulada)</option>
                            </select>
                        </div>
                        
                        <div id="campos_cliente" style="display: none;">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="documento_tipo_texto" style="width: 80px;">DNI</span>
                                <input type="text" class="form-control" id="documento_numero" 
                                       placeholder="Buscar DNI (8 dígitos)..." form="form_venta">
                                <button class="btn btn-info" type="button" id="btn_buscar_cliente">Buscar</button>
                            </div>
                            <div class="mb-3">
                                <label for="cliente_nombre" class="form-label">Nombre / Razón Social:</label>
                                <input type="text" class="form-control" id="cliente_nombre" name="cliente_nombre" readonly form="form_venta">
                                <button class="btn btn-link p-0" type="button" id="btn_registrar_cliente" style="display: none;">
                                    Cliente no encontrado. ¡Regístralo aquí!
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones (Opcional):</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Ej: Pagar al repartidor..." form="form_venta"></textarea>
                        </div>
                        
                        <hr> 
                        
                        <h5 class="text-center">Total a Pagar</h5>
                        <h1 class="display-4 fw-bold text-center text-success mb-3" id="total_pagar">S/ 0.00</h1>
                        
                        <div class="row text-end">
                            <div class="col-6">Subtotal:</div>
                            <div class="col-6" id="valor_subtotal">S/ 0.00</div>
                            <div class="col-6">IGV (18%):</div>
                            <div class="col-6" id="valor_igv">S/ 0.00</div>
                        </div>

                        <hr>
                        
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success btn-lg" id="btn_pago_efectivo"><i class="fas fa-money-bill-wave"></i> PAGAR CON EFECTIVO</button>
                            <button type="button" class="btn btn-primary btn-lg" id="btn_pago_tarjeta"><i class="fas fa-credit-card"></i> PAGAR CON TARJETA</button>
                            <button type="button" id="btn_cancelar_venta" class="btn btn-danger"><i class="fas fa-times-circle"></i> CANCELAR VENTA</button>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div> 
    
    <div class="modal fade" id="modalRegistrarCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClienteLabel">Registrar Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form_registrar_cliente">
                    <div class="modal-body">
                         <input type="hidden" name="documento_tipo" id="modal_tipo_doc">
                         <div class="mb-3">
                             <label class="form-label">Tipo Documento:</label>
                             <input type="text" class="form-control" readonly id="modal_tipo_doc_texto">
                         </div>
                         <div class="mb-3">
                             <label for="modal_num_doc" class="form-label">Número Documento:</label>
                             <input type="text" class="form-control" id="modal_num_doc" name="documento_numero" required readonly>
                         </div>
                         <div class="mb-3">
                             <label for="modal_nombre_completo" class="form-label">Nombre/Razón Social:</label>
                             <input type="text" class="form-control" id="modal_nombre_completo" name="nombre_completo" required>
                         </div>
                         <div class="mb-3">
                             <label for="modal_direccion" class="form-label">Dirección:</label>
                             <input type="text" class="form-control" id="modal_direccion" name="direccion">
                         </div>
                         <div class="mb-3">
                             <label for="modal_email" class="form-label">Email:</label>
                             <input type="email" class="form-control" id="modal_email" name="email">
                         </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPagoEfectivo" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                 <div class="modal-header">
                    <h5 class="modal-title" id="modalPagoLabel">Pago en Efectivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                 </div>
                 <div class="modal-body text-center">
                    <h4>Total a Pagar: <span class="text-success" id="modal_total_pagar">S/ 0.00</span></h4>
                    <div class="mb-3 mt-3">
                        <label for="modal_monto_recibido" class="form-label fs-5">Monto Recibido:</label>
                        <input type="number" step="0.01" min="0" class="form-control form-control-lg text-center" id="modal_monto_recibido" placeholder="0.00">
                    </div>
                    <h4 class="mt-4">Vuelto: <span class="text-primary" id="modal_vuelto">S/ 0.00</span></h4>
                 </div>
                 <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btn_confirmar_pago_efectivo" disabled>Confirmar Venta</button>
                 </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="../assets/js/ventas.js"></script>
</body>
</html>