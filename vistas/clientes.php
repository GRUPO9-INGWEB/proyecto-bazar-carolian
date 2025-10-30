<?php
// Ubicación: vistas/clientes.php
include_once "../conexion.php";
include_once "../modelos/cliente_modelo.php";

$clientes = obtenerClientes($conexion);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gestión de Clientes</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-end">
            <button id="btn_nuevo_cliente" class="btn btn-primary" data-toggle="modal" data-target="#modalCliente">
                <i class="fas fa-user-plus"></i> Registrar Nuevo Cliente
            </button>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableClientes" width="100%" cellspacing="0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Tipo Doc.</th>
                            <th>Nº Doc.</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        while ($cliente = $clientes->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $cliente['id_cliente'] . '</td>';
                            echo '<td>' . htmlspecialchars($cliente['nombre_completo']) . '</td>';
                            echo '<td>' . htmlspecialchars($cliente['documento_tipo']) . '</td>';
                            echo '<td>' . htmlspecialchars($cliente['documento_numero']) . '</td>'; 
                            echo '<td>' . htmlspecialchars($cliente['telefono']) . '</td>';
                            echo '<td>' . htmlspecialchars($cliente['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($cliente['direccion']) . '</td>';
                            echo '<td>';
                            echo '<button class="btn btn-warning btn-sm btn-editar mr-1" data-id="' . $cliente['id_cliente'] . '">Editar</button>';
                            echo '<button class="btn btn-danger btn-sm btn-eliminar" data-id="' . $cliente['id_cliente'] . '">Eliminar</button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCliente" tabindex="-1" role="dialog" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalClienteLabel">Nuevo Cliente</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="formCliente">
                <div class="modal-body">
                    <input type="hidden" id="id_cliente" name="id_cliente">
                    <input type="hidden" id="accion" name="accion" value="registrar">
                    
                    <div class="form-group"><label for="nombre_completo">Nombre/Razón Social *</label><input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required></div>
                    
                    <div class="form-group">
                        <label for="documento_tipo">Tipo de Documento *</label>
                        <select class="form-control" id="documento_tipo" name="documento_tipo" required>
                            <option value="">Seleccione</option>
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="OTRO">Otro</option>
                        </select>
                    </div>
                    
                    <div class="form-group"><label for="documento_numero">Nº Documento *</label><input type="text" class="form-control" id="documento_numero" name="documento_numero" required></div>
                    <div class="form-group"><label for="telefono">Teléfono</label><input type="text" class="form-control" id="telefono" name="telefono" maxlength="15"></div>
                    <div class="form-group"><label for="email">Email</label><input type="email" class="form-control" id="email" name="email"></div>
                    <div class="form-group"><label for="direccion">Dirección</label><input type="text" class="form-control" id="direccion" name="direccion"></div>
                    
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../js/clientes.js"></script>