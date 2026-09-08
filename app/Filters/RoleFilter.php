<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // 1. Si no hay sesión activa, redirigir al login
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $userRole = $session->get('rol');

        // 2. Comprobar permisos según los roles permitidos en la ruta
        if (! empty($arguments) && ! in_array($userRole, $arguments, true)) {
            
            // Verificación segura para peticiones AJAX o Header Accept
            $isAjax = $request->isAJAX() || 
                      $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' ||
                      str_contains($request->getHeaderLine('Accept'), 'application/json');

            if ($isAjax) {
                return response()
                    ->setStatusCode(403)
                    ->setJSON(['error' => 'No tienes permisos suficientes para realizar esta acción.']);
            }

            // Si es un usuario "encargado" intentando entrar a un módulo de administrador
            if ($userRole === 'encargado') {
                return redirect()->to(site_url('facturas'))
                    ->with('error', 'No tienes acceso a los módulos administrativos.');
            }

            return redirect()->to(site_url('login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}