<?php
// vistas/proveedores/listado_proveedores.php

if (!isset($alerta)) {
    $alerta = "";
}
if (!isset($texto_busqueda)) {
    $texto_busqueda = "";
}
if (!isset($proveedores)) {
    $proveedores = [];
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi bi-truck me-2"></i>
            Proveedores
        </h3>
        <p class="text-muted small mb-0">
            Gestión de proveedores: creación, edición y activación/desactivación para las compras de mercadería.
        </p>
    </div>

    <div class="mt-2 mt-md-0">
        <a href="panel_admin.php?modulo=proveedores&accion=nuevo"
           class="btn btn-primary btn-sm">
            <i class="bi bi-truck-front me-1"></i> Nuevo proveedor
        </a>
    </div>
</div>

<?php if (!empty($alerta)): ?>
    <div class="alert alert-info mb-3">
        <?php echo htmlspecialchars($alerta); ?>
    </div>
<?php endif; ?>

<!-- Barra de búsqueda -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="get" action="panel_admin.php">
            <input type="hidden" name="modulo" value="proveedores">
            <input type="hidden" name="accion" value="listar">

            <div class="col-md-8">
                <label class="form-label small text-muted mb-1">Buscar proveedor</label>
                <input
                    type="text"
                    class="form-control"
                    name="buscar"
                    placeholder="Razón social, contacto, RUC, DNI, correo..."
                    value="<?php echo htmlspecialchars($texto_busqueda); ?>"
                >
            </div>

            <div class="col-md-4 d-flex gap-2">
                <div class="flex-fill">
                    <button class="btn btn-outline-primary w-100" type="submit">
                        <i class="bi bi-search me-1"></i> Buscar
                    </button>
                </div>
                <div class="flex-fill">
                    <a href="panel_admin.php?modulo=proveedores&accion=listar"
                       class="btn btn-outline-secondary w-100">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de proveedores -->
<div class="card card-table">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listado de proveedores</h5>
        <span class="text-muted extra-small">
            <?php echo !empty($proveedores) ? count($proveedores) . " registro(s)" : "Sin registros"; ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive table-wrapper-scroll">
            <table class="table table-sm table-hover align-middle mb-0 table-modern">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Documento</th>
                        <th>Razón social</th>
                        <th>Contacto</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <th class="text-center" style="width: 210px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($proveedores)): ?>
                    <?php $i = 1; foreach ($proveedores as $p): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td>
                                <?php
                                $doc = trim(($p["tipo_documento"] ?? "") . " " . ($p["numero_documento"] ?? ""));
                                ?>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-file-earmark-text me-1"></i>
                                    <?php echo htmlspecialchars($doc); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($p["razon_social"]); ?></td>
                            <td>
                                <?php if (!empty($p["nombre_contacto"])): ?>
                                    <i class="bi bi-person me-1"></i>
                                    <?php echo htmlspecialchars($p["nombre_contacto"]); ?>
                                <?php else: ?>
                                    <span class="text-muted extra-small">Sin contacto</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($p["telefono"]); ?></td>
                            <td><?php echo htmlspecialchars($p["correo"]); ?></td>
                            <td>
                                <?php if ((int)$p["estado"] === 1): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> ACTIVO
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-slash-circle me-1"></i> INACTIVO
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-wrap justify-content-center gap-1">
                                    <a href="panel_admin.php?modulo=proveedores&accion=editar&id=<?php echo $p["id_proveedor"]; ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> Editar
                                    </a>
                                    <?php if ((int)$p["estado"] === 1): ?>
                                        <a href="panel_admin.php?modulo=proveedores&accion=cambiar_estado&id=<?php echo $p["id_proveedor"]; ?>&estado=0"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('¿Desactivar proveedor?');">
                                            <i class="bi bi-x-circle me-1"></i> Desactivar
                                        </a>
                                    <?php else: ?>
                                        <a href="panel_admin.php?modulo=proveedores&accion=cambiar_estado&id=<?php echo $p["id_proveedor"]; ?>&estado=1"
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
                            No hay proveedores registrados.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
