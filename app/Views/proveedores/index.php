<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Proveedores<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Administración de Proveedores<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Listado de Proveedores</h3>
        <button type="button" class="btn btn-primary btn-sm ms-auto" onclick="abrirModalCrear()">
            <i class="bi bi-truck"></i> Nuevo Proveedor
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaProveedores" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">ID</th>
                        <th>Identificación (RUC/Cédula)</th>
                        <th>Razón Social / Nombre</th>
                        <th>Teléfono</th>
                        <th style="width: 100px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Registrar / Editar -->
<div class="modal fade" id="modalProveedor" tabindex="-1" aria-labelledby="modalTitle">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formProveedor" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_proveedor" name="id_proveedor">
                    
                    <div class="mb-3">
                        <label for="identificacion" class="form-label">Identificación (RUC/Cédula) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="identificacion" name="identificacion" maxlength="20" placeholder="Ej. 1790012345001">
                        <div class="invalid-feedback" id="error-identificacion"></div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Razón Social / Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Distribuidora S.A.">
                        <div class="invalid-feedback" id="error-nombre"></div>
                    </div>

                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" maxlength="20" placeholder="Ej. 022123456">
                        <div class="invalid-feedback" id="error-telefono"></div>
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
    let tablaProveedores;
    const modalElement = document.getElementById('modalProveedor');
    const modalBS = new bootstrap.Modal(modalElement);

    $(document).ready(function() {
        $('#modalProveedor').on('shown.bs.modal', function() {
            $('#identificacion').trigger('focus');
        });

        tablaProveedores = $('#tablaProveedores').DataTable({
            "ajax": "<?= base_url('proveedores/getProveedores') ?>",
            "columns": [
                { "data": "id_proveedor" },
                { "data": "identificacion" },
                { "data": "nombre" },
                { "data": "telefono", "render": function(d) { return d ? d : '-'; } },
                {
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `
                            <button class="btn btn-warning btn-sm me-1" onclick="editarProveedor(${row.id_proveedor})" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="eliminarProveedor(${row.id_proveedor})" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        `;
                    }
                }
            ],
            "language": typeof dtLanguageEs !== 'undefined' ? dtLanguageEs : {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
            }
        });

        $('#formProveedor').on('submit', function(e) {
            e.preventDefault();
            limpiarErrores();

            $.ajax({
                url: "<?= base_url('proveedores/guardar') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "JSON",
                success: function(response) {
                    if (response.status === 'success') {
                        modalBS.hide();
                        tablaProveedores.ajax.reload();
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                    } else if (response.status === 'error') {
                        if (response.errors) {
                            mostrarErrores(response.errors);
                        }
                    }
                }
            });
        });
    });

    function abrirModalCrear() {
        $('#formProveedor')[0].reset();
        $('#id_proveedor').val('');
        limpiarErrores();
        $('#modalTitle').text('Nuevo Proveedor');
        modalBS.show();
    }

    function editarProveedor(id) {
        limpiarErrores();
        $.get("<?= base_url('proveedores/obtener/') ?>" + id, function(response) {
            if (response.status === 'success') {
                $('#id_proveedor').val(response.data.id_proveedor);
                $('#identificacion').val(response.data.identificacion);
                $('#nombre').val(response.data.nombre);
                $('#telefono').val(response.data.telefono);
                $('#modalTitle').text('Editar Proveedor');
                modalBS.show();
            } else {
                Toast.fire({
                    icon: 'error',
                    title: response.message
                });
            }
        });
    }

    function eliminarProveedor(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('proveedores/eliminar/') ?>" + id,
                    type: "DELETE",
                    dataType: "JSON",
                    success: function(response) {
                        if (response.status === 'success') {
                            tablaProveedores.ajax.reload();
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            });
                        }
                    }
                });
            }
        });
    }

    function mostrarErrores(errors) {
        $.each(errors, function(campo, mensaje) {
            $('#' + campo).addClass('is-invalid');
            $('#error-' + campo).text(mensaje);
        });
        const primerCampoError = Object.keys(errors)[0];
        if (primerCampoError) {
            $('#' + primerCampoError).trigger('focus');
        }
    }

    function limpiarErrores() {
        $('#formProveedor .form-control').removeClass('is-invalid');
        $('#formProveedor .invalid-feedback').text('');
    }
</script>
<?= $this->endSection() ?>