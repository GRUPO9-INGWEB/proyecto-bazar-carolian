<?php
// Ubicación: vistas/usuarios.php
include_once "../conexion.php";
include_once "../modelos/usuario_modelo.php";

$usuarios = obtenerUsuarios($conexion);
$roles = obtenerRoles($conexion); 
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gestión de Usuarios</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-end">
            <button id="btn_nuevo_usuario" class="btn btn-primary" data-toggle="modal" data-target="#modalUsuario">
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        while ($usuario = $usuarios->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $usuario['id_usuario'] . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['nombre_completo']) . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['email']) . '</td>';
                            echo '<td>' . htmlspecialchars(ucfirst($usuario['rol'])) . '</td>'; 
                            echo '<td>';
                            echo '<button class="btn btn-warning btn-sm btn-editar mr-1" data-id="' . $usuario['id_usuario'] . '">Editar</button>';
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
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="formUsuario">
                <div class="modal-body">
                    <input type="hidden" id="id_usuario" name="id_usuario">
                    <input type="hidden" id="accion" name="accion" value="registrar">
                    
                    <div class="form-group"><label for="nombre_completo">Nombre Completo *</label><input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required></div>
                    <div class="form-group"><label for="email">Email (Login) *</label><input type="email" class="form-control" id="email" name="email" required></div>
                    <div class="form-group"><label for="dni">DNI</label><input type="text" class="form-control" id="dni" name="dni" maxlength="8"></div>
                    <div class="form-group"><label for="telefono">Teléfono</label><input type="text" class="form-control" id="telefono" name="telefono" maxlength="15"></div>
                    <div class="form-group"><label for="password">Contraseña <small id="passHelpText" class="form-text text-muted"></small></label><input type="password" class="form-control" id="password" name="password"></div>
                    
                    <div class="form-group">
                        <label for="id_rol">Rol *</label>
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
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../js/usuarios.js"></script>