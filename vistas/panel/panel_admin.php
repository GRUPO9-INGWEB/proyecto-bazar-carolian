<?php
// vistas/panel/panel_admin.php

session_start();

// Solo la administradora puede entrar aquí
if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION["nombre_rol"] !== "ADMINISTRADORA") {
    header("Location: panel_vendedora.php");
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
    // Muy importante: salir para que NO se impriman encabezado/pie HTML
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

    // Igual de importante: salir para que no salga HTML extra
    exit;
}

// PDF de caja / cierre diario
if ($modulo === "caja" && $accion === "cierre_pdf") {
    require_once __DIR__ . "/../../controladores/CajaControlador.php";
    $controladorCaja = new CajaControlador();
    $controladorCaja->pdfCierre();
    exit; // IMPORTANTE: que no se imprima el HTML del panel
}


// Encabezado y menú común (solo para vistas normales)
include __DIR__ . "/../plantillas/encabezado.php";
include __DIR__ . "/../plantillas/menu_admin.php";
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

    <h3>Panel de administradora</h3>
    <p class="text-muted">
        Resumen del sistema. Aquí se muestran alertas importantes de inventario
        (stock bajo y caducidad), además de accesos rápidos a los módulos.
    </p>

    <!-- Tarjetas de resumen -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-start border-primary border-3">
                <div class="card-body">
                    <h6 class="card-title">Productos con stock bajo</h6>
                    <p class="display-6 mb-0"><?php echo $totalStockBajo; ?></p>
                    <small class="text-muted">Stock actual ≤ stock mínimo</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-start border-warning border-3">
                <div class="card-body">
                    <h6 class="card-title">Por vencer (≤ 30 días)</h6>
                    <p class="display-6 mb-0"><?php echo $totalPorVencer; ?></p>
                    <small class="text-muted">Según fecha de caducidad</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-start border-danger border-3">
                <div class="card-body">
                    <h6 class="card-title">Productos vencidos</h6>
                    <p class="display-6 mb-0"><?php echo $totalVencidos; ?></p>
                    <small class="text-muted">No deberían venderse</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Ventas / reportes</h6>
                    <p class="card-text">Accesos rápidos a módulos principales.</p>
                    <a href="panel_admin.php?modulo=ventas&accion=listar" class="btn btn-sm btn-primary">Ir a ventas</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Listados rápidos -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <h5>Productos con stock bajo</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
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
                        <tr><td colspan="4" class="text-center">No hay productos con stock bajo.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <h5>Productos por vencer / vencidos</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
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
                        <tr><td colspan="3" class="text-center">No hay productos próximos a vencer.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Accesos rápidos a módulos -->
    <div class="row mt-3">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Usuarios</h6>
                    <p class="card-text">Gestiona los usuarios y sus roles.</p>
                    <a href="panel_admin.php?modulo=usuarios&accion=listar" class="btn btn-sm btn-primary">Ir</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Productos</h6>
                    <p class="card-text">Control de inventario, precios y stock.</p>
                    <a href="panel_admin.php?modulo=productos&accion=listar" class="btn btn-sm btn-primary">Ir</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Ventas</h6>
                    <p class="card-text">Registro de ventas y comprobantes.</p>
                    <a href="panel_admin.php?modulo=ventas&accion=listar" class="btn btn-sm btn-primary">Ir</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Reportes</h6>
                    <p class="card-text">Análisis de ventas y productos.</p>
                    <a href="panel_admin.php?modulo=reportes&accion=listar" class="btn btn-sm btn-primary">Ir</a>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($modulo === "usuarios"): ?>

    <?php
    require_once __DIR__ . "/../../controladores/UsuarioControlador.php";
    $controladorUsuarios = new UsuarioControlador();

    switch ($accion) {
        case "nuevo":
            $controladorUsuarios->formularioNuevo();
            break;

        case "guardar_nuevo":
            $controladorUsuarios->guardarNuevo();
            break;

        case "editar":
            $id = intval($_GET["id"] ?? 0);
            $controladorUsuarios->formularioEditar($id);
            break;

        case "guardar_edicion":
            $controladorUsuarios->guardarEdicion();
            break;

        case "cambiar_estado":
            $id = intval($_GET["id"] ?? 0);
            $estado = intval($_GET["estado"] ?? 0);
            $controladorUsuarios->cambiarEstado($id, $estado);
            break;

        default:
            $controladorUsuarios->listar();
            break;
    }
    ?>

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

        case "enviar_correo":   // aquí solo enviamos correo, vista normal
            $id = (int)($_GET["id"] ?? 0);
            $controladorVentas->enviarCorreo($id);
            break;

        default:
            $controladorVentas->listar();
            break;
    }
    ?>

<?php elseif ($modulo === "proveedores"): ?>

    <?php
    require_once __DIR__ . "/../../controladores/ProveedorControlador.php";
    $controladorProveedores = new ProveedorControlador();

    switch ($accion) {
        case "nuevo":
            $controladorProveedores->formularioNuevo();
            break;

        case "guardar_nuevo":
            $controladorProveedores->guardarNuevo();
            break;

        case "editar":
            $id = intval($_GET["id"] ?? 0);
            $controladorProveedores->formularioEditar($id);
            break;

        case "guardar_edicion":
            $controladorProveedores->guardarEdicion();
            break;

        case "cambiar_estado":
            $id     = intval($_GET["id"] ?? 0);
            $estado = intval($_GET["estado"] ?? 0);
            $controladorProveedores->cambiarEstado($id, $estado);
            break;

        default:
            $controladorProveedores->listar();
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
            // Formulario de filtros de ventas
            $controladorReportes->formularioVentas();
            break;

        case "productos":
            // Formulario de reporte de productos vendidos
            $controladorReportes->formularioProductos();
            break;

        case "compras":
            // Formulario de reporte de compras (mercadería ingresada)
            $controladorReportes->formularioCompras();
            break;

        case "categorias":
            // Formulario de reporte de productos por categoría
            $controladorReportes->formularioCategorias();
            break;

        default:
            $controladorReportes->formularioVentas();
            break;
    }
    ?>

<?php elseif ($modulo === "auditoria"): ?>

    <?php
    require_once __DIR__ . "/../../controladores/AuditoriaControlador.php";
    $controladorAuditoria = new AuditoriaControlador();
    $controladorAuditoria->listar();
    ?>

<?php elseif ($modulo === "caja"): ?>

    <?php
    require_once __DIR__ . "/../../controladores/CajaControlador.php";
    $controladorCaja = new CajaControlador();

    // por ahora solo tenemos una acción: cierre diario
    $controladorCaja->cierreDiario();
    ?>


<?php else: ?>
    <h3>Módulo: <?php echo htmlspecialchars(ucfirst($modulo)); ?></h3>
    <p class="text-muted">Módulo en construcción.</p>
<?php endif; ?>



<?php
// Pie de página común
include __DIR__ . "/../plantillas/pie.php";
