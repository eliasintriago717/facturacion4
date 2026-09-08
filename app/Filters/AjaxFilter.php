<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AjaxFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Usamos el helper global request() para asegurar el llamado al método exacto isAJAX()
        if (! request()->isAJAX()) {
            return response()
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Acceso denegado. Este recurso solo acepta peticiones AJAX.'
                ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Sin acciones después de la petición
    }
}
