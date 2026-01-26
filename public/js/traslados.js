// Estado de la aplicación
let productosData = [];
let productoSeleccionado = null;
let detalle = [];
let contador = 0;

// Cargar productos al iniciar
document.addEventListener('DOMContentLoaded', function() {
    loadProductos();
    initializeEventListeners();
    renderDetalle();
});

// Cargar productos desde el controlador
function loadProductos() {
    const baseUrl = document.querySelector('meta[name="app-url"]')?.content || '/traslados';
    fetch(baseUrl + '/api/productos')
        .then(response => response.json())
        .then(data => {
            productosData = data;
        })
        .catch(error => console.error('Error loading productos:', error));
}

// Inicializar event listeners
function initializeEventListeners() {
    // Búsqueda de productos
    document.getElementById('producto_search')?.addEventListener('keyup', handleProductoSearch);
    
    // Cambio de almacenes
    document.getElementById('origen_almacen_id')?.addEventListener('change', handleAlmacenChange);
    document.getElementById('destino_almacen_id')?.addEventListener('change', handleAlmacenChange);
    
    // Agregar producto
    document.getElementById('btn_agregar')?.addEventListener('click', agregarProducto);
    
    // Envío de formulario
    document.getElementById('trasladoForm')?.addEventListener('submit', validarFormulario);
}

// Buscar y mostrar productos
function handleProductoSearch(e) {
    const searchTerm = e.target.value.toLowerCase();
    const dropdown = document.getElementById('products_dropdown');

    if (searchTerm.length === 0) {
        dropdown.classList.remove('show');
        return;
    }

    const filtrados = productosData.filter(p =>
        p.nombre.toLowerCase().includes(searchTerm) ||
        p.codigo.toLowerCase().includes(searchTerm)
    );

    let html = '';
    filtrados.forEach(p => {
        const totalStock = p.inventarios.reduce((sum, inv) => sum + inv.stock, 0);
        html += `
            <div class="product-item" data-id="${p.id}" data-nombre="${p.nombre}" 
                data-codigo="${p.codigo}" data-inventarios='${JSON.stringify(p.inventarios)}'>
                <div class="product-item-info">
                    <strong>${p.nombre}</strong>
                    <span class="badge bg-info">${parseFloat(totalStock).toFixed(4)}</span>
                </div>
                <div class="product-item-code">${p.codigo}</div>
            </div>
        `;
    });

    dropdown.innerHTML = html;
    dropdown.classList.add('show');

    // Agregar listeners a los items
    document.querySelectorAll('.product-item').forEach(item => {
        item.addEventListener('click', seleccionarProducto);
    });
}

// Seleccionar un producto
function seleccionarProducto(e) {
    const item = e.currentTarget;
    const inventarios = JSON.parse(item.dataset.inventarios);
    const origenAlmacenId = parseInt(document.getElementById('origen_almacen_id').value);

    let stockEnOrigen = 0;
    if (origenAlmacenId) {
        const inv = inventarios.find(i => i.almacen_id === origenAlmacenId);
        stockEnOrigen = inv ? inv.stock : 0;
    }

    productoSeleccionado = {
        id: parseInt(item.dataset.id),
        nombre: item.dataset.nombre,
        codigo: item.dataset.codigo,
        inventarios: inventarios,
        stock: stockEnOrigen
    };

    document.getElementById('producto_search').value = productoSeleccionado.nombre;
    document.getElementById('stock').value = parseFloat(productoSeleccionado.stock).toFixed(4);
    document.getElementById('products_dropdown').classList.remove('show');
}

// Manejar cambio de almacenes
function handleAlmacenChange(e) {
    const origenId = parseInt(document.getElementById('origen_almacen_id').value);
    const destinoId = parseInt(document.getElementById('destino_almacen_id').value);

    // Deshabilitar opciones iguales
    document.querySelectorAll('#origen_almacen_id option').forEach(option => {
        option.disabled = parseInt(option.value) === destinoId && option.value !== '';
    });

    document.querySelectorAll('#destino_almacen_id option').forEach(option => {
        option.disabled = parseInt(option.value) === origenId && option.value !== '';
    });

    // Actualizar stock si cambió almacén origen
    if (productoSeleccionado && origenId && origenId !== destinoId) {
        const inv = productoSeleccionado.inventarios.find(i => i.almacen_id === origenId);
        productoSeleccionado.stock = inv ? inv.stock : 0;
        document.getElementById('stock').value = parseFloat(productoSeleccionado.stock).toFixed(4);
    }
}

// Agregar producto al detalle
function agregarProducto() {
    if (!productoSeleccionado) {
        Swal.fire('Advertencia', 'Seleccione un producto', 'warning');
        return;
    }

    const cantidad = parseFloat(document.getElementById('cantidad').value);
    if (cantidad <= 0) {
        Swal.fire('Advertencia', 'La cantidad debe ser mayor a 0', 'warning');
        return;
    }

    if (cantidad > parseFloat(productoSeleccionado.stock)) {
        Swal.fire('Advertencia', 'Cantidad supera el stock disponible en el almacén origen', 'warning');
        return;
    }

    // Verificar si ya existe
    const existe = detalle.find(d => d.id === productoSeleccionado.id);
    if (existe) {
        existe.cantidad += cantidad;
    } else {
        contador++;
        detalle.push({
            contador: contador,
            id: productoSeleccionado.id,
            nombre: productoSeleccionado.nombre,
            codigo: productoSeleccionado.codigo,
            cantidad: cantidad,
            stock: productoSeleccionado.stock
        });
    }

    renderDetalle();
    limpiarCampos();
}

// Renderizar tabla de detalle
function renderDetalle() {
    const tbody = document.getElementById('tbody_detalle');
    const sinProductos = document.getElementById('sin_productos');

    let html = '';
    detalle.forEach(item => {
        html += `
            <tr>
                <td>${item.contador}</td>
                <td>${item.nombre} <br> <small class="text-muted">${item.codigo}</small></td>
                <td>
                    <input type="hidden" name="arrayidproducto[]" value="${item.id}">
                    <input type="hidden" name="arraycantidad[]" value="${item.cantidad}">
                    ${parseFloat(item.cantidad).toFixed(4)}
                </td>
                <td>${parseFloat(item.stock).toFixed(4)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
    sinProductos.style.display = detalle.length === 0 ? 'block' : 'none';
}

// Eliminar producto del detalle
function eliminarProducto(id) {
    detalle = detalle.filter(d => d.id !== id);
    renderDetalle();
}

// Limpiar campos
function limpiarCampos() {
    document.getElementById('producto_search').value = '';
    document.getElementById('cantidad').value = '1.000';
    document.getElementById('stock').value = '';
    productoSeleccionado = null;
}

// Validar formulario
function validarFormulario(e) {
    if (detalle.length === 0) {
        e.preventDefault();
        Swal.fire('Advertencia', 'Debe agregar al menos un producto', 'warning');
        return false;
    }

    const origenId = document.getElementById('origen_almacen_id').value;
    const destinoId = document.getElementById('destino_almacen_id').value;

    if (!origenId) {
        e.preventDefault();
        Swal.fire('Advertencia', 'Debe seleccionar un almacén origen', 'warning');
        return false;
    }

    if (!destinoId) {
        e.preventDefault();
        Swal.fire('Advertencia', 'Debe seleccionar un almacén destino', 'warning');
        return false;
    }

    if (origenId === destinoId) {
        e.preventDefault();
        Swal.fire('Error', 'El almacén origen y destino no pueden ser iguales', 'error');
        return false;
    }

    return true;
}
