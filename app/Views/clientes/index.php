<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Clientes<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Administración de Clientes<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Listado de Clientes</h3>
        <button type="button" class="btn btn-primary btn-sm ms-auto" onclick="abrirModalCrear()">
            <i class="bi bi-person-plus"></i> Nuevo Cliente
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaClientes" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">ID</th>
                        <th>Cédula</th>
                        <th>Nombre Cliente</th>
                        <th>Teléfono</th>
                        <th>Correo Electrónico</th>
                        <th style="width: 100px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Registrar / Editar -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalTitle">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formCliente" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_cliente" name="id_cliente">
                    
                    <div class="mb-3">
                        <label for="identificacion" class="form-label">Cédula <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="identificacion" name="identificacion" maxlength="10" placeholder="Ej. 1712345678">
                        <div class="invalid-feedback" id="error-identificacion"></div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Juan Pérez">
                        <div class="invalid-feedback" id="error-nombre"></div>
                    </div>

                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" maxlength="20" placeholder="Ej. 0991234567">
                        <div class="invalid-feedback" id="error-telefono"></div>
                    </div>

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" placeholder="Ej. cliente@correo.com">
                        <div class="invalid-feedback" id="error-correo"></div>
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
    let tablaClientes;
    const modalElement = document.getElementById('modalCliente');
    const modalBS = new bootstrap.Modal(modalElement);

    $(document).ready(function() {
        $('#modalCliente').on('shown.bs.modal', function() {
            $('#identificacion').trigger('focus');
        });

        tablaClientes = $('#tablaClientes').DataTable({
            "ajax": "<?= base_url('clientes/getClientes') ?>",
            "columns": [
                { "data": "id_cliente" },
                { "data": "identificacion" },
                { "data": "nombre" },
                { "data": "telefono", "render": function(d) { return d ? d : '-'; } },
                { "data": "correo", "render": function(d) { return d ? d : '-'; } },
                {
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `
                            <button class="btn btn-warning btn-sm me-1" onclick="editarCliente(${row.id_cliente})" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="eliminarCliente(${row.id_cliente})" title="Eliminar">
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

        $('#formCliente').on('submit', function(e) {
            e.preventDefault();
            limpiarErrores();

            $.ajax({
                url: "<?= base_url('clientes/guardar') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "JSON",
                success: function(response) {
                    if (response.status === 'success') {
                        modalBS.hide();
                        tablaClientes.ajax.reload();
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
        $('#formCliente')[0].reset();
        $('#id_cliente').val('');
        limpiarErrores();
        $('#modalTitle').text('Nuevo Cliente');
        modalBS.show();
    }

    function editarCliente(id) {
        limpiarErrores();
        $.get("<?= base_url('clientes/obtener/') ?>" + id, function(response) {
            if (response.status === 'success') {
                $('#id_cliente').val(response.data.id_cliente);
                $('#identificacion').val(response.data.identificacion);
                $('#nombre').val(response.data.nombre);
                $('#telefono').val(response.data.telefono);
                $('#correo').val(response.data.correo);
                $('#modalTitle').text('Editar Cliente');
                modalBS.show();
            } else {
                Toast.fire({
                    icon: 'error',
                    title: response.message
                });
            }
        });
    }

    function eliminarCliente(id) {
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
                    url: "<?= base_url('clientes/eliminar/') ?>" + id,
                    type: "DELETE",
                    dataType: "JSON",
                    success: function(response) {
                        if (response.status === 'success') {
                            tablaClientes.ajax.reload();
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
        // Enfocar el primer campo con error
        const primerCampoError = Object.keys(errors)[0];
        if (primerCampoError) {
            $('#' + primerCampoError).trigger('focus');
        }
    }

    function limpiarErrores() {
        $('#formCliente .form-control').removeClass('is-invalid');
        $('#formCliente .invalid-feedback').text('');
    }
</script>
<?= $this->endSection() ?>