<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    public function index()
    {
        // Si ya está autenticado, redirigir al módulo principal
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('facturas'));
        }
        return view('auth/login');
    }

    public function authenticate()
    {
        // Se valida entrada de correo/usuario y contraseña
        $correo   = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if (empty($correo) || empty($password)) {
            return redirect()->back()->with('error', 'Por favor, ingrese el correo y la contraseña.');
        }

        $usuarioModel = new UsuarioModel();

        // 1. Buscar al usuario por correo electrónico
        $usuario = $usuarioModel->where('correo', $correo)->first();

        // 2. Verificar existencia del usuario
        if (!$usuario) {
            return redirect()->back()->with('error', 'El correo o la contraseña son incorrectos.');
        }

        // 3. Verificar si la cuenta está activa (estado == 1 o true)
        if (!$usuario['estado']) {
            return redirect()->back()->with('error', 'Su cuenta se encuentra inactiva. Contacte al administrador.');
        }

        

        // 5. Crear la sesión con los datos reales del usuario
        session()->set([
            'id_usuario' => $usuario['id_usuario'],
            'nombre'     => $usuario['nombre'],
            'correo'     => $usuario['correo'],
            'rol'        => $usuario['rol'],
            'isLoggedIn' => true
        ]);

        return redirect()->to(site_url('facturas'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}