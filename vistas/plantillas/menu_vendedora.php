<?php
// vistas/plantillas/menu_vendedora.php
// Usa la variable $modulo que viene desde panel_vendedora.php
?>
        <aside class="col-md-3 col-lg-2 mb-3">
            <div class="list-group shadow-sm">
                <a href="panel_vendedora.php"
                   class="list-group-item list-group-item-action <?php echo ($modulo === 'inicio') ? 'active' : ''; ?>">
                    Inicio
                </a>

                <a href="panel_vendedora.php?modulo=productos"
                   class="list-group-item list-group-item-action <?php echo ($modulo === 'productos') ? 'active' : ''; ?>">
                    Productos
                </a>

                <a href="panel_vendedora.php?modulo=categorias"
                   class="list-group-item list-group-item-action <?php echo ($modulo === 'categorias') ? 'active' : ''; ?>">
                    Categorías
                </a>

                <a href="panel_vendedora.php?modulo=clientes"
                   class="list-group-item list-group-item-action <?php echo ($modulo === 'clientes') ? 'active' : ''; ?>">
                    Clientes
                </a>

                <a href="panel_vendedora.php?modulo=compras"
                   class="list-group-item list-group-item-action <?php echo ($modulo === 'compras') ? 'active' : ''; ?>">
                    Compras (ingreso mercadería)
                </a>

                <a href="panel_vendedora.php?modulo=ventas"
                   class="list-group-item list-group-item-action <?php echo ($modulo === 'ventas') ? 'active' : ''; ?>">
                    Ventas
                </a>

                <a href="panel_vendedora.php?modulo=reportes"
                   class="list-group-item list-group-item-action <?php echo ($modulo === 'reportes') ? 'active' : ''; ?>">
                    Reportes
                </a>

                <a href="panel_vendedora.php?modulo=caja"
                   class="list-group-item list-group-item-action <?php echo ($modulo === 'caja') ? 'active' : ''; ?>">
                    Caja / cierre diario
                </a>
            </div>
        </aside>

        <main class="col-md-9 col-lg-10">
