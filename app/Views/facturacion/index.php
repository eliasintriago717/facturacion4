<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Facturación de Ventas<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Gestión de Facturas<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Historial de Facturas</h3>
        <button type="button" class="btn btn-primary btn-sm ms-auto" onclick="abrirModalNuevaVenta()">
            <i class="bi bi-plus-lg"></i> Nueva Factura
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaVentas" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th style="width: 80px;" class="text-center">N° Factura</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Atendido por</th>
                        <th class="text-end">Total ($)</th>
                        <th style="width: 100px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nueva Factura -->
<div class="modal fade" id="modalNuevaVenta" tabindex="-1" aria-labelledby="modalNuevaVentaLabel" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalNuevaVentaLabel"><i class="bi bi-receipt me-2"></i>Nueva Factura de Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formVenta" autocomplete="off">
                    <!-- Datos del Cliente -->
                    <div class="card mb-3 border-secondary">
                        <div class="card-header bg-light fw-bold text-secondary">
                            1. Información del Cliente
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6 position-relative">
                                    <label for="buscar_cliente" class="form-label">Buscar Cliente (Cédula / Nombre) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="buscar_cliente" placeholder="Escriba para buscar...">
                                    <div id="resultados_cliente" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; display:none;"></div>
                                    <input type="hidden" id="id_cliente" name="id_cliente">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Identificación</label>
                                    <input type="text" class="form-control" id="cliente_identificacion" readonly disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="cliente_telefono" readonly disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Búsqueda e Inserción de Productos -->
                    <div class="card mb-3 border-secondary">
                        <div class="card-header bg-light fw-bold text-secondary">
                            2. Agregar Productos
                        </div>
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-8 position-relative">
                                    <label for="buscar_producto" class="form-label">Buscar Producto (Código / Nombre)</label>
                                    <input type="text" class="form-control" id="buscar_producto" placeholder="Escriba para buscar producto...">
                                    <div id="resultados_producto" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; display:none;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- app/Views/facturacion/index.php -->


                    <!-- Tabla Maestro-Detalle -->
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="tablaDetalleVenta">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th style="width: 120px;">Stock Disp.</th>
                                    <th style="width: 120px;">Cantidad</th>
                                    <th style="width: 140px;">P. Unitario ($)</th>
                                    <th style="width: 140px;">Subtotal ($)</th>
                                    <th style="width: 60px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="bodyDetalleVenta">
                                <tr id="rowVacio">
                                    <td colspan="7" class="text-center text-muted">No se han agregado productos a la factura.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totales -->
                    <div class="row justify-content-end">
                        <div class="col-md-4">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Subtotal:
                                    <span class="fw-bold" id="lblSubtotal">$ 0.00</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    IVA (15%):
                                    <span class="fw-bold" id="lblIva">$ 0.00</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light fs-5 fw-bold">
                                    Total a Pagar:
                                    <span class="text-primary" id="lblTotal">$ 0.00</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnProcesarVenta" onclick="procesarVenta()">
                    <i class="bi bi-check-circle me-1"></i> Finalizar Factura
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalle Factura -->
<div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-labelledby="modalVerDetalleLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalVerDetalleLabel">Detalle de Factura #<span id="detNumFactura"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Cliente:</strong> <span id="detClienteNombre"></span></p>
                        <p class="mb-1"><strong>Identificación:</strong> <span id="detClienteId"></span></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-1"><strong>Fecha:</strong> <span id="detFecha"></span></p>
                        <p class="mb-1"><strong>Vendedor:</strong> <span id="detUsuario"></span></p>
                    </div>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">P. Unitario</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detProductosBody"></tbody>
                    </table>
                </div>
                <div class="row justify-content-end mt-2">
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total Facturado:</span>
                            <span class="text-success" id="detTotal"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let tablaVentas;
let productosFactura = [];
const modalNuevaVenta = new bootstrap.Modal(document.getElementById('modalNuevaVenta'));
const modalVerDetalle = new bootstrap.Modal(document.getElementById('modalVerDetalle'));

$(document).ready(function() {
    // 1. Inicializar DataTable
    tablaVentas = $('#tablaVentas').DataTable({
        "ajax": "<?= base_url('facturas/getVentas') ?>",
        "order": [[0, "desc"]],
        "columns": [
            { "data": "id_venta", "className": "text-center fw-bold" },
            { "data": "fecha", "className": "text-center" },
            { 
                "data": null, 
                "render": function(data, type, row) {
                    return `${row.cliente_nombre} <br><small class="text-muted">(${row.cliente_identificacion})</small>`;
                }
            },
            { "data": "usuario_nombre" },
            { 
                "data": "total", 
                "className": "text-end fw-bold",
                "render": function(data) { return `$ ${parseFloat(data).toFixed(2)}`; }
            },
            {
                "data": null,
                "orderable": false,
                "className": "text-center",
                "render": function(data, type, row) {
                    return `
                        <button class="btn btn-info btn-sm text-white" onclick="verDetalleFactura(${row.id_venta})" title="Ver Detalle">
                            <i class="bi bi-eye"></i>
                        </button>
                    `;
                }
            }
        ],
        "language": typeof dtLanguageEs !== 'undefined' ? dtLanguageEs : {
            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
        }
    });

    // 2. Búsqueda en vivo de Clientes
    $('#buscar_cliente').on('keyup', function() {
        let q = $(this).val().trim();
        if (q.length < 2) {
            $('#resultados_cliente').hide().empty();
            return;
        }

        $.get("<?= base_url('facturas/buscarClientes') ?>", { q: q }, function(data) {
            let resHtml = '';
            if (data.length > 0) {
                data.forEach(c => {
                    resHtml += `
                        <button type="button" class="list-group-item list-group-item-action" onclick="seleccionarCliente(${c.id_cliente}, '${c.nombre}', '${c.identificacion}', '${c.telefono || ''}')">
                            <strong>${c.nombre}</strong> <small class="text-muted">(${c.identificacion})</small>
                        </button>
                    `;
                });
            } else {
                resHtml = `<div class="list-group-item text-muted">No se encontraron clientes</div>`;
            }
            $('#resultados_cliente').html(resHtml).show();
        });
    });

    // 3. Búsqueda en vivo de Productos
    $('#buscar_producto').on('keyup', function() {
        let q = $(this).val().trim();
        if (q.length < 2) {
            $('#resultados_producto').hide().empty();
            return;
        }

        $.get("<?= base_url('facturas/buscarProductos') ?>", { q: q }, function(data) {
            let resHtml = '';
            if (data.length > 0) {
                data.forEach(p => {
                    let sinStock = p.stock <= 0;
                    resHtml += `
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center ${sinStock ? 'disabled bg-light' : ''}" 
                                onclick="${sinStock ? '' : `agregarProducto(${p.id_producto}, '${p.codigo_barras || ''}', '${p.nombre.replace(/'/g, "\\'")}', ${p.precio_venta}, ${p.stock})`}">
                            <div>
                                <strong>${p.nombre}</strong> <small class="text-muted">(${p.codigo_barras || 'Sin código'})</small>
                                <br><small class="text-primary">$ ${parseFloat(p.precio_venta).toFixed(2)}</small>
                            </div>
                            <span class="badge ${sinStock ? 'bg-danger' : 'bg-success'} rounded-pill">
                                ${sinStock ? 'Sin Stock' : 'Stock: ' + p.stock}
                            </span>
                        </button>
                    `;
                });
            } else {
                resHtml = `<div class="list-group-item text-muted">No se encontraron productos</div>`;
            }
            $('#resultados_producto').html(resHtml).show();
        });
    });

    // Cerrar desplegables de búsqueda al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#buscar_cliente, #resultados_cliente').length) {
            $('#resultados_cliente').hide();
        }
        if (!$(e.target).closest('#buscar_producto, #resultados_producto').length) {
            $('#resultados_producto').hide();
        }
    });
});

function abrirModalNuevaVenta() {
    $('#formVenta')[0].reset();
    $('#id_cliente').val('');
    $('#cliente_identificacion').val('');
    $('#cliente_telefono').val('');
    productosFactura = [];
    renderizarTablaDetalle();
    modalNuevaVenta.show();
}

function seleccionarCliente(id, nombre, identificacion, telefono) {
    $('#id_cliente').val(id);
    $('#buscar_cliente').val(nombre);
    $('#cliente_identificacion').val(identificacion);
    $('#cliente_telefono').val(telefono);
    $('#resultados_cliente').hide().empty();
}

function agregarProducto(id, codigo, nombre, precio, stock) {
    $('#resultados_producto').hide().empty();
    $('#buscar_producto').val('');

    // Verificar si el producto ya existe en el detalle
    let existente = productosFactura.find(p => p.id_producto === id);
    if (existente) {
        if (existente.cantidad + 1 > stock) {
            Toast.fire({ icon: 'warning', title: 'Supera el stock disponible.' });
            return;
        }
        existente.cantidad++;
    } else {
        productosFactura.push({
            id_producto: id,
            codigo: codigo,
            nombre: nombre,
            precio: parseFloat(precio),
            stock: parseInt(stock),
            cantidad: 1
        });
    }

    renderizarTablaDetalle();
}

function cambiarCantidad(idProducto, nuevaCantidad) {
    let cant = parseInt(nuevaCantidad);
    let prod = productosFactura.find(p => p.id_producto === idProducto);

    if (prod) {
        if (isNaN(cant) || cant <= 0) {
            prod.cantidad = 1;
        } else if (cant > prod.stock) {
            Toast.fire({ icon: 'warning', title: `Stock máximo disponible: ${prod.stock}` });
            prod.cantidad = prod.stock;
        } else {
            prod.cantidad = cant;
        }
    }
    renderizarTablaDetalle();
}

function eliminarProducto(idProducto) {
    productosFactura = productosFactura.filter(p => p.id_producto !== idProducto);
    renderizarTablaDetalle();
}

function renderizarTablaDetalle() {
    let tbody = $('#bodyDetalleVenta');
    tbody.empty();

    if (productosFactura.length === 0) {
        tbody.append(`
            <tr id="rowVacio">
                <td colspan="7" class="text-center text-muted">No se han agregado productos a la factura.</td>
            </tr>
        `);
        actualizarTotales(0);
        return;
    }

    let subtotalGeneral = 0;

    productosFactura.forEach(p => {
        let subtotal = p.cantidad * p.precio;
        subtotalGeneral += subtotal;

        tbody.append(`
            <tr>
                <td class="text-center">${p.codigo || '-'}</td>
                <td>${p.nombre}</td>
                <td class="text-center"><span class="badge bg-secondary">${p.stock}</span></td>
                <td>
                    <input type="number" class="form-control form-control-sm text-center" 
                           value="${p.cantidad}" min="1" max="${p.stock}" 
                           onchange="cambiarCantidad(${p.id_producto}, this.value)">
                </td>
                <td class="text-end">$ ${p.precio.toFixed(2)}</td>
                <td class="text-end fw-bold">$ ${subtotal.toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarProducto(${p.id_producto})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    actualizarTotales(subtotalGeneral);
}

function actualizarTotales(subtotalBase) {
    let iva = subtotalBase * 0.15; // 15% IVA
    let total = subtotalBase + iva;

    $('#lblSubtotal').text(`$ ${subtotalBase.toFixed(2)}`);
    $('#lblIva').text(`$ ${iva.toFixed(2)}`);
    $('#lblTotal').text(`$ ${total.toFixed(2)}`);
}

function procesarVenta() {
    let idCliente = $('#id_cliente').val();

    if (!idCliente) {
        Toast.fire({ icon: 'error', title: 'Debe seleccionar un cliente válido.' });
        return;
    }

    if (productosFactura.length === 0) {
        Toast.fire({ icon: 'error', title: 'Debe agregar al menos un producto a la factura.' });
        return;
    }

    let payload = {
        id_cliente: idCliente,
        productos: productosFactura.map(p => ({
            id_producto: p.id_producto,
            cantidad: p.cantidad
        }))
    };

    $('#btnProcesarVenta').prop('disabled', true);

    $.ajax({
        url: "<?= base_url('facturas/guardar') ?>",
        type: "POST",
        data: payload,
        dataType: "JSON",
        success: function(response) {
            $('#btnProcesarVenta').prop('disabled', false);

            if (response.status === 'success') {
                modalNuevaVenta.hide();
                tablaVentas.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: '¡Factura Creada!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Toast.fire({ icon: 'error', title: response.message });
            }
        },
        error: function() {
            $('#btnProcesarVenta').prop('disabled', false);
            Toast.fire({ icon: 'error', title: 'Error de servidor al guardar la factura.' });
        }
    });
}

function verDetalleFactura(idVenta) {
    $.get("<?= base_url('facturas/obtener/') ?>" + idVenta, function(response) {
        if (response.status === 'success') {
            let v = response.venta;
            let d = response.detalles;

            $('#detNumFactura').text(v.id_venta);
            $('#detClienteNombre').text(v.cliente_nombre);
            $('#detClienteId').text(v.cliente_identificacion);
            $('#detFecha').text(v.fecha);
            $('#detUsuario').text(v.usuario_nombre);
            $('#detTotal').text(`$ ${parseFloat(v.total).toFixed(2)}`);

            let body = $('#detProductosBody');
            body.empty();

            d.forEach(item => {
                body.append(`
                    <tr>
                        <td class="text-center">${item.codigo_barras || '-'}</td>
                        <td>${item.producto_nombre}</td>
                        <td class="text-center">${item.cantidad}</td>
                        <td class="text-end">$ ${parseFloat(item.precio_unitario).toFixed(2)}</td>
                        <td class="text-end fw-bold">$ ${parseFloat(item.subtotal).toFixed(2)}</td>
                    </tr>
                `);
            });

            modalVerDetalle.show();
        } else {
            Toast.fire({ icon: 'error', title: response.message });
        }
    });
}
</script>
<?= $this->endSection() ?>