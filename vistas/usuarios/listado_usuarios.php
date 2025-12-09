<?php
// vistas/usuarios/listado_usuarios.php

if (!isset($mensaje)) $mensaje = "";
if (!isset($buscar))  $buscar  = "";
if (!isset($orden))   $orden   = "DESC";
?>
<h3>Gestión de usuarios</h3>
<p class="text-muted">
    En este módulo puede registrar, editar y desactivar usuarios del sistema.
</p>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Filtro de búsqueda + orden -->
<form class="row g-2 mb-3" method="get" action="panel_admin.php">
    <input type="hidden" name="modulo" value="usuarios">
    <input type="hidden" name="accion" value="listar">

    <!-- Búsqueda -->
    <div class="col-md-4">
        <input type="text"
               name="buscar"
               class="form-control"
               placeholder="Buscar por usuario, nombre, DNI, rol..."
               value="<?php echo htmlspecialchars($buscar); ?>">
    </div>

    <!-- Orden -->
    <div class="col-md-3">
        <select name="orden" class="form-select">
            <option value="DESC" <?php if ($orden === "DESC") echo "selected"; ?>>
                Mostrar primero los más recientes
            </option>
            <option value="ASC" <?php if ($orden === "ASC") echo "selected"; ?>>
                Mostrar primero los más antiguos
            </option>
        </select>
    </div>

    <!-- Botones -->
    <div class="col-md-3">
        <button type="submit" class="btn btn-outline-primary">
            Buscar
        </button>
        <a href="panel_admin.php?modulo=usuarios&accion=listar"
           class="btn btn-outline-secondary">
            Limpiar
        </a>
    </div>

    <!-- Nuevo usuario -->
    <div class="col-md-2 text-md-end mt-2 mt-md-0">
        <a href="panel_admin.php?modulo=usuarios&accion=nuevo" class="btn btn-primary btn-sm">
            Nuevo usuario
        </a>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-striped table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Nombres</th>
                <th>Rol</th>
                <th>DNI</th>
                <th>Correo</th>
                <th>Estado</th>
                <th style="width: 160px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($lista_usuarios)): ?>
            <?php foreach ($lista_usuarios as $u): ?>
                <tr>
                    <td><?php echo $u["id_usuario"]; ?></td>
                    <td><?php echo htmlspecialchars($u["nombre_usuario"]); ?></td>
                    <td><?php echo htmlspecialchars($u["nombres"] . " " . $u["apellidos"]); ?></td>
                    <td><?php echo htmlspecialchars($u["nombre_rol"]); ?></td>
                    <td><?php echo htmlspecialchars($u["dni"]); ?></td>
                    <td><?php echo htmlspecialchars($u["correo"]); ?></td>
                    <td>
                        <?php if ($u["estado"] == 1): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="panel_admin.php?modulo=usuarios&accion=editar&id=<?php echo $u["id_usuario"]; ?>"
                           class="btn btn-sm btn-outline-primary">
                            Editar
                        </a>

                        <?php if ($u["estado"] == 1): ?>
                            <a href="panel_admin.php?modulo=usuarios&accion=cambiar_estado&id=<?php echo $u["id_usuario"]; ?>&estado=0"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('¿Seguro que desea desactivar este usuario?');">
                                Desactivar
                            </a>
                        <?php else: ?>
                            <a href="panel_admin.php?modulo=usuarios&accion=cambiar_estado&id=<?php echo $u["id_usuario"]; ?>&estado=1"
                               class="btn btn-sm btn-outline-success">
                                Activar
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="8" class="text-center">No hay usuarios registrados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
