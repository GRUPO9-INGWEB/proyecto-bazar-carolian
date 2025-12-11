<?php
// vistas/proveedores/formulario_proveedor.php

// $modo: "nuevo" | "editar"
// $proveedor: array con datos o null
// $alerta: mensaje opcional

$esEdicion = ($modo === "editar");
$titulo = $esEdicion ? "Editar proveedor" : "Nuevo proveedor";
$accion = $esEdicion ? "guardar_edicion" : "guardar_nuevo";

if (!isset($alerta)) {
    $alerta = "";
}

$tipo_documento   = $proveedor["tipo_documento"]   ?? "RUC";
$numero_documento = $proveedor["numero_documento"] ?? "";
$razon_social     = $proveedor["razon_social"]     ?? "";
$nombre_contacto  = $proveedor["nombre_contacto"]  ?? "";
$telefono         = $proveedor["telefono"]         ?? "";
$correo           = $proveedor["correo"]           ?? "";
$direccion        = $proveedor["direccion"]        ?? "";
$estadoChecked    = ((int)($proveedor["estado"] ?? 1) === 1);

// Icono del título
$icono_titulo = $esEdicion ? "bi-pencil-square" : "bi-truck";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi <?php echo $icono_titulo; ?> me-2"></i>
            <?php echo $titulo; ?>
        </h3>
        <p class="text-muted small mb-0">
            <?php echo $esEdicion
                ? "Actualiza los datos del proveedor de la botica-bazar."
                : "Registra un nuevo proveedor para las compras de mercadería."; ?>
        </p>
    </div>

    <a href="panel_admin.php?modulo=proveedores&accion=listar"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
</div>

<?php if (!empty($alerta)): ?>
    <div class="alert alert-warning mb-3">
        <?php echo htmlspecialchars($alerta); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form method="post"
              action="panel_admin.php?modulo=proveedores&accion=<?php echo $accion; ?>"
              class="row g-3">

            <?php if ($esEdicion): ?>
                <input type="hidden" name="id_proveedor"
                       value="<?php echo (int)$proveedor["id_proveedor"]; ?>">
            <?php endif; ?>

            <!-- Tipo de documento -->
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Tipo de documento</label>
                <select name="tipo_documento" class="form-select">
                    <?php
                    $tipos = ["DNI", "RUC", "OTRO"];
                    foreach ($tipos as $t):
                    ?>
                        <option value="<?php echo $t; ?>"
                            <?php echo ($tipo_documento === $t) ? "selected" : ""; ?>>
                            <?php echo $t; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Número documento -->
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Número de documento</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-credit-card-2-front"></i>
                    </span>
                    <input type="text"
                           name="numero_documento"
                           class="form-control border-start-0"
                           autocomplete="off"
                           value="<?php echo htmlspecialchars($numero_documento); ?>">
                </div>
            </div>

            <!-- Razón social -->
            <div class="col-md-6">
                <label class="form-label small text-muted mb-1">Razón social</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-building"></i>
                    </span>
                    <input type="text"
                           name="razon_social"
                           class="form-control border-start-0"
                           required
                           value="<?php echo htmlspecialchars($razon_social); ?>">
                </div>
            </div>

            <!-- Nombre de contacto -->
            <div class="col-md-6">
                <label class="form-label small text-muted mb-1">Nombre de contacto</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text"
                           name="nombre_contacto"
                           class="form-control border-start-0"
                           value="<?php echo htmlspecialchars($nombre_contacto); ?>">
                </div>
            </div>

            <!-- Teléfono -->
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Teléfono</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-telephone"></i>
                    </span>
                    <input type="text"
                           name="telefono"
                           class="form-control border-start-0"
                           value="<?php echo htmlspecialchars($telefono); ?>">
                </div>
            </div>

            <!-- Correo -->
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Correo</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email"
                           name="correo"
                           class="form-control border-start-0"
                           value="<?php echo htmlspecialchars($correo); ?>">
                </div>
            </div>

            <!-- Dirección -->
            <div class="col-md-12">
                <label class="form-label small text-muted mb-1">Dirección</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-geo-alt"></i>
                    </span>
                    <input type="text"
                           name="direccion"
                           class="form-control border-start-0"
                           value="<?php echo htmlspecialchars($direccion); ?>">
                </div>
            </div>

            <!-- Estado -->
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1 d-block">Estado</label>
                <div class="form-check form-switch mt-1">
                    <input class="form-check-input"
                           type="checkbox"
                           role="switch"
                           name="estado"
                           id="chk_estado"
                           <?php echo $estadoChecked ? "checked" : ""; ?>>
                    <label class="form-check-label" for="chk_estado">
                        Proveedor activo
                    </label>
                </div>
            </div>

            <!-- Botones -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a href="panel_admin.php?modulo=proveedores&accion=listar"
                   class="btn btn-outline-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle me-1"></i> Guardar
                </button>
            </div>

        </form>
    </div>
</div>
