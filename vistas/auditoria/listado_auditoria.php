<?php
// vistas/auditoria/listado_auditoria.php
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi bi-clipboard-data me-2"></i> Auditoría
        </h3>
        <p class="text-muted small mb-0">
            Registro de acciones realizadas en el sistema: inicios de sesión, registros,
            actualizaciones de productos, compras, ventas y más.
        </p>
    </div>
</div>

<!-- FILTROS -->
<form class="card mb-3 shadow-sm border-0" method="get" action="panel_admin.php">
    <input type="hidden" name="modulo" value="auditoria">

    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label form-label-sm">Desde</label>
                <input type="date"
                       name="desde"
                       class="form-control form-control-sm"
                       value="<?php echo htmlspecialchars($fecha_desde); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label form-label-sm">Hasta</label>
                <input type="date"
                       name="hasta"
                       class="form-control form-control-sm"
                       value="<?php echo htmlspecialchars($fecha_hasta); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label form-label-sm">Módulo</label>
                <select name="modulo_filtro" class="form-select form-select-sm">
                    <?php foreach ($modulosDisponibles as $mod): ?>
                        <option value="<?php echo $mod; ?>"
                            <?php echo ($modulo === $mod) ? 'selected' : ''; ?>>
                            <?php echo ($mod === 'TODOS') ? 'Todos' : htmlspecialchars($mod); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label form-label-sm">Buscar (acción / descripción / tabla)</label>
                <input type="text"
                       name="texto"
                       class="form-control form-control-sm"
                       placeholder="Ej: REGISTRAR, LOGIN..."
                       value="<?php echo htmlspecialchars($texto); ?>">
            </div>
        </div>

        <div class="row g-3 align-items-end mt-1">
            <div class="col-md-3">
                <label class="form-label form-label-sm">Usuario</label>
                <input type="text"
                       name="usuario"
                       class="form-control form-control-sm"
                       placeholder="Nombre de usuario"
                       value="<?php echo htmlspecialchars($usuario); ?>">
            </div>

            <div class="col-md-9 text-md-end">
                <button type="submit" class="btn btn-primary btn-sm mt-2 mt-md-0">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                <a href="panel_admin.php?modulo=auditoria"
                   class="btn btn-outline-secondary btn-sm mt-2 mt-md-0 ms-1">
                    <i class="bi bi-x-circle me-1"></i> Limpiar
                </a>
            </div>
        </div>
    </div>
</form>

<!-- TABLA -->
<div class="card shadow-sm border-0">
    <div class="card-header py-2 small d-flex align-items-center">
        <i class="bi bi-activity me-2"></i>
        <span>Eventos de auditoría</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th style="width: 150px;">Fecha / hora</th>
                        <th style="width: 120px;">Usuario</th>
                        <th style="width: 120px;">Módulo</th>
                        <th style="width: 120px;">Acción</th>
                        <th>Descripción</th>
                        <th style="width: 140px;">Tabla</th>
                        <th style="width: 80px;" class="text-end">ID reg.</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($eventos)): ?>
                    <?php foreach ($eventos as $ev): ?>
                        <tr>
                            <td class="small">
                                <?php echo htmlspecialchars($ev['fecha_hora']); ?>
                            </td>
                            <td class="small">
                                <?php echo htmlspecialchars($ev['nombre_usuario'] ?? ''); ?>
                            </td>
                            <td class="small">
                                <?php echo htmlspecialchars($ev['modulo']); ?>
                            </td>
                            <td class="small">
                                <?php echo htmlspecialchars($ev['accion']); ?>
                            </td>
                            <td class="small">
                                <?php echo nl2br(htmlspecialchars($ev['descripcion'] ?? '')); ?>
                            </td>
                            <td class="small">
                                <?php echo htmlspecialchars($ev['tabla_afectada'] ?? ''); ?>
                            </td>
                            <td class="small text-end">
                                <?php echo htmlspecialchars($ev['id_registro_afectado'] ?? ''); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted small">
                            No se encontraron registros de auditoría con los filtros seleccionados.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
