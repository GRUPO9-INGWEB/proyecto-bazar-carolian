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
<h3>Clientes</h3>
<p class="text-muted">
    En este módulo puede registrar, editar y activar/desactivar clientes
    para las boletas y facturas.
</p>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Barra de búsqueda + orden -->
<form class="row g-2 mb-3" method="get" action="panel_admin.php">
    <input type="hidden" name="modulo" value="clientes">
    <input type="hidden" name="accion" value="listar">

    <div class="col-md-4">
        <input type="text"
               name="buscar"
               class="form-control"
               placeholder="Buscar por nombre, RUC, DNI, razón social, correo..."
               value="<?php echo htmlspecialchars($buscar); ?>">
    </div>

    <!-- Selector de orden -->
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

    <div class="col-md-3">
        <button type="submit" class="btn btn-outline-primary">
            Buscar
        </button>
        <a href="panel_admin.php?modulo=clientes&accion=listar" class="btn btn-outline-secondary">
            Limpiar
        </a>
    </div>

    <div class="col-md-2 text-md-end mt-2 mt-md-0">
        <a href="panel_admin.php?modulo=clientes&accion=nuevo" class="btn btn-primary btn-sm">
            Nuevo cliente
        </a>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-striped table-sm align-middle">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Tipo doc.</th>
            <th>N° documento</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Razón social</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Estado</th>
            <th style="width: 180px;">Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($lista_clientes)): ?>
            <?php foreach ($lista_clientes as $c): ?>
                <tr>
                    <td><?php echo $c["id_cliente"]; ?></td>
                    <td><?php echo htmlspecialchars($c["tipo_documento"]); ?></td>
                    <td><?php echo htmlspecialchars($c["numero_documento"]); ?></td>
                    <td><?php echo htmlspecialchars($c["nombres"]); ?></td>
                    <td><?php echo htmlspecialchars($c["apellidos"]); ?></td>
                    <td><?php echo htmlspecialchars($c["razon_social"]); ?></td>
                    <td><?php echo htmlspecialchars($c["telefono"]); ?></td>
                    <td><?php echo htmlspecialchars($c["correo"]); ?></td>
                    <td>
                        <?php if ($c["estado"] == 1): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="panel_admin.php?modulo=clientes&accion=editar&id=<?php echo $c["id_cliente"]; ?>"
                           class="btn btn-sm btn-outline-primary">
                            Editar
                        </a>

                        <?php if ($c["estado"] == 1): ?>
                            <a href="panel_admin.php?modulo=clientes&accion=cambiar_estado&id=<?php echo $c["id_cliente"]; ?>&estado=0"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('¿Desactivar este cliente?');">
                                Desactivar
                            </a>
                        <?php else: ?>
                            <a href="panel_admin.php?modulo=clientes&accion=cambiar_estado&id=<?php echo $c["id_cliente"]; ?>&estado=1"
                               class="btn btn-sm btn-outline-success">
                                Activar
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="10" class="text-center">No hay clientes registrados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
