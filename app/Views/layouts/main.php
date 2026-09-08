<!DOCTYPE html>
<html lang="es">

<!-- Cargar Head -->
<?= $this->include('layouts/components/head') ?>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">

        <!-- Cargar Navbar -->
        <?= $this->include('layouts/components/navbar') ?>

        <!-- Cargar Sidebar -->
        <?= $this->include('layouts/components/sidebar') ?>

        <!-- Main Content Wrapper -->
        <main class="app-main">
            <!-- Header de la página (Título y Breadcrumb) -->
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0"><?= $this->renderSection('page_title') ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido dinámico de cada vista -->
            <div class="app-content">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </main>

        <!-- Cargar Footer -->
        <?= $this->include('layouts/components/footer') ?>

    </div>

    <!-- Cargar Scripts -->
    <?= $this->include('layouts/components/scripts') ?>
</body>
</html>
