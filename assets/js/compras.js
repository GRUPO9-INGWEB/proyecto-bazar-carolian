document.addEventListener('DOMContentLoaded', function() {
    
    const buscarInput = document.getElementById('buscar_producto');
    const resultadosDiv = document.getElementById('resultados_busqueda');
    const listaProductosTbody = document.getElementById('lista_productos_compra');
    const filaVacia = document.getElementById('fila_vacia');
    
    let carrito = [];

    // --- 1. LÓGICA DE BÚSQUEDA (ACTUALIZADA) ---
    buscarInput.addEventListener('keyup', function() {
        let termino = this.value.trim();

        if (termino.length < 2) {
            resultadosDiv.innerHTML = '';
            return;
        }

        // --- ¡CAMBIO AQUÍ! ---
        // Apuntamos al nuevo controlador exclusivo de compras
        fetch('../controladores/buscar_producto_compra.php', { 
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'termino=' + encodeURIComponent(termino)
        })
        .then(response => response.json())
        .then(data => {
            mostrarResultados(data);
        })
        .catch(error => console.error('Error en búsqueda:', error));
    });

    function mostrarResultados(productos) {
        resultadosDiv.innerHTML = '';
        if (productos.length > 0) {
            productos.forEach(producto => {
                let item = document.createElement('a');
                item.href = '#';
                item.classList.add('list-group-item', 'list-group-item-action');
                // Usamos los datos que nos devuelve el nuevo controlador
                item.textContent = `${producto.codigo || 'S/C'} - ${producto.nombre} (Stock: ${producto.stock})`;
                
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Pasamos el objeto 'producto' completo
                    anadirProductoAlCarrito(producto); 
                    buscarInput.value = '';
                    resultadosDiv.innerHTML = '';
                });
                resultadosDiv.appendChild(item);
            });
        } else {
            resultadosDiv.innerHTML = '<span class="list-group-item">No se encontraron productos.</span>';
        }
    }

    // Ocultar resultados si se hace clic fuera
    document.addEventListener('click', function(e) {
        if (e.target !== buscarInput) {
            resultadosDiv.innerHTML = '';
        }
    });

    // --- 2. LÓGICA DEL CARRITO ---
    function anadirProductoAlCarrito(producto) {
        // El 'producto' que recibimos ahora SÍ tiene 'precio_costo'
        let productoExistente = carrito.find(item => item.id === producto.id);

        if (productoExistente) {
            document.getElementById(`cantidad_${producto.id}`).focus();
        } else {
            let item = {
                id: producto.id,
                nombre: producto.nombre,
                // Usamos el precio_costo que SÍ viene en la respuesta AJAX
                precio_costo: parseFloat(producto.precio_costo).toFixed(2) || '0.00', 
                cantidad: 1
            };
            carrito.push(item);
        }
        actualizarVistaCarrito();
    }

    function actualizarVistaCarrito() {
        listaProductosTbody.innerHTML = ''; 

        if (carrito.length === 0) {
            listaProductosTbody.appendChild(filaVacia);
        } else {
            carrito.forEach(item => {
                let fila = document.createElement('tr');
                fila.id = `fila_${item.id}`;
                let subtotal = (item.cantidad * item.precio_costo).toFixed(2);

                fila.innerHTML = `
                    <td>${item.nombre}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm" id="cantidad_${item.id}" 
                               value="${item.cantidad}" min="1" data-id="${item.id}">
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control form-control-sm" id="costo_${item.id}" 
                               value="${item.precio_costo}" min="0" data-id="${item.id}">
                    </td>
                    <td>S/ <span id="subtotal_${item.id}">${subtotal}</span></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" data-id="${item.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                listaProductosTbody.appendChild(fila);
            });
        }
        
        agregarListenersInputs();
        calcularTotales();
    }

    function agregarListenersInputs() {
        listaProductosTbody.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('change', function() {
                let id = this.dataset.id;
                let tipo = this.id.startsWith('cantidad_') ? 'cantidad' : 'precio_costo';
                let valor = parseFloat(this.value);
                
                actualizarDatosCarrito(id, tipo, valor);
            });
        });

        listaProductosTbody.querySelectorAll('.btn-danger').forEach(boton => {
            boton.addEventListener('click', function() {
                let id = this.dataset.id;
                eliminarItemCarrito(id);
            });
        });
    }

    function actualizarDatosCarrito(id, tipo, valor) {
        let itemIndex = carrito.findIndex(item => item.id == id);
        if (itemIndex === -1) return;

        if (tipo === 'cantidad' && valor > 0) {
            carrito[itemIndex].cantidad = valor;
        } else if (tipo === 'precio_costo' && valor >= 0) {
            carrito[itemIndex].precio_costo = valor;
        }
        
        let item = carrito[itemIndex];
        let subtotalFila = (item.cantidad * item.precio_costo).toFixed(2);
        document.getElementById(`subtotal_${item.id}`).textContent = subtotalFila;

        calcularTotales();
    }

    function eliminarItemCarrito(id) {
        carrito = carrito.filter(item => item.id != id);
        actualizarVistaCarrito();
    }

    // --- 3. LÓGICA DE TOTALES (Sin cambios) ---
    function calcularTotales() {
        let subtotal = 0;
        
        carrito.forEach(item => {
            subtotal += item.cantidad * item.precio_costo;
        });

        let igv = subtotal * 0.18;
        let total = subtotal + igv;

        document.getElementById('total_subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('total_igv').textContent = igv.toFixed(2);
        document.getElementById('total_final').textContent = total.toFixed(2);

        document.getElementById('input_subtotal').value = subtotal.toFixed(2);
        document.getElementById('input_igv').value = igv.toFixed(2);
        document.getElementById('input_total').value = total.toFixed(2);
        document.getElementById('input_carrito').value = JSON.stringify(carrito);
    }
    
    // --- 4. VALIDACIÓN ANTES DE ENVIAR (Sin cambios) ---
    document.getElementById('form_nueva_compra').addEventListener('submit', function(e) {
        if (carrito.length === 0) {
            e.preventDefault();
            Swal.fire('Carrito Vacío', 'Debe añadir al menos un producto a la compra.', 'warning');
        }
    });

});