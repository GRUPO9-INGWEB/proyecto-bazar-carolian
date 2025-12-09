<?php
// vistas/proveedores/listado_proveedores.php
?>
<h3>Proveedores</h3>
<p class="text-muted">
    Gestión de proveedores: creación, edición y activación / desactivación.
</p>

<?php if (!empty($alerta)): ?>
    <div class="alert alert-info alert-sm py-2">
        <?php echo htmlspecialchars($alerta); ?>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="d-flex" method="get" action="panel_admin.php">
        <input type="hidden" name="modulo" value="proveedores">
        <input type="hidden" name="accion" value="listar">

        <input
            type="text"
            class="form-control form-control-sm me-2"
            name="buscar"
            placeholder="Buscar por razón social, contacto, documento..."
            value="<?php echo htmlspecialchars($texto_busqueda ?? ""); ?>"
        >
        <button class="btn btn-sm btn-primary me-2">Buscar</button>
        <a href="panel_admin.php?modulo=proveedores&accion=listar"
           class="btn btn-sm btn-outline-secondary">
            Limpiar
        </a>
    </form>

    <a href="panel_admin.php?modulo=proveedores&accion=nuevo"
       class="btn btn-sm btn-success">
        Nuevo proveedor
    </a>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Documento</th>
                <th>Razón social</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($proveedores)): ?>
            <?php $i = 1; foreach ($proveedores as $p): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td>
                        <?php
                        $doc = trim($p["tipo_documento"] . " " . $p["numero_documento"]);
                        echo htmlspecialchars($doc);
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($p["razon_social"]); ?></td>
                    <td><?php echo htmlspecialchars($p["nombre_contacto"]); ?></td>
                    <td><?php echo htmlspecialchars($p["telefono"]); ?></td>
                    <td><?php echo htmlspecialchars($p["correo"]); ?></td>
                    <td>
                        <?php if ((int)$p["estado"] === 1): ?>
                            <span class="badge bg-success">ACTIVO</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">INACTIVO</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="panel_admin.php?modulo=proveedores&accion=editar&id=<?php echo $p["id_proveedor"]; ?>"
                           class="btn btn-sm btn-outline-primary">
                            Editar
                        </a>
                        <?php if ((int)$p["estado"] === 1): ?>
                            <a href="panel_admin.php?modulo=proveedores&accion=cambiar_estado&id=<?php echo $p["id_proveedor"]; ?>&estado=0"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('¿Desactivar proveedor?');">
                                Desactivar
                            </a>
                        <?php else: ?>
                            <a href="panel_admin.php?modulo=proveedores&accion=cambiar_estado&id=<?php echo $p["id_proveedor"]; ?>&estado=1"
                               class="btn btn-sm btn-outline-success">
                                Activar
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center">
                    No hay proveedores registrados.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
