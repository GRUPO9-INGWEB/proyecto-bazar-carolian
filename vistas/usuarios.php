<?php
// Ubicación: vistas/usuarios.php
include_once "../conexion.php";
include_once "../modelos/usuario_modelo.php";

// NOTA: Asegúrate que obtenerUsuarios() y obtenerRoles() retornen los resultados de la consulta.
$usuarios = obtenerUsuarios($conexion); 
$roles = obtenerRoles($conexion); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
    <style>
        .table-sm th, .table-sm td { padding: 0.5rem; } 
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="h3 mb-4 text-gray-800">Gestión de Usuarios</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-end">
            <button id="btn_nuevo_usuario" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                <i class="fas fa-plus"></i> Registrar Nuevo Usuario
            </button>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableUsuarios" width="100%" cellspacing="0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Email (Login)</th>
                            <th>Rol</th>
                            <th>Estado</th> <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $usuarios->data_seek(0); 
                        while ($usuario = $usuarios->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $usuario['id_usuario'] . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['nombre_completo']) . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['email']) . '</td>';
                            echo '<td>' . htmlspecialchars(ucfirst($usuario['rol'])) . '</td>'; 
                            
                            // 🌟 CELDA DE ESTADO: Mapea 1/0 a colores 🌟
                            echo '<td>';
                            if (isset($usuario['estado'])) {
                                if ($usuario['estado'] == 1) { // 1 = Activo
                                    echo '<span class="badge bg-success">Activo</span>';
                                } else if ($usuario['estado'] == 0) { // 0 = Inactivo/Eliminado
                                    echo '<span class="badge bg-danger">Inactivo</span>';
                                } else {
                                     echo '<span class="badge bg-secondary">Sin Definir</span>';
                                }
                            } else {
                                echo '<span class="badge bg-warning">N/D</span>';
                            }
                            echo '</td>';
                            // FIN CELDA DE ESTADO
                            
                            echo '<td>';
                            echo '<button class="btn btn-warning btn-sm btn-editar me-1" data-id="' . $usuario['id_usuario'] . '" data-bs-toggle="modal" data-bs-target="#modalUsuario">Editar</button>';
                            echo '<button class="btn btn-danger btn-sm btn-eliminar" data-id="' . $usuario['id_usuario'] . '">Eliminar</button>';
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

<div class="modal fade" id="modalUsuario" tabindex="-1" role="dialog" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUsuarioLabel">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUsuario">
                <div class="modal-body">
                    <input type="hidden" id="id_usuario" name="id_usuario">
                    <input type="hidden" id="accion" name="accion" value="registrar">
                    
                    <div class="mb-3"><label for="nombre_completo" class="form-label">Nombre Completo *</label><input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required></div>
                    <div class="mb-3"><label for="email" class="form-label">Email (Login) *</label><input type="email" class="form-control" id="email" name="email" required></div>
                    <div class="mb-3"><label for="dni" class="form-label">DNI</label><input type="text" class="form-control" id="dni" name="dni" maxlength="8"></div>
                    <div class="mb-3"><label for="telefono" class="form-label">Teléfono</label><input type="text" class="form-control" id="telefono" name="telefono" maxlength="15"></div>
                    <div class="mb-3"><label for="password" class="form-label">Contraseña <small id="passHelpText" class="form-text text-muted"></small></label><input type="password" class="form-control" id="password" name="password"></div>
                    
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado *</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="">Seleccione el Estado</option>
                            <option value="A">Activo</option>
                            <option value="I">Inactivo</option> 
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_rol" class="form-label">Rol *</label>
                        <select class="form-control" id="id_rol" name="id_rol" required>
                            <option value="">Seleccione un Rol</option>
                            <?php 
                            $roles->data_seek(0);
                            while ($rol = $roles->fetch_assoc()) {
                                echo '<option value="' . $rol['id_rol'] . '">' . htmlspecialchars($rol['nombre_rol']) . '</option>'; 
                            }
                            ?>
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

<script src="../assets/js/usuarios.js"></script>

</body>
</html>