<?php
// vistas/auditoria/listado_auditoria.php
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3>Auditoría</h3>
        <p class="text-muted mb-0">
            Registro de acciones realizadas en el sistema (logins, ventas, productos, etc.).
        </p>
    </div>
</div>

<!-- FILTROS -->
<form class="card mb-3" method="get" action="panel_admin.php">
    <input type="hidden" name="modulo" value="auditoria">

    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date"
                       name="desde"
                       class="form-control"
                       value="<?php echo htmlspecialchars($fecha_desde); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date"
                       name="hasta"
                       class="form-control"
                       value="<?php echo htmlspecialchars($fecha_hasta); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Módulo</label>
                <select name="modulo_filtro" class="form-select">
                    <?php foreach ($modulosDisponibles as $mod): ?>
                        <option value="<?php echo $mod; ?>"
                            <?php echo ($modulo === $mod) ? 'selected' : ''; ?>>
                            <?php echo ($mod === 'TODOS') ? 'Todos' : htmlspecialchars($mod); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Buscar (acción / descripción / tabla)</label>
                <input type="text"
                       name="texto"
                       class="form-control"
                       placeholder="Ej: REGISTRAR, LOGIN..."
                       value="<?php echo htmlspecialchars($texto); ?>">
            </div>
        </div>

        <div class="row g-3 align-items-end mt-2">
            <div class="col-md-3">
                <label class="form-label">Usuario</label>
                <input type="text"
                       name="usuario"
                       class="form-control"
                       placeholder="nombre de usuario"
                       value="<?php echo htmlspecialchars($usuario); ?>">
            </div>

            <div class="col-md-9 text-end">
                <button type="submit" class="btn btn-primary mt-3">Filtrar</button>
                <a href="panel_admin.php?modulo=auditoria"
                   class="btn btn-outline-secondary mt-3">
                    Limpiar
                </a>
            </div>
        </div>
    </div>
</form>

<!-- TABLA -->
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 150px;">Fecha / hora</th>
                    <th style="width: 120px;">Usuario</th>
                    <th style="width: 110px;">Módulo</th>
                    <th style="width: 120px;">Acción</th>
                    <th>Descripción</th>
                    <th style="width: 130px;">Tabla</th>
                    <th style="width: 80px;">ID reg.</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($eventos)): ?>
                <?php foreach ($eventos as $ev): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ev['fecha_hora']); ?></td>
                        <td><?php echo htmlspecialchars($ev['nombre_usuario'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($ev['modulo']); ?></td>
                        <td><?php echo htmlspecialchars($ev['accion']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($ev['descripcion'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($ev['tabla_afectada'] ?? ''); ?></td>
                        <td class="text-end">
                            <?php echo htmlspecialchars($ev['id_registro_afectado'] ?? ''); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">
                        No se encontraron registros de auditoría con los filtros seleccionados.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
