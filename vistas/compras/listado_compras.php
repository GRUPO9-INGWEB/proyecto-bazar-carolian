<?php
// vistas/compras/listado_compras.php

$texto       = $_GET["buscar"] ?? "";
$orden       = $_GET["orden"] ?? "recientes";
$tipo_filtro = $_GET["tipo_filtro"] ?? "TODOS";
?>

<h3>Compras (ingreso de mercadería)</h3>
<p class="text-muted">
    En este módulo puede consultar las compras realizadas, filtrar por proveedor o comprobante
    y ordenar por fecha o monto.
</p>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <form class="d-flex flex-wrap gap-2" method="get" action="panel_admin.php">
        <input type="hidden" name="modulo" value="compras">
        <input type="hidden" name="accion" value="listar">

        <input type="text"
               name="buscar"
               class="form-control form-control-sm"
               style="min-width: 260px;"
               placeholder="Buscar por proveedor, documento o comprobante..."
               value="<?php echo htmlspecialchars($texto); ?>">

        <select name="tipo_filtro" class="form-select form-select-sm">
            <option value="TODOS"<?php echo $tipo_filtro === "TODOS" ? " selected" : ""; ?>>Todos los comprobantes</option>
            <option value="TICKET"<?php echo $tipo_filtro === "TICKET" ? " selected" : ""; ?>>Solo tickets</option>
            <option value="BOLETA"<?php echo $tipo_filtro === "BOLETA" ? " selected" : ""; ?>>Solo boletas</option>
            <option value="FACTURA"<?php echo $tipo_filtro === "FACTURA" ? " selected" : ""; ?>>Solo facturas</option>
        </select>

        <select name="orden" class="form-select form-select-sm">
            <option value="recientes"<?php echo $orden === "recientes" ? " selected" : ""; ?>>Más recientes primero</option>
            <option value="antiguas"<?php echo $orden === "antiguas" ? " selected" : ""; ?>>Más antiguas primero</option>
            <option value="monto_mayor"<?php echo $orden === "monto_mayor" ? " selected" : ""; ?>>Monto mayor primero</option>
            <option value="monto_menor"<?php echo $orden === "monto_menor" ? " selected" : ""; ?>>Monto menor primero</option>
        </select>

        <button class="btn btn-primary btn-sm" type="submit">Buscar</button>
        <a href="panel_admin.php?modulo=compras&accion=listar" class="btn btn-outline-secondary btn-sm">Limpiar</a>
    </form>

    <a href="panel_admin.php?modulo=compras&accion=nueva"
       class="btn btn-success btn-sm">
        Nueva compra
    </a>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info py-2"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Comprobante</th>
            <th>Proveedor</th>
            <th class="text-end">Subtotal</th>
            <th class="text-end">IGV</th>
            <th class="text-end">Total</th>
            <th>Estado</th>
            <th class="text-center">Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($lista_compras)): ?>
            <?php $i = 1; foreach ($lista_compras as $c): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($c["fecha_compra"]); ?></td>
                    <td>
                        <?php
                        echo htmlspecialchars(
                            $c["nombre_tipo"] . " " .
                            $c["serie_comprobante"] . "-" .
                            $c["numero_comprobante"]
                        );
                        ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($c["razon_social"]); ?><br>
                        <small class="text-muted">
                            <?php echo htmlspecialchars($c["numero_documento"]); ?>
                        </small>
                    </td>
                    <td class="text-end">S/ <?php echo number_format($c["subtotal"], 2); ?></td>
                    <td class="text-end">S/ <?php echo number_format($c["igv"], 2); ?></td>
                    <td class="text-end">S/ <?php echo number_format($c["total"], 2); ?></td>
                    <td>
                        <span class="badge bg-success">
                            <?php echo htmlspecialchars($c["estado"]); ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="panel_admin.php?modulo=compras&accion=ver&id=<?php echo (int)$c['id_compra']; ?>"
                           class="btn btn-sm btn-outline-primary">
                            Ver
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="text-center">No se han registrado compras aún.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
