<?php
// vistas/clientes/listado_clientes.php

if (!isset($mensaje)) {
    $mensaje = "";
}
if (!isset($buscar)) {
    $buscar = "";
}
if (!isset($orden)) {
    $orden = "DESC"; // por defecto: más recientes primero
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi bi-people me-2"></i>
            Clientes
        </h3>
        <p class="text-muted small mb-0">
            En este módulo puede registrar, editar y activar/desactivar clientes para su uso en boletas y facturas.
        </p>
    </div>

    <div class="mt-2 mt-md-0">
        <a href="panel_admin.php?modulo=clientes&accion=nuevo"
           class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i> Nuevo cliente
        </a>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info mb-3">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Barra de búsqueda + orden -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="get" action="panel_admin.php">
            <input type="hidden" name="modulo" value="clientes">
            <input type="hidden" name="accion" value="listar">

            <div class="col-md-5">
                <label class="form-label small text-muted mb-1">Buscar cliente</label>
                <input type="text"
                       name="buscar"
                       class="form-control"
                       placeholder="Nombre, RUC, DNI, razón social, correo..."
                       value="<?php echo htmlspecialchars($buscar); ?>">
            </div>

            <!-- Selector de orden -->
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

            <div class="col-md-3 d-flex gap-2">
                <div class="flex-fill">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> Buscar
                    </button>
                </div>
                <div class="flex-fill">
                    <a href="panel_admin.php?modulo=clientes&accion=listar"
                       class="btn btn-outline-secondary w-100">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-table">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listado de clientes</h5>
        <span class="text-muted extra-small">
            <?php echo !empty($lista_clientes) ? count($lista_clientes) . " registro(s)" : "Sin registros"; ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive table-wrapper-scroll">
            <table class="table table-striped table-sm align-middle mb-0 table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo doc.</th>
                        <th>N° documento</th>
                        <th>Nombres / Razón social</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <th style="width: 200px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($lista_clientes)): ?>
                    <?php foreach ($lista_clientes as $c): ?>
                        <tr>
                            <td><?php echo $c["id_cliente"]; ?></td>
                            <td>
                                <?php
                                $tipo = htmlspecialchars($c["tipo_documento"]);
                                $iconoTipo = "bi-person-badge";
                                if ($tipo === "RUC") $iconoTipo = "bi-building";
                                elseif ($tipo === "CE") $iconoTipo = "bi-passport";
                                ?>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi <?php echo $iconoTipo; ?> me-1"></i>
                                    <?php echo $tipo; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($c["numero_documento"]); ?></td>
                            <td>
                                <?php if (!empty($c["razon_social"])): ?>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($c["razon_social"]); ?></div>
                                    <?php if (!empty($c["nombres"]) || !empty($c["apellidos"])): ?>
                                        <div class="text-muted extra-small">
                                            <?php echo htmlspecialchars(trim($c["nombres"] . " " . $c["apellidos"])); ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo htmlspecialchars(trim($c["nombres"] . " " . $c["apellidos"])); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($c["telefono"]); ?></td>
                            <td><?php echo htmlspecialchars($c["correo"]); ?></td>
                            <td>
                                <?php if ($c["estado"] == 1): ?>
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
                                    <a href="panel_admin.php?modulo=clientes&accion=editar&id=<?php echo $c["id_cliente"]; ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> Editar
                                    </a>

                                    <?php if ($c["estado"] == 1): ?>
                                        <a href="panel_admin.php?modulo=clientes&accion=cambiar_estado&id=<?php echo $c["id_cliente"]; ?>&estado=0"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('¿Desactivar este cliente?');">
                                            <i class="bi bi-x-circle me-1"></i> Desactivar
                                        </a>
                                    <?php else: ?>
                                        <a href="panel_admin.php?modulo=clientes&accion=cambiar_estado&id=<?php echo $c["id_cliente"]; ?>&estado=1"
                                           class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-check-circle me-1"></i> Activar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            No hay clientes registrados.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
