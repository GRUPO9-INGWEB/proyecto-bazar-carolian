<?php
// vistas/usuarios/formulario_usuario.php

$es_edicion = $usuario !== null;
$titulo = $es_edicion ? "Editar usuario" : "Nuevo usuario";
$accion_formulario = $es_edicion
    ? "panel_admin.php?modulo=usuarios&accion=guardar_edicion"
    : "panel_admin.php?modulo=usuarios&accion=guardar_nuevo";

// Icono del título según sea nuevo / edición
$icono_titulo = $es_edicion ? "bi-pencil-square" : "bi-person-plus";

if (!isset($mensaje)) {
    $mensaje = "";
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi <?php echo $icono_titulo; ?> me-2"></i>
            <?php echo $titulo; ?>
        </h3>
        <p class="text-muted small mb-0">
            <?php echo $es_edicion
                ? "Actualiza los datos del usuario y su rol dentro del sistema."
                : "Registra un nuevo usuario para el sistema de inventario y ventas."; ?>
        </p>
    </div>

    <a href="panel_admin.php?modulo=usuarios&accion=listar"
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
                <input type="hidden" name="id_usuario" value="<?php echo $usuario["id_usuario"]; ?>">
            <?php endif; ?>

            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Rol</label>
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
                <label class="form-label small text-muted mb-1">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text"
                           name="nombre_usuario"
                           class="form-control border-start-0"
                           required
                           autocomplete="off"
                           value="<?php echo $es_edicion ? htmlspecialchars($usuario["nombre_usuario"]) : ""; ?>">
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">
                    <?php echo $es_edicion ? "Nueva contraseña (opcional)" : "Contraseña"; ?>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input
                        type="password"
                        id="campo_clave_usuario"
                        name="<?php echo $es_edicion ? 'clave_nueva' : 'clave'; ?>"
                        class="form-control border-start-0"
                        <?php echo $es_edicion ? "" : "required"; ?>
                    >
                    <button type="button"
                            class="btn btn-outline-secondary border-start-0"
                            onclick="toggleClave('campo_clave_usuario', this)"
                            aria-label="Mostrar contraseña">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label small text-muted mb-1">Nombres</label>
                <input type="text" name="nombres" class="form-control" required
                       value="<?php echo $es_edicion ? htmlspecialchars($usuario["nombres"]) : ""; ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label small text-muted mb-1">Apellidos</label>
                <input type="text" name="apellidos" class="form-control" required
                       value="<?php echo $es_edicion ? htmlspecialchars($usuario["apellidos"]) : ""; ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">DNI</label>
                <input type="text" name="dni" class="form-control"
                       value="<?php echo $es_edicion ? htmlspecialchars($usuario["dni"]) : ""; ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Correo</label>
                <input type="email" name="correo" class="form-control"
                       value="<?php echo $es_edicion ? htmlspecialchars($usuario["correo"]) : ""; ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Teléfono</label>
                <input type="text" name="telefono" class="form-control"
                       value="<?php echo $es_edicion ? htmlspecialchars($usuario["telefono"]) : ""; ?>">
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a href="panel_admin.php?modulo=usuarios&accion=listar"
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

<script>
function toggleClave(idCampo, boton) {
    const input = document.getElementById(idCampo);
    if (!input) return;

    const icon = boton.querySelector('i');

    if (input.type === "password") {
        input.type = "text";
        if (icon) {
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        }
        boton.setAttribute("aria-label", "Ocultar contraseña");
    } else {
        input.type = "password";
        if (icon) {
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
        boton.setAttribute("aria-label", "Mostrar contraseña");
    }
}
</script>
