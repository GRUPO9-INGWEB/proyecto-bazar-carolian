<?php
// vistas/clientes/formulario_cliente.php

$es_edicion = $cliente !== null;
$titulo = $es_edicion ? "Editar cliente" : "Nuevo cliente";
$accion_formulario = $es_edicion
    ? "panel_admin.php?modulo=clientes&accion=guardar_edicion"
    : "panel_admin.php?modulo=clientes&accion=guardar_nuevo";

if (!isset($mensaje)) {
    $mensaje = "";
}

$tipo_documento   = $es_edicion ? $cliente["tipo_documento"]   : "DNI";
$numero_documento = $es_edicion ? $cliente["numero_documento"] : "";
$nombres          = $es_edicion ? $cliente["nombres"]          : "";
$apellidos        = $es_edicion ? $cliente["apellidos"]        : "";
$razon_social     = $es_edicion ? $cliente["razon_social"]     : "";
$direccion        = $es_edicion ? $cliente["direccion"]        : "";
$correo           = $es_edicion ? $cliente["correo"]           : "";
$telefono         = $es_edicion ? $cliente["telefono"]         : "";

$esRUC   = ($tipo_documento === "RUC");
$textoLabelNumero = $esRUC ? "N° RUC" : "N° documento";

// Icono de título
$icono_titulo = $es_edicion ? "bi-pencil-square" : "bi-person-plus";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi <?php echo $icono_titulo; ?> me-2"></i>
            <?php echo $titulo; ?>
        </h3>
        <p class="text-muted small mb-0">
            <?php echo $es_edicion
                ? "Actualiza los datos del cliente para su uso en boletas y facturas."
                : "Registra un nuevo cliente para utilizarlo en las ventas (boletas y facturas)."; ?>
        </p>
    </div>

    <a href="panel_admin.php?modulo=clientes&accion=listar"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-warning mb-3">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="<?php echo $accion_formulario; ?>" method="post" class="row g-3">

            <?php if ($es_edicion): ?>
                <input type="hidden" name="id_cliente" value="<?php echo $cliente["id_cliente"]; ?>">
            <?php endif; ?>

            <!-- Tipo de documento -->
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Tipo de documento</label>
                <select name="tipo_documento"
                        id="tipo_documento"
                        class="form-select">
                    <option value="DNI" <?php if ($tipo_documento === "DNI") echo "selected"; ?>>DNI</option>
                    <option value="RUC" <?php if ($tipo_documento === "RUC") echo "selected"; ?>>RUC</option>
                    <option value="CE"  <?php if ($tipo_documento === "CE")  echo "selected"; ?>>Carné extranjería</option>
                    <option value="OTRO"<?php if ($tipo_documento === "OTRO")echo "selected"; ?>>Otro</option>
                </select>
            </div>

            <!-- Número de documento / RUC -->
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1" id="label_numero_documento">
                    <?php echo $textoLabelNumero; ?>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-credit-card-2-front"></i>
                    </span>
                    <input type="text"
                           name="numero_documento"
                           class="form-control border-start-0"
                           required
                           autocomplete="off"
                           value="<?php echo htmlspecialchars($numero_documento); ?>">
                </div>
            </div>

            <!-- Grupo nombres/apellidos (solo para DNI/CE/OTRO) -->
            <div class="col-md-3 <?php echo $esRUC ? 'd-none' : ''; ?>" id="grupo_nombres">
                <label class="form-label small text-muted mb-1">Nombres</label>
                <input type="text"
                       name="nombres"
                       id="input_nombres"
                       class="form-control"
                       <?php echo $esRUC ? '' : 'required'; ?>
                       value="<?php echo htmlspecialchars($nombres); ?>">
            </div>

            <div class="col-md-3 <?php echo $esRUC ? 'd-none' : ''; ?>" id="grupo_apellidos">
                <label class="form-label small text-muted mb-1">Apellidos</label>
                <input type="text"
                       name="apellidos"
                       id="input_apellidos"
                       class="form-control"
                       value="<?php echo htmlspecialchars($apellidos); ?>">
            </div>

            <!-- Razón social (solo RUC) -->
            <div class="col-md-6 <?php echo $esRUC ? '' : 'd-none'; ?>" id="grupo_razon_social">
                <label class="form-label small text-muted mb-1">Razón social (para RUC)</label>
                <input type="text"
                       name="razon_social"
                       id="razon_social"
                       class="form-control"
                       <?php echo $esRUC ? 'required' : ''; ?>
                       value="<?php echo htmlspecialchars($razon_social); ?>">
            </div>

            <!-- Dirección -->
            <div class="col-md-6">
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

            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a href="panel_admin.php?modulo=clientes&accion=listar"
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

<!-- Script para cambiar campos según tipo de documento -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectTipo   = document.getElementById("tipo_documento");
        const labelNumero  = document.getElementById("label_numero_documento");

        const grupoNombres   = document.getElementById("grupo_nombres");
        const grupoApellidos = document.getElementById("grupo_apellidos");
        const grupoRazon     = document.getElementById("grupo_razon_social");

        const inputNombres   = document.getElementById("input_nombres");
        const inputApellidos = document.getElementById("input_apellidos");
        const inputRazon     = document.getElementById("razon_social");

        function actualizarFormularioCliente() {
            if (selectTipo.value === "RUC") {
                // Etiqueta
                labelNumero.textContent = "N° RUC";

                // Mostrar razón social, ocultar nombres/apellidos
                grupoRazon.classList.remove("d-none");
                grupoNombres.classList.add("d-none");
                grupoApellidos.classList.add("d-none");

                // Requireds
                inputRazon.setAttribute("required", "required");
                inputNombres.removeAttribute("required");
            } else {
                // Etiqueta
                labelNumero.textContent = "N° documento";

                // Mostrar nombres/apellidos, ocultar razón social
                grupoRazon.classList.add("d-none");
                grupoNombres.classList.remove("d-none");
                grupoApellidos.classList.remove("d-none");

                // Requireds
                inputRazon.removeAttribute("required");
                inputNombres.setAttribute("required", "required");
            }
        }

        // Al cargar la página
        actualizarFormularioCliente();

        // Cada vez que cambie el tipo de documento
        selectTipo.addEventListener("change", actualizarFormularioCliente);
    });
</script>
