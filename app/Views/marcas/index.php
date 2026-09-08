<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Marcas<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Administración de Marcas<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Listado de Marcas</h3>
        <button type="button" class="btn btn-primary btn-sm ms-auto" onclick="abrirModalCrear()">
            <i class="bi bi-plus-lg"></i> Nueva Marca
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaMarcas" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th style="width: 80px;" class="text-center">ID</th>
                        <th>Nombre de la Marca</th>
                        <th style="width: 120px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Registrar / Editar -->
<div class="modal fade" id="modalMarca" tabindex="-1" aria-labelledby="modalTitle">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formMarca" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_marca" name="id_marca">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Nike, Samsung, Nestle">
                        <div class="invalid-feedback" id="error-nombre"></div>
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
    let tablaMarcas;
    const modalElement = document.getElementById('modalMarca');
    const modalBS = new bootstrap.Modal(modalElement);

    $(document).ready(function() {
        // Al terminar de abrirse el modal, enfocar el campo de texto
        $('#modalMarca').on('shown.bs.modal', function() {
            $('#nombre').trigger('focus');
        });

        tablaMarcas = $('#tablaMarcas').DataTable({
            "ajax": "<?= base_url('marcas/getMarcas') ?>",
            "columns": [
                { "data": "id_marca" },
                { "data": "nombre" },
                {
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `
                            <button class="btn btn-warning btn-sm me-1" onclick="editarMarca(${row.id_marca})" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="eliminarMarca(${row.id_marca})" title="Eliminar">
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

        $('#formMarca').on('submit', function(e) {
            e.preventDefault();
            limpiarErrores();

            $.ajax({
                url: "<?= base_url('marcas/guardar') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "JSON",
                success: function(response) {
                    if (response.status === 'success') {
                        modalBS.hide();
                        tablaMarcas.ajax.reload();
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                    } else if (response.status === 'error') {
                        if (response.errors && response.errors.nombre) {
                            $('#nombre').addClass('is-invalid');
                            $('#error-nombre').text(response.errors.nombre);
                            $('#nombre').trigger('focus');
                        }
                    }
                }
            });
        });
    });

    function abrirModalCrear() {
        $('#formMarca')[0].reset();
        $('#id_marca').val('');
        limpiarErrores();
        $('#modalTitle').text('Nueva Marca');
        modalBS.show();
    }

    function editarMarca(id) {
        limpiarErrores();
        $.get("<?= base_url('marcas/obtener/') ?>" + id, function(response) {
            if (response.status === 'success') {
                $('#id_marca').val(response.data.id_marca);
                $('#nombre').val(response.data.nombre);
                $('#modalTitle').text('Editar Marca');
                modalBS.show();
            } else {
                Toast.fire({
                    icon: 'error',
                    title: response.message
                });
            }
        });
    }

    function eliminarMarca(id) {
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
                    url: "<?= base_url('marcas/eliminar/') ?>" + id,
                    type: "DELETE",
                    dataType: "JSON",
                    success: function(response) {
                        if (response.status === 'success') {
                            tablaMarcas.ajax.reload();
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

    function limpiarErrores() {
        $('#nombre').removeClass('is-invalid');
        $('#error-nombre').text('');
    }
</script>
<?= $this->endSection() ?>