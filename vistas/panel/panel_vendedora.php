<?php
// vistas/panel/panel_vendedora.php

session_start();

// Solo la vendedora puede entrar aquí
if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../../index.php");
    exit;
}

// Si es administradora, que vaya a su panel
if ($_SESSION["nombre_rol"] === "ADMINISTRADORA") {
    header("Location: panel_admin.php");
    exit;
}

// Cualquier otro rol que no sea vendedora, fuera
if ($_SESSION["nombre_rol"] !== "VENDEDORA") {
    header("Location: ../../index.php");
    exit;
}

// Parámetros para saber qué módulo y acción mostrar
$modulo = $_GET["modulo"] ?? "inicio";
$accion = $_GET["accion"] ?? "listar";

/* ============================================================
   CASOS ESPECIALES: PDFs (SIN PLANTILLAS HTML)
   ============================================================ */

// Comprobante individual de ventas
if ($modulo === "ventas" && $accion === "imprimir_pdf") {
    require_once __DIR__ . "/../../controladores/VentaControlador.php";
    $controladorVentas = new VentaControlador();

    $id = (int)($_GET["id"] ?? 0);
    $controladorVentas->imprimirPdf($id);
    exit;
}

// Reportes en PDF (ventas, productos vendidos, compras, categorías)
if (
    $modulo === "reportes"
    && in_array($accion, ["ventas_pdf", "productos_pdf", "compras_pdf", "categorias_pdf"], true)
) {
    require_once __DIR__ . "/../../controladores/ReporteControlador.php";
    $controladorReportes = new ReporteControlador();

    switch ($accion) {
        case "ventas_pdf":
            $controladorReportes->pdfVentas();
            break;
        case "productos_pdf":
            $controladorReportes->pdfProductos();
            break;
        case "compras_pdf":
            $controladorReportes->pdfCompras();
            break;
        case "categorias_pdf":
            $controladorReportes->pdfCategorias();
            break;
    }
    exit;
}

// PDF de caja / cierre diario
if ($modulo === "caja" && $accion === "cierre_pdf") {
    require_once __DIR__ . "/../../controladores/CajaControlador.php";
    $controladorCaja = new CajaControlador();
    $controladorCaja->pdfCierre();
    exit;
}

// Encabezado y menú común
include __DIR__ . "/../plantillas/encabezado.php";
include __DIR__ . "/../plantillas/menu_vendedora.php";
?>

<?php if ($modulo === "inicio"): ?>

    <?php
    // Cargamos datos para las alertas de stock y caducidad
    require_once __DIR__ . "/../../modelos/ProductoModelo.php";
    $modeloProductos = new ProductoModelo();

    $productosStockBajo  = $modeloProductos->obtenerProductosStockBajo();
    $productosPorVencer  = $modeloProductos->obtenerProductosPorVencer(30);
    $productosVencidos   = $modeloProductos->obtenerProductosVencidos();

    $totalStockBajo = count($productosStockBajo);
    $totalPorVencer = count($productosPorVencer);
    $totalVencidos  = count($productosVencidos);
    ?>

    <!-- Título y descripción -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">Panel de vendedora</h3>
            <p class="text-muted mb-0 small">
                Resumen de inventario para las ventas diarias: stock, caducidad y accesos rápidos a los módulos.
            </p>
        </div>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-metric h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="card-metric-title">Stock bajo</div>
                            <div class="card-metric-value">
                                <?php echo $totalStockBajo; ?>
                            </div>
                        </div>
                        <div class="card-metric-icon">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        Productos con stock actual ≤ stock mínimo.
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-metric h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="card-metric-title">Por vencer (≤ 30 días)</div>
                            <div class="card-metric-value">
                                <?php echo $totalPorVencer; ?>
                            </div>
                        </div>
                        <div class="card-metric-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        Según fecha de caducidad registrada.
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-metric h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="card-metric-title">Productos vencidos</div>
                            <div class="card-metric-value">
                                <?php echo $totalVencidos; ?>
                            </div>
                        </div>
                        <div class="card-metric-icon">
                            <i class="bi bi-x-octagon"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        No deberían venderse.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Listados rápidos -->
    <div class="row g-3">
        <!-- Stock bajo -->
        <div class="col-md-6">
            <div class="card card-table h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Productos con stock bajo</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0 table-modern">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Stock</th>
                                    <th>Stock mínimo</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($totalStockBajo > 0): ?>
                                <?php foreach (array_slice($productosStockBajo, 0, 5) as $p): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p["nombre_producto"]); ?></td>
                                        <td><?php echo htmlspecialchars($p["nombre_categoria"]); ?></td>
                                        <td><?php echo $p["stock_actual"]; ?></td>
                                        <td><?php echo $p["stock_minimo"]; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">
                                        No hay productos con stock bajo.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Por vencer / vencidos -->
        <div class="col-md-6">
            <div class="card card-table h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Productos por vencer / vencidos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0 table-modern">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>F. caducidad</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $hoy = new DateTime();
                            $listaCaducidad = array_merge($productosPorVencer, $productosVencidos);
                            if (!empty($listaCaducidad)):
                                foreach ($listaCaducidad as $p):
                                    $fechaV = new DateTime($p["fecha_caducidad"]);
                                    $dias = (int)$hoy->diff($fechaV)->format('%r%a');
                                    if ($dias < 0) {
                                        $textoEstado = "Vencido";
                                        $clase = "badge bg-danger";
                                    } elseif ($dias <= 30) {
                                        $textoEstado = "Por vencer";
                                        $clase = "badge bg-warning text-dark";
                                    } else {
                                        $textoEstado = "Vigente";
                                        $clase = "badge bg-success";
                                    }
                            ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p["nombre_producto"]); ?></td>
                                        <td><?php echo $fechaV->format("d/m/Y"); ?></td>
                                        <td><span class="<?php echo $clase; ?>"><?php echo $textoEstado; ?></span></td>
                                    </tr>
                            <?php
                                endforeach;
                            else:
                            ?>
                                <tr>
                                    <td colspan="3" class="text-center">
                                        No hay productos próximos a vencer.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos rápidos (sin módulo Usuarios) -->
    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="card-title mb-1">Productos</h6>
                            <p class="card-text small text-muted mb-0">
                                Control de inventario, precios y stock.
                            </p>
                        </div>
                        <span class="card-metric-icon">
                            <i class="bi bi-box-seam"></i>
                        </span>
                    </div>
                    <a href="panel_vendedora.php?modulo=productos&accion=listar"
                       class="btn btn-sm btn-outline-primary mt-2">
                        Ir
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="card-title mb-1">Ventas</h6>
                            <p class="card-text small text-muted mb-0">
                                Registro de ventas y comprobantes.
                            </p>
                        </div>
                        <span class="card-metric-icon">
                            <i class="bi bi-cash-stack"></i>
                        </span>
                    </div>
                    <a href="panel_vendedora.php?modulo=ventas&accion=listar"
                       class="btn btn-sm btn-outline-primary mt-2">
                        Ir
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="card-title mb-1">Reportes</h6>
                            <p class="card-text small text-muted mb-0">
                                Análisis de ventas y productos.
                            </p>
                        </div>
                        <span class="card-metric-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </span>
                    </div>
                    <a href="panel_vendedora.php?modulo=reportes&accion=listar"
                       class="btn btn-sm btn-outline-primary mt-2">
                        Ir
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($modulo === "productos"): ?>


    <?php
    require_once __DIR__ . "/../../controladores/ProductoControlador.php";
    $controladorProductos = new ProductoControlador();

    switch ($accion) {
        case "nuevo":
            $controladorProductos->formularioNuevo();
            break;
        case "guardar_nuevo":
            $controladorProductos->guardarNuevo();
            break;
        case "editar":
            $id = intval($_GET["id"] ?? 0);
            $controladorProductos->formularioEditar($id);
            break;
        case "guardar_edicion":
            $controladorProductos->guardarEdicion();
            break;
        case "cambiar_estado":
            $id = intval($_GET["id"] ?? 0);
            $estado = intval($_GET["estado"] ?? 0);
            $controladorProductos->cambiarEstado($id, $estado);
            break;
        default:
            $controladorProductos->listar();
            break;
    }
    ?>

<?php elseif ($modulo === "categorias"): ?>

    <?php
    require_once __DIR__ . "/../../controladores/CategoriaControlador.php";
    $controladorCategorias = new CategoriaControlador();

    switch ($accion) {
        case "nuevo":
            $controladorCategorias->formularioNuevo();
            break;
        case "guardar_nuevo":
            $controladorCategorias->guardarNuevo();
            break;
        case "editar":
            $id = intval($_GET["id"] ?? 0);
            $controladorCategorias->formularioEditar($id);
            break;
        case "guardar_edicion":
            $controladorCategorias->guardarEdicion();
            break;
        case "cambiar_estado":
            $id = intval($_GET["id"] ?? 0);
            $estado = intval($_GET["estado"] ?? 0);
            $controladorCategorias->cambiarEstado($id, $estado);
            break;
        default:
            $controladorCategorias->listar();
            break;
    }
    ?>

<?php elseif ($modulo === "clientes"): ?>

    <?php
    require_once __DIR__ . "/../../controladores/ClienteControlador.php";
    $controladorClientes = new ClienteControlador();

    switch ($accion) {
        case "nuevo":
            $controladorClientes->formularioNuevo();
            break;
        case "guardar_nuevo":
            $controladorClientes->guardarNuevo();
            break;
        case "editar":
            $id = intval($_GET["id"] ?? 0);
            $controladorClientes->formularioEditar($id);
            break;
        case "guardar_edicion":
            $controladorClientes->guardarEdicion();
            break;
        case "cambiar_estado":
            $id = intval($_GET["id"] ?? 0);
            $estado = intval($_GET["estado"] ?? 0);
            $controladorClientes->cambiarEstado($id, $estado);
            break;
        default:
            $controladorClientes->listar();
            break;
    }
    ?>

<?php elseif ($modulo === "ventas"): ?>

    <?php
    require_once __DIR__ . "/../../controladores/VentaControlador.php";
    $controladorVentas = new VentaControlador();

    switch ($accion) {
        case "nueva":
            $controladorVentas->formularioNueva();
            break;
        case "guardar_nueva":
            $controladorVentas->guardarNueva();
            break;
        case "ver":
            $id = intval($_GET["id"] ?? 0);
            $controladorVentas->ver($id);
            break;
        case "enviar_correo":
            $id = (int)($_GET["id"] ?? 0);
            $controladorVentas->enviarCorreo($id);
            break;
        default:
            $controladorVentas->listar();
            break;
    }
    ?>

<?php elseif ($modulo === "compras"): ?>

    <?php
    require_once __DIR__ . "/../../controladores/CompraControlador.php";
    $controladorCompras = new CompraControlador();

    switch ($accion) {
        case "nueva":
            $controladorCompras->formularioNueva();
            break;
        case "guardar_nueva":
            $controladorCompras->guardarNueva();
            break;
        case "ver":
            $id = intval($_GET["id"] ?? 0);
            $controladorCompras->ver($id);
            break;
        default:
            $controladorCompras->listar();
            break;
    }
    ?>

<?php elseif ($modulo === "reportes"): ?>

    <?php
    require_once __DIR__ . "/../../controladores/ReporteControlador.php";
    $controladorReportes = new ReporteControlador();

    switch ($accion) {
        case "listar":
        case "ventas":
            $controladorReportes->formularioVentas();
            break;
        case "productos":
            $controladorReportes->formularioProductos();
            break;
        case "compras":
            $controladorReportes->formularioCompras();
            break;
        case "categorias":
            $controladorReportes->formularioCategorias();
            break;
        default:
            $controladorReportes->formularioVentas();
            break;
    }
    ?>

<?php elseif ($modulo === "caja"): ?>

    <?php
    require_once __DIR__ . "/../../controladores/CajaControlador.php";
    $controladorCaja = new CajaControlador();
    $controladorCaja->cierreDiario();
    ?>

<?php else: ?>
    <h3>Módulo: <?php echo htmlspecialchars(ucfirst($modulo)); ?></h3>
    <p class="text-muted">Módulo en construcción o sin acceso para vendedora.</p>
<?php endif; ?>

<?php
// Pie de página común
include __DIR__ . "/../plantillas/pie.php";
