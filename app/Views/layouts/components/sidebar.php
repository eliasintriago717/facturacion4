<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="<?= base_url() ?>" class="brand-link">
            <span class="brand-text fw-light">Facturación App</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <!-- Opción Accesible por Administrador y Encargado -->
                <li class="nav-item">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link <?= url_is('dashboard') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Opción Accesible por Administrador y Encargado: Facturación -->
                <li class="nav-item">
                    <a href="<?= base_url('facturas') ?>" class="nav-link <?= url_is('facturas*') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-receipt"></i>
                        <p>Facturación</p>
                    </a>
                </li>

                <!-- Opciones Exclusivas para el Administrador -->
                <?php if (session()->get('rol') === 'administrador'): ?>

                    <li class="nav-item">
                        <a href="<?= base_url('admin/categorias') ?>" class="nav-link <?= url_is('admin/categorias*') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-tags"></i>
                            <p>Categorías</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('admin/marcas') ?>" class="nav-link <?= url_is('admin/marcas*') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-bookmark-star"></i>
                            <p>Marcas</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('admin/clientes') ?>" class="nav-link <?= url_is('admin/clientes*') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-people"></i>
                            <p>Clientes</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('admin/proveedores') ?>" class="nav-link <?= url_is('admin/proveedores*') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-truck"></i>
                            <p>Proveedores</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('admin/productos') ?>" class="nav-link <?= url_is('admin/productos*') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-box-seam"></i>
                            <p>Productos</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('admin/compras') ?>" class="nav-link <?= url_is('admin/compras*') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-bag-plus"></i>
                            <p>Compras</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('admin/usuarios') ?>" class="nav-link <?= url_is('admin/usuarios*') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-person-gear"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </nav>
    </div>
</aside>