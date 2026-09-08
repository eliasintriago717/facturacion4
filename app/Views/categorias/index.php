<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Categorías<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Administración de Categorías<?= $this->endSection() ?>

<!-- Cargar CSS de DataTables en el <head> -->
<?= $this->section('styles') ?>

<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Listado de Categorías</h3>
        <button type="button" class="btn btn-primary btn-sm ms-auto" onclick="abrirModalCrear()">
            <i class="bi bi-plus-lg"></i> Nueva Categoría
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaCategorias" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th style="width: 80px;" class="text-center">ID</th>
                        <th>Nombre de la Categoría</th>
                        <th style="width: 120px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Registrar / Editar -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalTitle">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formCategoria" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_categoria" name="id_categoria">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Lácteos, Bebidas">
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

<!-- Cargar JS específicos de esta vista al final -->
<?= $this->section('scripts') ?>

<script>
    let tablaCategorias;
    const modalElement = document.getElementById('modalCategoria');
    const modalBS = new bootstrap.Modal(modalElement);

    $(document).ready(function() {
        // Al terminar de abrirse el modal, enfocar el campo de texto
        $('#modalCategoria').on('shown.bs.modal', function() {
            $('#nombre').trigger('focus');
        });

        tablaCategorias = $('#tablaCategorias').DataTable({
            "ajax": "<?= base_url('categorias/getCategorias') ?>",
            "columns": [{
                    "data": "id_categoria"
                },
                {
                    "data": "nombre"
                },
                {
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `
                            <button class="btn btn-warning btn-sm me-1" onclick="editarCategoria(${row.id_categoria})" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="eliminarCategoria(${row.id_categoria})" title="Eliminar">
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

        $('#formCategoria').on('submit', function(e) {
            e.preventDefault();

            limpiarErrores();

            $.ajax({
                url: "<?= base_url('categorias/guardar') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "JSON",
                success: function(response) {
                    if (response.status === 'success') {
                        modalBS.hide();
                        tablaCategorias.ajax.reload();

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
        $('#formCategoria')[0].reset();
        $('#id_categoria').val('');
        limpiarErrores();
        $('#modalTitle').text('Nueva Categoría');
        modalBS.show();
    }

    function editarCategoria(id) {
        limpiarErrores();
        $.get("<?= base_url('categorias/obtener/') ?>" + id, function(response) {
            if (response.status === 'success') {
                $('#id_categoria').val(response.data.id_categoria);
                $('#nombre').val(response.data.nombre);
                $('#modalTitle').text('Editar Categoría');
                modalBS.show();
            } else {
                Toast.fire({
                    icon: 'error',
                    title: response.message
                });
            }
        });
    }

    function eliminarCategoria(id) {
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
                    url: "<?= base_url('categorias/eliminar/') ?>" + id,
                    type: "DELETE",
                    dataType: "JSON",
                    success: function(response) {
                        if (response.status === 'success') {
                            tablaCategorias.ajax.reload();

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