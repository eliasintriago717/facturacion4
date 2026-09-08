<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <!-- Start Navbar Links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="<?= base_url() ?>" class="nav-link">Inicio</a>
            </li>
        </ul>

        <!-- Right Navbar Links -->
        <ul class="navbar-nav ms-auto">
    <!-- User Menu Dropdown -->
    <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle fs-5 text-primary"></i>
            <span class="d-none d-md-inline fw-semibold text-body">
                <?= esc(session('nombre') ?? 'Usuario') ?>
            </span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0 overflow-hidden" style="min-width: 240px;">
            <!-- Header con información del usuario -->
            <li class="user-header bg-body-tertiary p-3 text-center border-bottom">
                <div class="mb-2">
                    <i class="bi bi-person-circle text-secondary display-6"></i>
                </div>
                <p class="mb-0 fw-bold text-truncate" title="<?= esc(session('nombre') ?? 'Usuario') ?>">
                    <?= esc(session('nombre') ?? 'Usuario') ?>
                </p>
                <small class="text-body-secondary d-block mb-2">
                    <?= esc(session('correo') ?? '') ?>
                </small>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">
                    <?= esc(ucfirst((string) (session('rol') ?? ''))) ?>
                </span>
            </li>

            <!-- Opciones rápidas de usuario -->
            <li class="pt-1">
                <a href="<?= base_url('perfil') ?>" class="dropdown-item py-2 px-3 d-flex align-items-center gap-2">
                    <i class="bi bi-person text-body-secondary fs-6"></i>
                    <span>Mi Perfil</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('configuracion') ?>" class="dropdown-item py-2 px-3 d-flex align-items-center gap-2">
                    <i class="bi bi-gear text-body-secondary fs-6"></i>
                    <span>Configuración</span>
                </a>
            </li>

            <li><hr class="dropdown-divider my-1"></li>

            <!-- Footer: Cerrar Sesión -->
            <li class="user-footer p-2 bg-body-tertiary border-top">
                <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </li>
</ul>



    </div>
</nav>