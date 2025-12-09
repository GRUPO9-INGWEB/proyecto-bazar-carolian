<?php
// vistas/usuarios/formulario_usuario.php

$es_edicion = $usuario !== null;
$titulo = $es_edicion ? "Editar usuario" : "Nuevo usuario";
$accion_formulario = $es_edicion
    ? "panel_admin.php?modulo=usuarios&accion=guardar_edicion"
    : "panel_admin.php?modulo=usuarios&accion=guardar_nuevo";

if (!isset($mensaje)) {
    $mensaje = "";
}
?>
<h3><?php echo $titulo; ?></h3>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-warning">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<form action="<?php echo $accion_formulario; ?>" method="post" class="row g-3">

    <?php if ($es_edicion): ?>
        <input type="hidden" name="id_usuario" value="<?php echo $usuario["id_usuario"]; ?>">
    <?php endif; ?>

    <div class="col-md-4">
        <label class="form-label">Rol</label>
        <select name="id_rol" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php foreach ($lista_roles as $rol): ?>
                <option value="<?php echo $rol["id_rol"]; ?>"
                    <?php
                    if ($es_edicion && $usuario["id_rol"] == $rol["id_rol"]) {
                        echo "selected";
                    }
                    ?>>
                    <?php echo htmlspecialchars($rol["nombre_rol"]); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Usuario</label>
        <input type="text" name="nombre_usuario" class="form-control" required
               value="<?php echo $es_edicion ? htmlspecialchars($usuario["nombre_usuario"]) : ""; ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label">
            <?php echo $es_edicion ? "Nueva contraseña (opcional)" : "Contraseña"; ?>
        </label>
        <div class="input-group">
            <input
                type="password"
                id="campo_clave_usuario"
                name="<?php echo $es_edicion ? 'clave_nueva' : 'clave'; ?>"
                class="form-control"
                <?php echo $es_edicion ? "" : "required"; ?>
            >
            <button type="button"
                    class="btn btn-outline-secondary"
                    onclick="toggleClave('campo_clave_usuario', this)">
                Ver
            </button>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Nombres</label>
        <input type="text" name="nombres" class="form-control" required
               value="<?php echo $es_edicion ? htmlspecialchars($usuario["nombres"]) : ""; ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Apellidos</label>
        <input type="text" name="apellidos" class="form-control" required
               value="<?php echo $es_edicion ? htmlspecialchars($usuario["apellidos"]) : ""; ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label">DNI</label>
        <input type="text" name="dni" class="form-control"
               value="<?php echo $es_edicion ? htmlspecialchars($usuario["dni"]) : ""; ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label">Correo</label>
        <input type="email" name="correo" class="form-control"
               value="<?php echo $es_edicion ? htmlspecialchars($usuario["correo"]) : ""; ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control"
               value="<?php echo $es_edicion ? htmlspecialchars($usuario["telefono"]) : ""; ?>">
    </div>

    <div class="col-12">
        <a href="panel_admin.php?modulo=usuarios&accion=listar" class="btn btn-secondary">
            Volver
        </a>
        <button type="submit" class="btn btn-primary">
            Guardar
        </button>
    </div>
</form>

<script>
function toggleClave(idCampo, boton) {
    const input = document.getElementById(idCampo);
    if (!input) return;

    if (input.type === "password") {
        input.type = "text";
        boton.textContent = "Ocultar";
    } else {
        input.type = "password";
        boton.textContent = "Ver";
    }
}
</script>
