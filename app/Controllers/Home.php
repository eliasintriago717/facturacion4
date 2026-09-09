<?php

namespace App\Controllers;

use App\Models\DashboardModel;

class Home extends BaseController
{
    protected DashboardModel $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }

    /**
     * Vista principal del dashboard.
     */
    public function index()
    {
        return view('dashboard/index');
    }

    /**
     * Endpoint AJAX consumido por dashboard/index.php para poblar
     * y refrescar en tiempo real las tarjetas KPI y los gráficos.
     */
    public function getDashboardData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $data = $this->dashboardModel->getResumenCompleto();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data,
        ]);
    }
}
