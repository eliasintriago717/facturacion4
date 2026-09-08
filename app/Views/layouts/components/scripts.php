<!-- 1. jQuery (Debe ir primero para que DataTables funcione) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- 2. Bootstrap 5 JS Bundle CDN -->
    
<!-- Bootstrap 5 JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE v4 JS (Local) -->
<script src="<?= base_url('assets/adminlte/dist/js/adminlte.min.js') ?>"></script>

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="<?= base_url('assets/js/app-global.js') ?>"></script>

<!-- Scripts adicionales por sección -->
<?= $this->renderSection('scripts') ?>
