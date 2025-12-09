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
?>
<h3><?php echo $titulo; ?></h3>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-warning">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<form action="<?php echo $accion_formulario; ?>" method="post" class="row g-3">

    <?php if ($es_edicion): ?>
        <input type="hidden" name="id_cliente" value="<?php echo $cliente["id_cliente"]; ?>">
    <?php endif; ?>

    <!-- Tipo de documento -->
    <div class="col-md-3">
        <label class="form-label">Tipo de documento</label>
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
        <label class="form-label" id="label_numero_documento">
            <?php echo $textoLabelNumero; ?>
        </label>
        <input type="text"
               name="numero_documento"
               class="form-control"
               required
               value="<?php echo htmlspecialchars($numero_documento); ?>">
    </div>

    <!-- Grupo nombres/apellidos (solo para DNI/CE/OTRO) -->
    <div class="col-md-3 <?php echo $esRUC ? 'd-none' : ''; ?>" id="grupo_nombres">
        <label class="form-label">Nombres</label>
        <input type="text"
               name="nombres"
               id="input_nombres"
               class="form-control"
               <?php echo $esRUC ? '' : 'required'; ?>
               value="<?php echo htmlspecialchars($nombres); ?>">
    </div>

    <div class="col-md-3 <?php echo $esRUC ? 'd-none' : ''; ?>" id="grupo_apellidos">
        <label class="form-label">Apellidos</label>
        <input type="text"
               name="apellidos"
               id="input_apellidos"
               class="form-control"
               value="<?php echo htmlspecialchars($apellidos); ?>">
    </div>

    <!-- Razón social (solo RUC) -->
    <div class="col-md-6 <?php echo $esRUC ? '' : 'd-none'; ?>" id="grupo_razon_social">
        <label class="form-label">Razón social (para RUC)</label>
        <input type="text"
               name="razon_social"
               id="razon_social"
               class="form-control"
               <?php echo $esRUC ? 'required' : ''; ?>
               value="<?php echo htmlspecialchars($razon_social); ?>">
    </div>

    <!-- Dirección -->
    <div class="col-md-6">
        <label class="form-label">Dirección</label>
        <input type="text"
               name="direccion"
               class="form-control"
               value="<?php echo htmlspecialchars($direccion); ?>">
    </div>

    <!-- Correo -->
    <div class="col-md-3">
        <label class="form-label">Correo</label>
        <input type="email"
               name="correo"
               class="form-control"
               value="<?php echo htmlspecialchars($correo); ?>">
    </div>

    <!-- Teléfono -->
    <div class="col-md-3">
        <label class="form-label">Teléfono</label>
        <input type="text"
               name="telefono"
               class="form-control"
               value="<?php echo htmlspecialchars($telefono); ?>">
    </div>

    <div class="col-12">
        <a href="panel_admin.php?modulo=clientes&accion=listar" class="btn btn-secondary">
            Volver
        </a>
        <button type="submit" class="btn btn-primary">
            Guardar
        </button>
    </div>
</form>

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
