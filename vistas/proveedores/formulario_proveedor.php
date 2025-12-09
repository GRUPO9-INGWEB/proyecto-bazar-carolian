<?php
// vistas/proveedores/formulario_proveedor.php
?>
<h3>
    <?php echo ($modo === "editar") ? "Editar proveedor" : "Nuevo proveedor"; ?>
</h3>

<?php if (!empty($alerta)): ?>
    <div class="alert alert-warning alert-sm py-2">
        <?php echo htmlspecialchars($alerta); ?>
    </div>
<?php endif; ?>

<form method="post" action="panel_admin.php?modulo=proveedores&accion=<?php echo ($modo === 'editar') ? 'guardar_edicion' : 'guardar_nuevo'; ?>">

    <?php if ($modo === "editar"): ?>
        <input type="hidden" name="id_proveedor" value="<?php echo (int)$proveedor["id_proveedor"]; ?>">
    <?php endif; ?>

    <div class="row g-3">

        <div class="col-md-3">
            <label class="form-label">Tipo documento</label>
            <select name="tipo_documento" class="form-select form-select-sm">
                <?php
                $tipos = ["DNI", "RUC", "OTRO"];
                $selTipo = $proveedor["tipo_documento"] ?? "";
                foreach ($tipos as $t):
                ?>
                    <option value="<?php echo $t; ?>"
                        <?php echo ($selTipo === $t) ? "selected" : ""; ?>>
                        <?php echo $t; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Número documento</label>
            <input type="text" name="numero_documento"
                   class="form-control form-control-sm"
                   value="<?php echo htmlspecialchars($proveedor["numero_documento"] ?? ""); ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Razón social</label>
            <input type="text" name="razon_social"
                   class="form-control form-control-sm"
                   required
                   value="<?php echo htmlspecialchars($proveedor["razon_social"] ?? ""); ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Nombre de contacto</label>
            <input type="text" name="nombre_contacto"
                   class="form-control form-control-sm"
                   value="<?php echo htmlspecialchars($proveedor["nombre_contacto"] ?? ""); ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono"
                   class="form-control form-control-sm"
                   value="<?php echo htmlspecialchars($proveedor["telefono"] ?? ""); ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Correo</label>
            <input type="email" name="correo"
                   class="form-control form-control-sm"
                   value="<?php echo htmlspecialchars($proveedor["correo"] ?? ""); ?>">
        </div>

        <div class="col-md-12">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion"
                   class="form-control form-control-sm"
                   value="<?php echo htmlspecialchars($proveedor["direccion"] ?? ""); ?>">
        </div>

        <div class="col-md-3">
            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox"
                       name="estado" id="chk_estado"
                       <?php echo ((int)($proveedor["estado"] ?? 1) === 1) ? "checked" : ""; ?>>
                <label class="form-check-label" for="chk_estado">
                    Activo
                </label>
            </div>
        </div>

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary btn-sm">
            Guardar
        </button>
        <a href="panel_admin.php?modulo=proveedores&accion=listar"
           class="btn btn-secondary btn-sm">
            Cancelar
        </a>
    </div>

</form>
