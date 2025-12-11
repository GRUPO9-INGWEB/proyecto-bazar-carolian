<?php
// vistas/productos/listado_productos.php

if (!isset($mensaje)) $mensaje = "";
if (!isset($buscar))  $buscar  = "";
if (!isset($orden))   $orden   = "DESC";

// Fecha actual para calcular días hasta la caducidad
$hoy = new DateTime();
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">
            <i class="bi bi-box-seam me-2"></i>
            Productos
        </h3>
        <p class="text-muted small mb-0">
            En este módulo puede registrar y editar los productos de la botica-bazar.
            El stock se actualizará automáticamente con las compras y ventas.
        </p>
    </div>

    <div class="mt-2 mt-md-0">
        <a href="panel_admin.php?modulo=productos&accion=nuevo"
           class="btn btn-primary btn-sm">
            <i class="bi bi-boxes me-1"></i> Nuevo producto
        </a>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info mb-3">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Filtro de búsqueda + orden -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="get" action="panel_admin.php">
            <input type="hidden" name="modulo" value="productos">
            <input type="hidden" name="accion" value="listar">

            <!-- Búsqueda -->
            <div class="col-md-5">
                <label class="form-label small text-muted mb-1">Buscar producto</label>
                <input type="text"
                       name="buscar"
                       class="form-control"
                       placeholder="Código, nombre, descripción o categoría..."
                       value="<?php echo htmlspecialchars($buscar); ?>">
            </div>

            <!-- Orden -->
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Ordenar por fecha de registro</label>
                <select name="orden" class="form-select">
                    <option value="DESC" <?php if ($orden === "DESC") echo "selected"; ?>>
                        Mostrar primero los más recientes
                    </option>
                    <option value="ASC" <?php if ($orden === "ASC") echo "selected"; ?>>
                        Mostrar primero los más antiguos
                    </option>
                </select>
            </div>

            <!-- Botones -->
            <div class="col-md-3 d-flex gap-2">
                <div class="flex-fill">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> Buscar
                    </button>
                </div>
                <div class="flex-fill">
                    <a href="panel_admin.php?modulo=productos&accion=listar"
                       class="btn btn-outline-secondary w-100">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de productos -->
<div class="card card-table">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listado de productos</h5>
        <span class="text-muted extra-small">
            <?php echo !empty($lista_productos) ? count($lista_productos) . " registro(s)" : "Sin registros"; ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-sm align-middle mb-0 table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Stock mínimo</th>
                        <th>F. caducidad</th>
                        <th>Precio compra</th>
                        <th>Precio venta</th>
                        <th>IGV</th>
                        <th>Estado</th>
                        <th style="width: 190px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($lista_productos)): ?>
                    <?php foreach ($lista_productos as $p): ?>
                        <tr>
                            <td><?php echo $p["id_producto"]; ?></td>
                            <td><?php echo htmlspecialchars($p["codigo_interno"]); ?></td>
                            <td><?php echo htmlspecialchars($p["nombre_producto"]); ?></td>
                            <td><?php echo htmlspecialchars($p["nombre_categoria"]); ?></td>
                            <td><?php echo $p["stock_actual"]; ?></td>
                            <td><?php echo $p["stock_minimo"]; ?></td>

                            <!-- Fecha de caducidad con colores -->
                            <td>
                                <?php if (empty($p["fecha_caducidad"])): ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-dash-circle me-1"></i> Sin fecha
                                    </span>
                                <?php else: ?>
                                    <?php
                                    $fechaV = new DateTime($p["fecha_caducidad"]);
                                    // %r incluye signo (+ / -), %a = días de diferencia
                                    $dias = (int)$hoy->diff($fechaV)->format('%r%a');

                                    if ($dias < 0) {
                                        // Ya vencido
                                        $clase = "bg-danger";
                                        $icono = "bi-x-octagon";
                                        $texto = "Vencido (" . $fechaV->format("d/m/Y") . ")";
                                    } elseif ($dias <= 30) {
                                        // Por vencer en 30 días o menos
                                        $clase = "bg-warning text-dark";
                                        $icono = "bi-exclamation-triangle";
                                        $texto = "Por vencer (" . $fechaV->format("d/m/Y") . ")";
                                    } else {
                                        // Ok
                                        $clase = "bg-success";
                                        $icono = "bi-check-circle";
                                        $texto = $fechaV->format("d/m/Y");
                                    }
                                    ?>
                                    <span class="badge <?php echo $clase; ?>">
                                        <i class="bi <?php echo $icono; ?> me-1"></i>
                                        <?php echo $texto; ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>S/ <?php echo number_format($p["precio_compra"], 2); ?></td>
                            <td>S/ <?php echo number_format($p["precio_venta"], 2); ?></td>

                            <td>
                                <?php if ($p["afecta_igv"] == 1): ?>
                                    <span class="badge bg-info text-dark">
                                        <i class="bi bi-receipt me-1"></i> Afecto
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-slash-circle me-1"></i> No afecta
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($p["estado"] == 1): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-slash-circle me-1"></i> Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="panel_admin.php?modulo=productos&accion=editar&id=<?php echo $p["id_producto"]; ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> Editar
                                    </a>

                                    <?php if ($p["estado"] == 1): ?>
                                        <a href="panel_admin.php?modulo=productos&accion=cambiar_estado&id=<?php echo $p["id_producto"]; ?>&estado=0"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('¿Desactivar este producto?');">
                                            <i class="bi bi-x-circle me-1"></i> Desactivar
                                        </a>
                                    <?php else: ?>
                                        <a href="panel_admin.php?modulo=productos&accion=cambiar_estado&id=<?php echo $p["id_producto"]; ?>&estado=1"
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
                        <td colspan="12" class="text-center py-4">
                            No hay productos registrados.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
