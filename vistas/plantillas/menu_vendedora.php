<?php
// vistas/plantillas/menu_vendedora.php
// Usa la variable $modulo que viene desde panel_vendedora.php
?>
        <aside class="col-12 col-md-3 col-lg-2 mb-3 sidebar">
            <div class="sidebar-inner shadow-sm">
                <div class="sidebar-title">Punto de venta</div>

                <nav class="sidebar-nav">
                    <a href="panel_vendedora.php"
                       class="sidebar-link <?php echo ($modulo === 'inicio') ? 'active' : ''; ?>">
                        <i class="bi bi-house-door"></i>
                        <span>Inicio</span>
                    </a>

                    <a href="panel_vendedora.php?modulo=productos"
                       class="sidebar-link <?php echo ($modulo === 'productos') ? 'active' : ''; ?>">
                        <i class="bi bi-box-seam"></i>
                        <span>Productos</span>
                    </a>

                    <a href="panel_vendedora.php?modulo=categorias"
                       class="sidebar-link <?php echo ($modulo === 'categorias') ? 'active' : ''; ?>">
                        <i class="bi bi-tags"></i>
                        <span>Categorías</span>
                    </a>

                    <a href="panel_vendedora.php?modulo=clientes"
                       class="sidebar-link <?php echo ($modulo === 'clientes') ? 'active' : ''; ?>">
                        <i class="bi bi-person-badge"></i>
                        <span>Clientes</span>
                    </a>

                    <a href="panel_vendedora.php?modulo=compras"
                       class="sidebar-link <?php echo ($modulo === 'compras') ? 'active' : ''; ?>">
                        <i class="bi bi-bag-plus"></i>
                        <span>Compras (ingreso mercadería)</span>
                    </a>

                    <a href="panel_vendedora.php?modulo=ventas"
                       class="sidebar-link <?php echo ($modulo === 'ventas') ? 'active' : ''; ?>">
                        <i class="bi bi-cash-stack"></i>
                        <span>Ventas</span>
                    </a>

                    <a href="panel_vendedora.php?modulo=reportes"
                       class="sidebar-link <?php echo ($modulo === 'reportes') ? 'active' : ''; ?>">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Reportes</span>
                    </a>

                    <a href="panel_vendedora.php?modulo=caja"
                       class="sidebar-link <?php echo ($modulo === 'caja') ? 'active' : ''; ?>">
                        <i class="bi bi-safe2"></i>
                        <span>Caja / cierre diario</span>
                    </a>
                </nav>
            </div>
        </aside>

        <main class="col-12 col-md-9 col-lg-10 app-main-content">
