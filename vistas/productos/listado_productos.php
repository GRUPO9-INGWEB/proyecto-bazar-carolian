<?php
// vistas/productos/listado_productos.php

if (!isset($mensaje)) $mensaje = "";
if (!isset($buscar))  $buscar  = "";
if (!isset($orden))   $orden   = "DESC";

// Fecha actual para calcular días hasta la caducidad
$hoy = new DateTime();
?>
<h3>Productos</h3>
<p class="text-muted">
    En este módulo puede registrar y editar los productos de la botica-bazar.
    El stock se actualizará luego con las compras y ventas.
</p>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Filtro de búsqueda + orden -->
<form class="row g-2 mb-3" method="get" action="panel_admin.php">
    <input type="hidden" name="modulo" value="productos">
    <input type="hidden" name="accion" value="listar">

    <!-- Búsqueda -->
    <div class="col-md-4">
        <input type="text"
               name="buscar"
               class="form-control"
               placeholder="Buscar por código, nombre, descripción o categoría..."
               value="<?php echo htmlspecialchars($buscar); ?>">
    </div>

    <!-- Orden -->
    <div class="col-md-3">
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
    <div class="col-md-3">
        <button type="submit" class="btn btn-outline-primary">
            Buscar
        </button>
        <a href="panel_admin.php?modulo=productos&accion=listar"
           class="btn btn-outline-secondary">
            Limpiar
        </a>
    </div>

    <!-- Nuevo producto -->
    <div class="col-md-2 text-md-end mt-2 mt-md-0">
        <a href="panel_admin.php?modulo=productos&accion=nuevo" class="btn btn-primary btn-sm">
            Nuevo producto
        </a>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-striped table-sm align-middle">
        <thead class="table-light">
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
            <th style="width: 170px;">Acciones</th>
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
                            <span class="badge bg-secondary">Sin fecha</span>
                        <?php else: ?>
                            <?php
                            $fechaV = new DateTime($p["fecha_caducidad"]);
                            // %r incluye signo (+ / -), %a = días de diferencia
                            $dias = (int)$hoy->diff($fechaV)->format('%r%a');

                            if ($dias < 0) {
                                // Ya vencido
                                $clase = "bg-danger";
                                $texto = "Vencido (" . $fechaV->format("d/m/Y") . ")";
                            } elseif ($dias <= 30) {
                                // Por vencer en 30 días o menos
                                $clase = "bg-warning text-dark";
                                $texto = "Por vencer (" . $fechaV->format("d/m/Y") . ")";
                            } else {
                                // Ok
                                $clase = "bg-success";
                                $texto = $fechaV->format("d/m/Y");
                            }
                            ?>
                            <span class="badge <?php echo $clase; ?>">
                                <?php echo $texto; ?>
                            </span>
                        <?php endif; ?>
                    </td>

                    <td>S/ <?php echo number_format($p["precio_compra"], 2); ?></td>
                    <td>S/ <?php echo number_format($p["precio_venta"], 2); ?></td>

                    <td>
                        <?php if ($p["afecta_igv"] == 1): ?>
                            <span class="badge bg-info text-dark">Afecto</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">No afecta</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($p["estado"] == 1): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="panel_admin.php?modulo=productos&accion=editar&id=<?php echo $p["id_producto"]; ?>"
                           class="btn btn-sm btn-outline-primary">
                            Editar
                        </a>

                        <?php if ($p["estado"] == 1): ?>
                            <a href="panel_admin.php?modulo=productos&accion=cambiar_estado&id=<?php echo $p["id_producto"]; ?>&estado=0"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('¿Desactivar este producto?');">
                                Desactivar
                            </a>
                        <?php else: ?>
                            <a href="panel_admin.php?modulo=productos&accion=cambiar_estado&id=<?php echo $p["id_producto"]; ?>&estado=1"
                               class="btn btn-sm btn-outline-success">
                                Activar
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="12" class="text-center">No hay productos registrados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
