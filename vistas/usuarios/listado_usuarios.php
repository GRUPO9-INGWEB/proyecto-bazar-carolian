<?php
// vistas/usuarios/listado_usuarios.php

if (!isset($mensaje)) $mensaje = "";
if (!isset($buscar))  $buscar  = "";
if (!isset($orden))   $orden   = "DESC";
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi bi-people me-2"></i>
            Gestión de usuarios
        </h3>
        <p class="text-muted small mb-0">
            En este módulo puede registrar, editar y activar/desactivar usuarios del sistema.
        </p>
    </div>

    <div class="mt-2 mt-md-0">
        <a href="panel_admin.php?modulo=usuarios&accion=nuevo"
           class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i> Nuevo usuario
        </a>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info mb-3">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Filtro de búsqueda + orden -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="get" action="panel_admin.php">
            <input type="hidden" name="modulo" value="usuarios">
            <input type="hidden" name="accion" value="listar">

            <!-- Búsqueda -->
            <div class="col-md-5">
                <label class="form-label small text-muted mb-1">Buscar</label>
                <input type="text"
                       name="buscar"
                       class="form-control"
                       placeholder="Usuario, nombre, DNI, rol..."
                       value="<?php echo htmlspecialchars($buscar); ?>">
            </div>

            <!-- Orden -->
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Ordenar por fecha de registro</label>
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
            <div class="col-md-3 d-flex gap-2">
                <div class="flex-fill">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> Buscar
                    </button>
                </div>
                <div class="flex-fill">
                    <a href="panel_admin.php?modulo=usuarios&accion=listar"
                       class="btn btn-outline-secondary w-100">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de usuarios -->
<div class="card card-table">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listado de usuarios</h5>
        <span class="text-muted extra-small">
            <?php echo !empty($lista_usuarios) ? count($lista_usuarios) . " registro(s)" : "Sin registros"; ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-sm align-middle mb-0 table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombres</th>
                        <th>Rol</th>
                        <th>DNI</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <th style="width: 190px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($lista_usuarios)): ?>
                    <?php foreach ($lista_usuarios as $u): ?>
                        <tr>
                            <td><?php echo $u["id_usuario"]; ?></td>
                            <td><?php echo htmlspecialchars($u["nombre_usuario"]); ?></td>
                            <td><?php echo htmlspecialchars($u["nombres"] . " " . $u["apellidos"]); ?></td>
                            <td>
                                <?php if ($u["nombre_rol"] === "ADMINISTRADORA"): ?>
                                    <i class="bi bi-shield-lock me-1"></i>
                                <?php else: ?>
                                    <i class="bi bi-person-badge me-1"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($u["nombre_rol"]); ?>
                            </td>
                            <td><?php echo htmlspecialchars($u["dni"]); ?></td>
                            <td><?php echo htmlspecialchars($u["correo"]); ?></td>
                            <td>
                                <?php if ($u["estado"] == 1): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-slash-circle me-1"></i> Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="panel_admin.php?modulo=usuarios&accion=editar&id=<?php echo $u["id_usuario"]; ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> Editar
                                    </a>

                                    <?php if ($u["estado"] == 1): ?>
                                        <a href="panel_admin.php?modulo=usuarios&accion=cambiar_estado&id=<?php echo $u["id_usuario"]; ?>&estado=0"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('¿Seguro que desea desactivar este usuario?');">
                                            <i class="bi bi-person-x me-1"></i> Desactivar
                                        </a>
                                    <?php else: ?>
                                        <a href="panel_admin.php?modulo=usuarios&accion=cambiar_estado&id=<?php echo $u["id_usuario"]; ?>&estado=1"
                                           class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-person-check me-1"></i> Activar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
