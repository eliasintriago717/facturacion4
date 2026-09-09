<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Compras<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Gestión de Compras<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Historial de Compras</h3>
        <button type="button" class="btn btn-primary btn-sm ms-auto" onclick="abrirModalNuevaCompra()">
            <i class="bi bi-plus-lg"></i> Nueva Compra
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaCompras" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th style="width: 80px;" class="text-center">N° Compra</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Registrado por</th>
                        <th class="text-end">Total ($)</th>
                        <th style="width: 100px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nueva Compra -->
<div class="modal fade" id="modalNuevaCompra" tabindex="-1" aria-labelledby="modalNuevaCompraLabel" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalNuevaCompraLabel"><i class="bi bi-bag-plus me-2"></i>Nueva Compra a Proveedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCompra" autocomplete="off">
                    <!-- Datos del Proveedor -->
                    <div class="card mb-3 border-secondary">
                        <div class="card-header bg-light fw-bold text-secondary">
                            1. Información del Proveedor
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6 position-relative">
                                    <label for="buscar_proveedor" class="form-label">Buscar Proveedor (RUC/Cédula / Nombre) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="buscar_proveedor" placeholder="Escriba para buscar...">
                                    <div id="resultados_proveedor" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; display:none;"></div>
                                    <input type="hidden" id="id_proveedor" name="id_proveedor">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Identificación</label>
                                    <input type="text" class="form-control" id="proveedor_identificacion" readonly disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="proveedor_telefono" readonly disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Búsqueda e Inserción de Productos -->
                    <div class="card mb-3 border-secondary">
                        <div class="card-header bg-light fw-bold text-secondary">
                            2. Agregar Productos a Ingresar
                        </div>
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-8 position-relative">
                                    <label for="buscar_producto_compra" class="form-label">Buscar Producto (Código / Nombre)</label>
                                    <input type="text" class="form-control" id="buscar_producto_compra" placeholder="Escriba para buscar producto...">
                                    <div id="resultados_producto_compra" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; display:none;"></div>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle"></i> El costo unitario se define en cada línea; puede diferir del costo de la última compra.
                            </small>
                        </div>
                    </div>

                    <!-- Tabla Maestro-Detalle -->
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="tablaDetalleCompra">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th style="width: 110px;">Stock Actual</th>
                                    <th style="width: 110px;">Cantidad</th>
                                    <th style="width: 140px;">Costo Unit. ($)</th>
                                    <th style="width: 140px;">Subtotal ($)</th>
                                    <th style="width: 60px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="bodyDetalleCompra">
                                <tr id="rowVacioCompra">
                                    <td colspan="7" class="text-center text-muted">No se han agregado productos a la compra.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totales -->
                    <div class="row justify-content-end">
                        <div class="col-md-4">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light fs-5 fw-bold">
                                    Total de la Compra:
                                    <span class="text-primary" id="lblTotalCompra">$ 0.00</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnProcesarCompra" onclick="procesarCompra()">
                    <i class="bi bi-check-circle me-1"></i> Registrar Compra
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalle Compra -->
<div class="modal fade" id="modalVerDetalleCompra" tabindex="-1" aria-labelledby="modalVerDetalleCompraLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalVerDetalleCompraLabel">Detalle de Compra #<span id="detNumCompra"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Proveedor:</strong> <span id="detProveedorNombre"></span></p>
                        <p class="mb-1"><strong>Identificación:</strong> <span id="detProveedorId"></span></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-1"><strong>Fecha:</strong> <span id="detFechaCompra"></span></p>
                        <p class="mb-1"><strong>Registrado por:</strong> <span id="detUsuarioCompra"></span></p>
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
                                <th class="text-end">Costo Unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detProductosCompraBody"></tbody>
                    </table>
                </div>
                <div class="row justify-content-end mt-2">
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total de la Compra:</span>
                            <span class="text-success" id="detTotalCompra"></span>
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
let tablaCompras;
let productosCompra = [];
const modalNuevaCompra = new bootstrap.Modal(document.getElementById('modalNuevaCompra'));
const modalVerDetalleCompra = new bootstrap.Modal(document.getElementById('modalVerDetalleCompra'));

$(document).ready(function() {
    // 1. Inicializar DataTable
    tablaCompras = $('#tablaCompras').DataTable({
        "ajax": "<?= base_url('compras/getCompras') ?>",
        "order": [[0, "desc"]],
        "columns": [
            { "data": "id_compra", "className": "text-center fw-bold" },
            { "data": "fecha", "className": "text-center" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `${row.proveedor_nombre} <br><small class="text-muted">(${row.proveedor_identificacion})</small>`;
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
                        <button class="btn btn-info btn-sm text-white" onclick="verDetalleCompra(${row.id_compra})" title="Ver Detalle">
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

    // 2. Búsqueda en vivo de Proveedores
    $('#buscar_proveedor').on('keyup', function() {
        let q = $(this).val().trim();
        if (q.length < 2) {
            $('#resultados_proveedor').hide().empty();
            return;
        }

        $.get("<?= base_url('compras/buscarProveedores') ?>", { q: q }, function(data) {
            let resHtml = '';
            if (data.length > 0) {
                data.forEach(p => {
                    resHtml += `
                        <button type="button" class="list-group-item list-group-item-action" onclick="seleccionarProveedor(${p.id_proveedor}, '${p.nombre}', '${p.identificacion}', '${p.telefono || ''}')">
                            <strong>${p.nombre}</strong> <small class="text-muted">(${p.identificacion})</small>
                        </button>
                    `;
                });
            } else {
                resHtml = `<div class="list-group-item text-muted">No se encontraron proveedores</div>`;
            }
            $('#resultados_proveedor').html(resHtml).show();
        });
    });

    // 3. Búsqueda en vivo de Productos
    $('#buscar_producto_compra').on('keyup', function() {
        let q = $(this).val().trim();
        if (q.length < 2) {
            $('#resultados_producto_compra').hide().empty();
            return;
        }

        $.get("<?= base_url('compras/buscarProductos') ?>", { q: q }, function(data) {
            let resHtml = '';
            if (data.length > 0) {
                data.forEach(p => {
                    resHtml += `
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                onclick="agregarProductoCompra(${p.id_producto}, '${p.codigo_barras || ''}', '${p.nombre.replace(/'/g, "\\'")}', ${p.precio_venta}, ${p.stock})">
                            <div>
                                <strong>${p.nombre}</strong> <small class="text-muted">(${p.codigo_barras || 'Sin código'})</small>
                            </div>
                            <span class="badge bg-secondary rounded-pill">Stock: ${p.stock}</span>
                        </button>
                    `;
                });
            } else {
                resHtml = `<div class="list-group-item text-muted">No se encontraron productos</div>`;
            }
            $('#resultados_producto_compra').html(resHtml).show();
        });
    });

    // Cerrar desplegables de búsqueda al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#buscar_proveedor, #resultados_proveedor').length) {
            $('#resultados_proveedor').hide();
        }
        if (!$(e.target).closest('#buscar_producto_compra, #resultados_producto_compra').length) {
            $('#resultados_producto_compra').hide();
        }
    });
});

function abrirModalNuevaCompra() {
    $('#formCompra')[0].reset();
    $('#id_proveedor').val('');
    $('#proveedor_identificacion').val('');
    $('#proveedor_telefono').val('');
    productosCompra = [];
    renderizarTablaDetalleCompra();
    modalNuevaCompra.show();
}

function seleccionarProveedor(id, nombre, identificacion, telefono) {
    $('#id_proveedor').val(id);
    $('#buscar_proveedor').val(nombre);
    $('#proveedor_identificacion').val(identificacion);
    $('#proveedor_telefono').val(telefono);
    $('#resultados_proveedor').hide().empty();
}

function agregarProductoCompra(id, codigo, nombre, precioReferencia, stockActual) {
    $('#resultados_producto_compra').hide().empty();
    $('#buscar_producto_compra').val('');

    let existente = productosCompra.find(p => p.id_producto === id);
    if (existente) {
        existente.cantidad++;
    } else {
        productosCompra.push({
            id_producto: id,
            codigo: codigo,
            nombre: nombre,
            stock_actual: parseInt(stockActual),
            cantidad: 1,
            costo_unitario: parseFloat(precioReferencia) || 0
        });
    }

    renderizarTablaDetalleCompra();
}

function cambiarCantidadCompra(idProducto, nuevaCantidad) {
    let cant = parseInt(nuevaCantidad);
    let prod = productosCompra.find(p => p.id_producto === idProducto);

    if (prod) {
        prod.cantidad = (isNaN(cant) || cant <= 0) ? 1 : cant;
    }
    renderizarTablaDetalleCompra();
}

function cambiarCostoCompra(idProducto, nuevoCosto) {
    let costo = parseFloat(nuevoCosto);
    let prod = productosCompra.find(p => p.id_producto === idProducto);

    if (prod) {
        prod.costo_unitario = (isNaN(costo) || costo < 0) ? 0 : costo;
    }
    renderizarTablaDetalleCompra();
}

function eliminarProductoCompra(idProducto) {
    productosCompra = productosCompra.filter(p => p.id_producto !== idProducto);
    renderizarTablaDetalleCompra();
}

function renderizarTablaDetalleCompra() {
    let tbody = $('#bodyDetalleCompra');
    tbody.empty();

    if (productosCompra.length === 0) {
        tbody.append(`
            <tr id="rowVacioCompra">
                <td colspan="7" class="text-center text-muted">No se han agregado productos a la compra.</td>
            </tr>
        `);
        actualizarTotalCompra(0);
        return;
    }

    let totalGeneral = 0;

    productosCompra.forEach(p => {
        let subtotal = p.cantidad * p.costo_unitario;
        totalGeneral += subtotal;

        tbody.append(`
            <tr>
                <td class="text-center">${p.codigo || '-'}</td>
                <td>${p.nombre}</td>
                <td class="text-center"><span class="badge bg-secondary">${p.stock_actual}</span></td>
                <td>
                    <input type="number" class="form-control form-control-sm text-center"
                           value="${p.cantidad}" min="1"
                           onchange="cambiarCantidadCompra(${p.id_producto}, this.value)">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm text-end"
                           value="${p.costo_unitario.toFixed(2)}" min="0" step="0.01"
                           onchange="cambiarCostoCompra(${p.id_producto}, this.value)">
                </td>
                <td class="text-end fw-bold">$ ${subtotal.toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarProductoCompra(${p.id_producto})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    actualizarTotalCompra(totalGeneral);
}

function actualizarTotalCompra(total) {
    $('#lblTotalCompra').text(`$ ${total.toFixed(2)}`);
}

function procesarCompra() {
    let idProveedor = $('#id_proveedor').val();

    if (!idProveedor) {
        Toast.fire({ icon: 'error', title: 'Debe seleccionar un proveedor válido.' });
        return;
    }

    if (productosCompra.length === 0) {
        Toast.fire({ icon: 'error', title: 'Debe agregar al menos un producto a la compra.' });
        return;
    }

    let productosInvalidos = productosCompra.some(p => p.costo_unitario <= 0);
    if (productosInvalidos) {
        Toast.fire({ icon: 'error', title: 'Todos los productos deben tener un costo unitario mayor a cero.' });
        return;
    }

    let payload = {
        id_proveedor: idProveedor,
        productos: productosCompra.map(p => ({
            id_producto: p.id_producto,
            cantidad: p.cantidad,
            costo_unitario: p.costo_unitario
        }))
    };

    $('#btnProcesarCompra').prop('disabled', true);

    $.ajax({
        url: "<?= base_url('compras/guardar') ?>",
        type: "POST",
        data: payload,
        dataType: "JSON",
        success: function(response) {
            $('#btnProcesarCompra').prop('disabled', false);

            if (response.status === 'success') {
                modalNuevaCompra.hide();
                tablaCompras.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: '¡Compra Registrada!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Toast.fire({ icon: 'error', title: response.message });
            }
        },
        error: function() {
            $('#btnProcesarCompra').prop('disabled', false);
            Toast.fire({ icon: 'error', title: 'Error de servidor al guardar la compra.' });
        }
    });
}

function verDetalleCompra(idCompra) {
    $.get("<?= base_url('compras/obtener/') ?>" + idCompra, function(response) {
        if (response.status === 'success') {
            let c = response.compra;
            let d = response.detalles;

            $('#detNumCompra').text(c.id_compra);
            $('#detProveedorNombre').text(c.proveedor_nombre);
            $('#detProveedorId').text(c.proveedor_identificacion);
            $('#detFechaCompra').text(c.fecha);
            $('#detUsuarioCompra').text(c.usuario_nombre);
            $('#detTotalCompra').text(`$ ${parseFloat(c.total).toFixed(2)}`);

            let body = $('#detProductosCompraBody');
            body.empty();

            d.forEach(item => {
                body.append(`
                    <tr>
                        <td class="text-center">${item.codigo_barras || '-'}</td>
                        <td>${item.producto_nombre}</td>
                        <td class="text-center">${item.cantidad}</td>
                        <td class="text-end">$ ${parseFloat(item.costo_unitario).toFixed(2)}</td>
                        <td class="text-end fw-bold">$ ${parseFloat(item.subtotal).toFixed(2)}</td>
                    </tr>
                `);
            });

            modalVerDetalleCompra.show();
        } else {
            Toast.fire({ icon: 'error', title: response.message });
        }
    });
}
</script>
<?= $this->endSection() ?>
