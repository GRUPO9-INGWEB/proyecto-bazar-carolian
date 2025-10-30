<?php
// Ubicación: vistas/clientes.php
include_once "../conexion.php";
include_once "../modelos/cliente_modelo.php";

$clientes = obtenerClientes($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
</head>
<body>

<div class="container mt-5">
    <h1 class="h3 mb-4 text-gray-800">Gestión de Clientes</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-end">
            <button id="btn_nuevo_cliente" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCliente">
                <i class="fas fa-user-plus"></i> Registrar Nuevo Cliente
            </button>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableClientes" width="100%" cellspacing="0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Tipo Doc.</th>
                            <th>Nº Doc.</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($clientes->num_rows > 0) {
                            while ($cliente = $clientes->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . $cliente['id_cliente'] . '</td>';
                                echo '<td>' . htmlspecialchars($cliente['nombre_completo']) . '</td>';
                                echo '<td>' . htmlspecialchars($cliente['documento_tipo']) . '</td>';
                                echo '<td>' . htmlspecialchars($cliente['documento_numero']) . '</td>'; 
                                echo '<td>' . htmlspecialchars($cliente['telefono']) . '</td>';
                                echo '<td>' . htmlspecialchars($cliente['email']) . '</td>';
                                echo '<td>' . htmlspecialchars($cliente['direccion']) . '</td>';
                                
                                // CELDA DE ESTADO: Mapea 1/0 a colores
                                echo '<td>';
                                if (isset($cliente['estado'])) {
                                    if ($cliente['estado'] == 1) { // 1 = Activo
                                        echo '<span class="badge bg-success">Activo</span>';
                                    } else if ($cliente['estado'] == 0) { // 0 = Inactivo/Eliminado
                                        echo '<span class="badge bg-danger">Inactivo</span>';
                                    } else {
                                         echo '<span class="badge bg-secondary">N/D</span>';
                                    }
                                } else {
                                    echo '<span class="badge bg-warning">Error</span>';
                                }
                                echo '</td>';
                                
                                echo '<td>';
                                echo '<button class="btn btn-warning btn-sm btn-editar me-1" data-id="' . $cliente['id_cliente'] . '" data-bs-toggle="modal" data-bs-target="#modalCliente">Editar</button>';
                                echo '<button class="btn btn-danger btn-sm btn-eliminar" data-id="' . $cliente['id_cliente'] . '">Eliminar</button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="9" class="text-center">No hay clientes registrados.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalClienteLabel">Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCliente">
                <div class="modal-body">
                    <input type="hidden" id="id_cliente" name="id_cliente">
                    <input type="hidden" id="accion" name="accion" value="registrar">
                    
                    <div class="mb-3"><label for="nombre_completo" class="form-label">Nombre/Razón Social *</label><input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required></div>
                    
                    <div class="mb-3">
                        <label for="documento_tipo" class="form-label">Tipo de Documento *</label>
                        <select class="form-select" id="documento_tipo" name="documento_tipo" required>
                            <option value="">Seleccione</option>
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="OTRO">Otro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3"><label for="documento_numero" class="form-label">Nº Documento *</label><input type="text" class="form-control" id="documento_numero" name="documento_numero" required></div>
                    <div class="mb-3"><label for="telefono" class="form-label">Teléfono</label><input type="text" class="form-control" id="telefono" name="telefono" maxlength="15"></div>
                    <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email"></div>
                    <div class="mb-3"><label for="direccion" class="form-label">Dirección</label><input type="text" class="form-control" id="direccion" name="direccion"></div>
                    
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado *</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="">Seleccione el Estado</option>
                            <option value="A">Activo</option> 
                            <option value="I">Inactivo</option> 
                        </select>
                    </div>
                    </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>

<script src="../assets/js/clientes.js"></script> 
</body>
</html>