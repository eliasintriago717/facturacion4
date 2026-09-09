<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Sistema de Facturación</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <style>
        :root {
            --primary-color: #1e3a8a;
            --accent-color: #2563eb;
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }

        body {
            font-family: 'Source Sans 3', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 900px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
        }

        /* Hero / Branding Section */
        .brand-section {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .brand-section::after {
            content: "";
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background: url('https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&q=80&w=1000') center/cover;
            opacity: 0.12;
            mix-blend-mode: overlay;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1rem;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .feature-item i {
            font-size: 1.25rem;
            color: #93c5fd;
        }

        /* Formulario */
        .form-section {
            padding: 3rem 2.5rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        .input-group-text {
            border-radius: 8px 0 0 8px;
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="row g-0">
            <!-- Panel Izquierdo: Branding corporativo -->
            <div class="col-lg-6 brand-section d-none d-lg-flex">
                <div style="position: relative; z-index: 2;">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-receipt-cutoff fs-2 text-warning"></i>
                        <span class="fs-4 fw-bold tracking-wide">FacturaApp</span>
                    </div>
                    <h3 class="fw-bold mb-3">Gestión Financiera & Facturación Electrónica</h3>
                    <p class="text-light opacity-75 small">Administra tus comprobantes, inventario y clientes en una plataforma rápida y segura.</p>
                </div>

                <div class="mt-4" style="position: relative; z-index: 2;">
                    <div class="feature-item">
                        <i class="bi bi-shield-check"></i>
                        <span>Emisión segura y rápida de facturas</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Reportes y estadísticas en tiempo real</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-cloud-check"></i>
                        <span>Respaldo de datos automático</span>
                    </div>
                </div>

                <div class="mt-auto pt-4 border-top border-white-10" style="position: relative; z-index: 2;">
                    <small class="opacity-75">&copy; <?= date('Y') ?> Sistema de Facturación. Todos los derechos reservados.</small>
                </div>
            </div>

            <!-- Panel Derecho: Formulario de Login -->
            <div class="col-lg-6 form-section bg-white">
                <div class="mb-4 text-center text-lg-start">
                    <div class="d-lg-none d-flex align-items-center justify-content-center gap-2 mb-3">
                        <i class="bi bi-receipt-cutoff fs-2 text-primary"></i>
                        <span class="fs-4 fw-bold">FacturaApp</span>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">¡Bienvenido de nuevo!</h4>
                    <p class="text-muted small">Ingresa tus credenciales para acceder al sistema</p>
                </div>

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
                        <label for="username" class="form-label text-dark fw-medium small">Usuario o Correo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" id="username" class="form-control" placeholder="admin" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label text-dark fw-medium small mb-0">Contraseña</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                        </button>
                    </div>
                </form>

                <p class="text-center text-muted small mt-4 d-lg-none">&copy; <?= date('Y') ?> Sistema de Facturación</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>