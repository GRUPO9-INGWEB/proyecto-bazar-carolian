<?php
// vistas/home_vendedor.php
include_once "../includes/seguridad.php";
?>
<div class="container mt-4">
  <h2 class="text-success mb-3">🏠 Bienvenido/a al Panel del Vendedor</h2>
  <p>Selecciona una opción del menú lateral para comenzar a trabajar con las ventas o productos.</p>

  <div class="row g-4 mt-3">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h5>🛒 Ventas hoy</h5>
          <p class="fs-4 text-primary">S/ 0.00</p>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h5>📦 Productos disponibles</h5>
          <p class="fs-4 text-primary">0</p>
        </div>
      </div>
    </div>
  </div>
</div>
