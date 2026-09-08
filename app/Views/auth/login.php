<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Sistema de Facturación</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">

    <!-- Estilos para el Fondo Difuminado y Estático -->
    <style>
        body.login-page {
            position: relative;
            background: none !important; /* Anulamos el color de fondo por defecto */
            overflow-x: hidden;
        }

        /* Capa de imagen de fondo difuminada */
        body.login-page::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            /* Reemplaza esta URL por la ruta de tu imagen */
            background-image: url('<?= base_url("assets/img/background.png") ?>'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            
            /* Difuminado y opacidad para mejorar contraste UX */
            filter: blur(8px);
            transform: scale(1.05); /* Evita bordes blancos cortados por el desenfoque */
            opacity: 0.6; 
            
            z-index: -1; /* Mantiene la imagen detrás del contenido */
        }

        /* Elevación visual de la tarjeta sobre el fondo */
        .login-box {
            position: relative;
            z-index: 1;
        }
        
        .card {
            backdrop-filter: blur(10px); /* Efecto cristal (Glassmorphism) moderno */
            background-color: rgba(255, 255, 255, 0.9) !important;
        }
    </style>
</head>
<body class="login-page d-flex align-items-center justify-content-center min-vh-100">
    
    <div class="login-box w-100" style="max-width: 400px;">
        <div class="login-logo text-center mb-4">
            <a href="#" class="h2 text-decoration-none fw-bold text-dark">
                <i class="bi bi-receipt text-primary me-2"></i>Facturación App
            </a>
        </div>
        
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body login-card-body p-4">
                <p class="login-box-msg text-center text-muted mb-4">Ingresa tus credenciales para iniciar sesión</p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('login/authenticate') ?>" method="post" autocomplete="off">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label text-secondary small">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" id="username" class="form-control" placeholder="admin" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label text-secondary small">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary fw-semibold py-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <p class="text-center text-dark fw-medium small mt-4">&copy; <?= date('Y') ?> Sistema de Facturación</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/adminlte/dist/js/adminlte.min.js') ?>"></script>
</body>
</html>