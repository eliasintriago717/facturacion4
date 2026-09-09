<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * DashboardModel
 *
 * Centraliza las consultas de agregación (KPIs, series de tiempo y rankings)
 * que alimentan el panel de control. No mapea una tabla específica: opera
 * directamente sobre venta, detalle_venta, cliente y producto.
 */
class DashboardModel extends Model
{
    // Umbral por defecto para considerar que un producto necesita reabastecerse.
    protected int $umbralStockBajo = 5;

    protected function db()
    {
        return \Config\Database::connect();
    }

    /**
     * Todas las métricas del dashboard en una sola estructura,
     * lista para serializar a JSON.
     */
    public function getResumenCompleto(): array
    {
        return [
            'kpis'               => $this->getKpis(),
            'actividad_semanal'  => $this->getActividadUltimosDias(7),
            'ingresos_mensuales' => $this->getIngresosMensuales(6),
            'productos_top'      => $this->getProductosMasVendidos(5),
            'alertas_inventario' => $this->getAlertasInventario(),
            'generado_en'        => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Tarjetas KPI superiores: ventas de hoy, ingresos del mes,
     * clientes registrados y stock por revisar.
     */
    public function getKpis(): array
    {
        $db = $this->db();

        $ventasHoy = $db->table('venta')
            ->where('DATE(fecha)', date('Y-m-d'))
            ->countAllResults();

        $ingresosMes = $db->table('venta')
            ->selectSum('total')
            ->where('YEAR(fecha)', date('Y'))
            ->where('MONTH(fecha)', date('n'))
            ->get()
            ->getRow('total');

        $clientesRegistrados = $db->table('cliente')->countAllResults();

        $stockPorRevisar = $db->table('producto')
            ->where('stock <=', $this->umbralStockBajo)
            ->countAllResults();

        return [
            'ventas_hoy'            => (int) $ventasHoy,
            'ingresos_mes'          => (float) ($ingresosMes ?? 0),
            'clientes_registrados'  => (int) $clientesRegistrados,
            'stock_por_revisar'     => (int) $stockPorRevisar,
        ];
    }

    /**
     * Ventas e ingresos de cada uno de los últimos $dias días
     * (incluye días sin ventas, rellenados en 0).
     */
    public function getActividadUltimosDias(int $dias = 7): array
    {
        $db = $this->db();

        $desde = date('Y-m-d', strtotime("-" . ($dias - 1) . " days"));

        $filas = $db->table('venta')
            ->select("DATE(fecha) AS dia, COUNT(*) AS ventas, SUM(total) AS ingresos")
            ->where('DATE(fecha) >=', $desde)
            ->groupBy('DATE(fecha)')
            ->orderBy('dia', 'ASC')
            ->get()
            ->getResultArray();

        // Indexar por fecha para poder rellenar los días sin actividad.
        $porFecha = [];
        foreach ($filas as $fila) {
            $porFecha[$fila['dia']] = $fila;
        }

        $resultado = [];
        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $resultado[] = [
                'fecha'    => $fecha,
                'etiqueta' => date('d/m', strtotime($fecha)),
                'ventas'   => isset($porFecha[$fecha]) ? (int) $porFecha[$fecha]['ventas'] : 0,
                'ingresos' => isset($porFecha[$fecha]) ? (float) $porFecha[$fecha]['ingresos'] : 0.0,
            ];
        }

        return $resultado;
    }

    /**
     * Ingresos totales agrupados por mes, para los últimos $meses meses
     * (incluye meses sin ventas, rellenados en 0).
     */
    public function getIngresosMensuales(int $meses = 6): array
    {
        $db = $this->db();

        $desde = date('Y-m-01', strtotime("-" . ($meses - 1) . " months"));

        $filas = $db->table('venta')
            ->select("DATE_FORMAT(fecha, '%Y-%m') AS mes, SUM(total) AS ingresos")
            ->where('fecha >=', $desde)
            ->groupBy("DATE_FORMAT(fecha, '%Y-%m')")
            ->orderBy('mes', 'ASC')
            ->get()
            ->getResultArray();

        $porMes = [];
        foreach ($filas as $fila) {
            $porMes[$fila['mes']] = (float) $fila['ingresos'];
        }

        $resultado = [];
        for ($i = $meses - 1; $i >= 0; $i--) {
            $clave = date('Y-m', strtotime("-$i months"));
            $resultado[] = [
                'mes'      => $clave,
                'etiqueta' => ucfirst(strftime_es($clave)),
                'ingresos' => $porMes[$clave] ?? 0.0,
            ];
        }

        return $resultado;
    }

    /**
     * Ranking de productos más vendidos históricamente
     * (unidades e ingresos generados).
     */
    public function getProductosMasVendidos(int $limite = 5): array
    {
        $db = $this->db();

        return $db->table('detalle_venta dv')
            ->select("producto.nombre AS producto, SUM(dv.cantidad) AS unidades, SUM(dv.subtotal) AS ingresos")
            ->join('producto', 'producto.id_producto = dv.id_producto')
            ->groupBy('dv.id_producto')
            ->orderBy('unidades', 'DESC')
            ->limit($limite)
            ->get()
            ->getResultArray();
    }

    /**
     * Productos cuyo stock está en o por debajo del umbral definido.
     */
    public function getAlertasInventario(): array
    {
        $db = $this->db();

        return $db->table('producto')
            ->select('id_producto, nombre, precio_venta, stock')
            ->where('stock <=', $this->umbralStockBajo)
            ->orderBy('stock', 'ASC')
            ->get()
            ->getResultArray();
    }
}

/**
 * Traduce el mes de una clave 'Y-m' a un formato corto en español
 * sin depender de la configuración de locale del servidor.
 */
if (!function_exists('strftime_es')) {
    function strftime_es(string $anioMes): string
    {
        $meses = [
            '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr',
            '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
            '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic',
        ];

        [$anio, $mes] = explode('-', $anioMes);

        return ($meses[$mes] ?? $mes) . ' ' . $anio;
    }
}
