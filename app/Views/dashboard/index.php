<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .kpi-card {
        border: none;
        border-radius: .75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        transition: box-shadow .2s ease;
    }
    .kpi-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); }
    .kpi-icon {
        width: 44px; height: 44px;
        border-radius: .6rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }
    .kpi-value { font-size: 1.6rem; font-weight: 700; line-height: 1.2; }
    .kpi-label { font-size: .8rem; color: #6c757d; }
    .kpi-sub { font-size: .72rem; color: #adb5bd; }
    .chart-card { border: none; border-radius: .75rem; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
    .badge-alerta { font-size: .72rem; }
    #dashboardUpdatedAt { font-size: .75rem; color: #adb5bd; }
    .live-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #20c997; display: inline-block; margin-right: 5px;
        animation: pulse 1.6s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(32,201,151,.5); }
        70% { box-shadow: 0 0 0 6px rgba(32,201,151,0); }
        100% { box-shadow: 0 0 0 0 rgba(32,201,151,0); }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Revisa el pulso del negocio y detecta lo que necesita atención hoy.</p>
    <div class="text-end">
        <span class="live-dot"></span>
        <span id="dashboardUpdatedAt">Actualizando...</span>
        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="btnRefrescarDashboard" title="Actualizar ahora">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
    </div>
</div>

<!-- KPIs -->
<div class="row g-3 mb-3">
    <div class="col-6 col-xl-3">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-start gap-3">
                <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <div class="kpi-label">Ventas de hoy</div>
                    <div class="kpi-value" id="kpiVentasHoy">0</div>
                    <div class="kpi-sub">transacciones registradas</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-start gap-3">
                <div class="kpi-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <div class="kpi-label">Ingresos del mes</div>
                    <div class="kpi-value" id="kpiIngresosMes">$0.00</div>
                    <div class="kpi-sub">acumulado mensual</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-start gap-3">
                <div class="kpi-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="kpi-label">Clientes registrados</div>
                    <div class="kpi-value" id="kpiClientes">0</div>
                    <div class="kpi-sub">base de clientes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-start gap-3">
                <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="kpi-label">Stock por revisar</div>
                    <div class="kpi-value" id="kpiStockBajo">0</div>
                    <div class="kpi-sub">productos con 5 o menos unidades</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row g-3 mb-3">
    <div class="col-12 col-xl-8">
        <div class="card chart-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-0">Actividad de los últimos 7 días</h6>
                        <small class="text-muted">Ventas e ingresos diarios</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary"><i class="bi bi-graph-up-arrow me-1"></i>Tendencia</span>
                </div>
                <div style="height: 300px;">
                    <canvas id="chartActividad"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card chart-card h-100">
            <div class="card-body">
                <h6 class="mb-0">Ingresos mensuales</h6>
                <small class="text-muted">Últimos 6 meses</small>
                <div style="height: 300px;">
                    <canvas id="chartIngresosMensuales"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla productos top + alertas -->
<div class="row g-3">
    <div class="col-12 col-xl-6">
        <div class="card chart-card h-100">
            <div class="card-body">
                <h6 class="mb-0"><i class="bi bi-bar-chart-line me-1 text-primary"></i>Productos más vendidos</h6>
                <small class="text-muted">Unidades colocadas históricamente</small>
                <div class="table-responsive mt-2">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>PRODUCTO</th>
                                <th class="text-center">UNIDADES</th>
                                <th class="text-end">INGRESOS</th>
                            </tr>
                        </thead>
                        <tbody id="tablaProductosTop">
                            <tr><td colspan="3" class="text-center text-muted py-3">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card chart-card h-100">
            <div class="card-body">
                <h6 class="mb-0"><i class="bi bi-box-seam me-1 text-warning"></i>Alertas de inventario</h6>
                <small class="text-muted">Productos que requieren atención</small>
                <div id="listaAlertasInventario" class="mt-2">
                    <p class="text-center text-muted py-3">Cargando...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
$(function () {
    const REFRESH_MS = 30000; // Frecuencia de actualización "en vivo" (30s)
    const urlData = "<?= base_url('dashboard/data') ?>";

    let chartActividad = null;
    let chartMensual = null;

    function formatoMoneda(valor) {
        return '$' + Number(valor).toLocaleString('es-EC', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function pintarKpis(kpis) {
        $('#kpiVentasHoy').text(kpis.ventas_hoy);
        $('#kpiIngresosMes').text(formatoMoneda(kpis.ingresos_mes));
        $('#kpiClientes').text(kpis.clientes_registrados);
        $('#kpiStockBajo').text(kpis.stock_por_revisar);
    }

    function pintarChartActividad(actividad) {
        const etiquetas = actividad.map(d => d.etiqueta);
        const ventas = actividad.map(d => d.ventas);
        const ingresos = actividad.map(d => d.ingresos);

        if (!chartActividad) {
            const ctx = document.getElementById('chartActividad').getContext('2d');
            chartActividad = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: etiquetas,
                    datasets: [
                        {
                            label: 'Ventas',
                            data: ventas,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13,110,253,.08)',
                            tension: .35,
                            fill: true,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Ingresos ($)',
                            data: ingresos,
                            borderColor: '#fd7e14',
                            backgroundColor: 'rgba(253,126,20,.08)',
                            tension: .35,
                            fill: false,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y:  { position: 'left', beginAtZero: true, ticks: { precision: 0 } },
                        y1: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false } }
                    },
                    plugins: { legend: { position: 'top', align: 'end' } }
                }
            });
        } else {
            chartActividad.data.labels = etiquetas;
            chartActividad.data.datasets[0].data = ventas;
            chartActividad.data.datasets[1].data = ingresos;
            chartActividad.update();
        }
    }

    function pintarChartMensual(mensual) {
        const etiquetas = mensual.map(m => m.etiqueta);
        const ingresos = mensual.map(m => m.ingresos);

        if (!chartMensual) {
            const ctx = document.getElementById('chartIngresosMensuales').getContext('2d');
            chartMensual = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: etiquetas,
                    datasets: [{
                        label: 'Ingresos',
                        data: ingresos,
                        backgroundColor: 'rgba(13,202,240,.45)',
                        borderRadius: 6,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        } else {
            chartMensual.data.labels = etiquetas;
            chartMensual.data.datasets[0].data = ingresos;
            chartMensual.update();
        }
    }

    function pintarProductosTop(productos) {
        const $tbody = $('#tablaProductosTop').empty();
        if (!productos.length) {
            $tbody.append('<tr><td colspan="3" class="text-center text-muted py-3">Aún no hay ventas registradas.</td></tr>');
            return;
        }
        productos.forEach(p => {
            $tbody.append(`
                <tr>
                    <td>${p.producto}</td>
                    <td class="text-center"><span class="badge bg-primary-subtle text-primary">${p.unidades}</span></td>
                    <td class="text-end">${formatoMoneda(p.ingresos)}</td>
                </tr>
            `);
        });
    }

    function pintarAlertas(alertas) {
        const $cont = $('#listaAlertasInventario').empty();
        if (!alertas.length) {
            $cont.append('<p class="text-center text-muted py-3 mb-0">Todo el inventario está en niveles saludables.</p>');
            return;
        }
        alertas.forEach(a => {
            $cont.append(`
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <div class="fw-semibold">${a.nombre}</div>
                        <small class="text-muted">Precio: ${formatoMoneda(a.precio_venta)}</small>
                    </div>
                    <span class="badge bg-danger-subtle text-danger badge-alerta">${a.stock} unid.</span>
                </div>
            `);
        });
    }

    function cargarDashboard() {
        $.getJSON(urlData)
            .done(function (resp) {
                if (resp.status !== 'success') return;
                const d = resp.data;
                pintarKpis(d.kpis);
                pintarChartActividad(d.actividad_semanal);
                pintarChartMensual(d.ingresos_mensuales);
                pintarProductosTop(d.productos_top);
                pintarAlertas(d.alertas_inventario);
                $('#dashboardUpdatedAt').text('Actualizado ' + new Date().toLocaleTimeString('es-EC'));
            })
            .fail(function () {
                $('#dashboardUpdatedAt').text('No se pudo actualizar');
            });
    }

    cargarDashboard();
    setInterval(cargarDashboard, REFRESH_MS);
    $('#btnRefrescarDashboard').on('click', cargarDashboard);
});
</script>
<?= $this->endSection() ?>
