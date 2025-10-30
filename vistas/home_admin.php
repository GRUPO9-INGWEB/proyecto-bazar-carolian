<?php
// vistas/home_admin.php
include_once "../includes/seguridad.php";
?>
<div class="container mt-4">
  <h2 class="text-primary mb-4">🏠 Bienvenida al Panel de Administración</h2>
  <p>Desde aquí podrás gestionar todas las áreas del sistema: ventas, categorías, productos, reportes, usuarios y clientes.</p>

  <div class="row mt-4 g-4">
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h5 class="card-title">🛒 Ventas del día</h5>
          <h3 class="text-success">S/ 0.00</h3>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h5 class="card-title">📦 Productos con stock bajo</h5>
          <h3 class="text-warning">0</h3>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h5 class="card-title">👥 Usuarios activos</h5>
          <h3 class="text-info">0</h3>
        </div>
      </div>
    </div>
  </div>
</div>
