<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-boxes me-2"></i>Gestión de Productos</h1>
            </div>
            <div class="col-sm-6 text-end">
                <button type="button" class="btn btn-primary" id="btnNuevoProducto">
                    <i class="fas fa-plus me-1"></i> Nuevo Producto
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaProductos" class="table table-bordered table-striped align-middle w-100">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Código Barras</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Marca</th>
                                <th>Precio Venta</th>
                                <th>Stock</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalProductoLabel">Nuevo Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formProducto">
                <div class="modal-body">
                    <input type="hidden" id="id_producto" name="id_producto">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="codigo_barras" class="form-label">Código de Barras</label>
                            <input type="text" class="form-control" id="codigo_barras" name="codigo_barras" placeholder="Opcional">
                            <div class="invalid-feedback" id="err_codigo_barras"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                            <div class="invalid-feedback" id="err_nombre"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select" id="id_categoria" name="id_categoria" required>
                                <option value="">Seleccione una categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>"><?= esc($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="err_id_categoria"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="id_marca" class="form-label">Marca <span class="text-danger">*</span></label>
                            <select class="form-select" id="id_marca" name="id_marca" required>
                                <option value="">Seleccione una marca</option>
                                <?php foreach ($marcas as $mar): ?>
                                    <option value="<?= $mar['id_marca'] ?>"><?= esc($mar['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="err_id_marca"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="precio_venta" class="form-label">Precio de Venta <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="precio_venta" name="precio_venta" required>
                            </div>
                            <div class="invalid-feedback" id="err_precio_venta"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">Stock Inicial <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" id="stock" name="stock" required>
                            <div class="invalid-feedback" id="err_stock"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const urlGetProductos = "<?= base_url('productos/getProductos') ?>";
    const urlGuardar      = "<?= base_url('productos/guardar') ?>";
    const urlObtener      = "<?= base_url('productos/obtener') ?>";
    const urlEliminar     = "<?= base_url('productos/eliminar') ?>";

    let tabla = $('#tablaProductos').DataTable({
        ajax: {
            url: urlGetProductos,
            type: 'GET'
        },
        columns: [
            { data: 'id_producto' },
            { 
                data: 'codigo_barras',
                render: function(data) {
                    return data ? `<span class="badge bg-secondary">${data}</span>` : '<span class="text-muted">N/A</span>';
                }
            },
            { data: 'nombre' },
            { data: 'categoria_nombre' },
            { data: 'marca_nombre' },
            { 
                data: 'precio_venta',
                render: function(data) {
                    return '$ ' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'stock',
                render: function(data) {
                    let badgeClass = data > 10 ? 'bg-success' : (data > 0 ? 'bg-warning text-dark' : 'bg-danger');
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            {
                data: null,
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-outline-warning btn-editar" data-id="${row.id_producto}" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${row.id_producto}" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });

    $('#btnNuevoProducto').click(function() {
        limpiarFormulario();
        $('#modalProductoLabel').text('Nuevo Producto');
        $('#modalProducto').modal('show');
    });

    $('#formProducto').submit(function(e) {
        e.preventDefault();
        limpiarErrores();

        $.ajax({
            url: urlGuardar,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#modalProducto').modal('hide');
                    tabla.ajax.reload();
                    Swal.fire('¡Éxito!', res.message, 'success');
                } else if (res.status === 'error') {
                    if (res.errors) {
                        mostrarErrores(res.errors);
                    } else if (res.message) {
                        Swal.fire('Atención', res.message, 'warning');
                    }
                }
            },
            error: function() {
                Swal.fire('Error', 'No se pudo procesar la solicitud.', 'error');
            }
        });
    });

    $(document).on('click', '.btn-editar', function() {
        let id = $(this).data('id');
        limpiarFormulario();

        $.get(`${urlObtener}/${id}`, function(res) {
            if (res.status === 'success') {
                $('#id_producto').val(res.data.id_producto);
                $('#codigo_barras').val(res.data.codigo_barras);
                $('#nombre').val(res.data.nombre);
                $('#id_categoria').val(res.data.id_categoria);
                $('#id_marca').val(res.data.id_marca);
                $('#precio_venta').val(res.data.precio_venta);
                $('#stock').val(res.data.stock);

                $('#modalProductoLabel').text('Editar Producto');
                $('#modalProducto').modal('show');
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, 'json');
    });

    $(document).on('click', '.btn-eliminar', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: '¿Está seguro?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmColor: '#d33',
            cancelColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${urlEliminar}/${id}`,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            tabla.ajax.reload();
                            Swal.fire('Eliminado', res.message, 'success');
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo procesar la eliminación.', 'error');
                    }
                });
            }
        });
    });

    function limpiarFormulario() {
        $('#formProducto')[0].reset();
        $('#id_producto').val('');
        limpiarErrores();
    }

    function limpiarErrores() {
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    function mostrarErrores(errors) {
        $.each(errors, function(campo, mensaje) {
            $(`#${campo}`).addClass('is-invalid');
            $(`#err_${campo}`).text(mensaje);
        });
    }
});
</script>
<?= $this->endSection() ?>