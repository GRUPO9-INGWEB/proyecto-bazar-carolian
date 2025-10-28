<?php
session_start(); // Para manejar los mensajes de SweetAlert
include_once "../conexion.php";
$id_usuario_actual = 1; // Asumimos usuario 1 (luego vendrá del Login)
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta - Bazar Carolian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    
    <style>
        /* Estilo para el autocompletar */
        .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1056; /* Encima del modal */
        }
        .ui-menu-item-wrapper {
            padding: 5px 10px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <h1 class="mb-4">Punto de Venta (POS)</h1>

        <div class="row">

            <div class="col-md-7">
                
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Buscar Producto</h5>
                        <div class="input-group">
                            <span class="input-group-text">🔍</span>
                            <input type="text" id="buscar_producto" class="form-control" placeholder="Escriba el nombre o código...">
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <form id="form_venta" action="../controladores/registrar_venta.php" method="POST">
                            <input type="hidden" name="id_usuario" value="<?php echo $id_usuario_actual; ?>">
                            <input type="hidden" name="carrito_data" id="carrito_data">
                            <input type="hidden" name="subtotal" id="input_subtotal">
                            <input type="hidden" name="igv" id="input_igv">
                            <input type="hidden" name="total" id="input_total">
                            <input type="hidden" name="id_cliente_oculto" id="id_cliente_oculto">
                            <input type="hidden" name="metodo_pago" id="input_metodo_pago">
                            <input type="hidden" name="monto_recibido" id="input_monto_recibido">
                            <input type="hidden" name="vuelto" id="input_vuelto">
                            
                            <input type="hidden" id="documento_tipo" name="documento_tipo" value="DNI" form="form_venta">
                        </form>
                        
                        <h5 class="card-title">Carrito de Venta</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cant.</th>
                                        <th>Importe</th> <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_carrito">
                                    <tr id="fila_vacia">
                                        <td colspan="5" class="text-center">El carrito está vacío</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        
                        <h5 class="card-title">Datos del Cliente</h5>
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
                                
                                <input type="text" class="form-control" 
                                       id="documento_numero" name="documento_numero" 
                                       placeholder="Buscar DNI (8 dígitos)..." 
                                       maxlength="8" 
                                       pattern="[0-9]{8}" 
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                       form="form_venta">
                                       
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
                        
                        <hr> 

                        <h5 class="text-center">Total a Pagar</h5>
                        <h1 class="display-4 fw-bold text-center text-success mb-3" id="total_pagar">
                            S/ 0.00
                        </h1>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span id="texto_subtotal">S/ 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>IGV (18%):</span>
                            <span id="texto_igv">S/ 0.00</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success btn-lg" id="btn_pago_efectivo">
                                PAGAR CON EFECTIVO
                            </button>
                            <button type="button" class="btn btn-primary btn-lg" id="btn_pago_tarjeta">
                                PAGAR CON TARJETA
                            </button>
                            <button type="button" id="btn_cancelar_venta" class="btn btn-danger">
                                CANCELAR VENTA
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div> </div> <div class="modal fade" id="modalRegistrarCliente" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form_registrar_cliente">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tipo Documento:</label>
                            <input type="text" class="form-control" id="modal_tipo_doc_texto" readonly>
                            <input type="hidden" id="modal_tipo_doc" name="modal_tipo_doc"> 
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Número Documento:</label>
                            <input type="text" class="form-control" id="modal_num_doc" name="modal_num_doc" readonly oninput="this.value = this.value.replace(/[^0-9]/g, '');"> 
                        </div>
                        <div class="mb-3">
                            <label for="modal_nombre" class="form-label">Nombre / Razón Social:</label>
                            <input type="text" class="form-control" id="modal_nombre" name="modal_nombre" required> 
                        </div>
                        <div class="mb-3">
                            <label for="modal_email" class="form-label">Email (Opcional):</label>
                            <input type="email" class="form-control" id="modal_email" name="modal_email"> 
                        </div>
                        <div class="mb-3">
                            <label for="modal_direccion" class="form-label">Dirección (Opcional):</label>
                            <input type="text" class="form-control" id="modal_direccion" name="modal_direccion"> 
                        </div>
                        <div class="mb-3">
                            <label for="modal_telefono" class="form-label">Teléfono (Opcional):</label>
                            <input type="text" class="form-control" id="modal_telefono" name="modal_telefono" oninput="this.value = this.value.replace(/[^0-9]/g, '');"> 
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

    <div class="modal fade" id="modalPagoEfectivo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pago en Efectivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">Total a Pagar:</h3>
                    <h1 class="display-5 fw-bold text-center text-success mb-3" id="modal_total_pagar">S/ 0.00</h1>
                    
                    <div class="mb-3">
                        <label for="modal_monto_recibido" class="form-label">Monto Recibido:</label>
                        <input type="number" step="0.10" class="form-control form-control-lg" id="modal_monto_recibido">
                    </div>
                    
                    <h3 class="text-center">Vuelto:</h3>
                    <h1 class="display-5 fw-bold text-center text-primary mb-3" id="modal_vuelto">S/ 0.00</h1>
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

    <?php
    if (isset($_SESSION['mensaje_venta'])) {
        $mensaje = $_SESSION['mensaje_venta'];
        echo "<script>
            Swal.fire({
                title: '" . ($mensaje['tipo'] == 'exito' ? '¡Éxito!' : '¡Error!') . "',
                text: '" . $mensaje['texto'] . "',
                icon: '" . ($mensaje['tipo'] == 'exito' ? 'success' : 'error') . "',
                timer: 3000,
                showConfirmButton: false
            });
        </script>";
        unset($_SESSION['mensaje_venta']);
    }
    ?>
</body>
</html>