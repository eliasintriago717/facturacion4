<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class UsuariosController extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        return view('usuarios/index');
    }

    public function getUsuarios()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // Se selecciona sin exponer el hash de la contraseña
        $usuarios = $this->usuarioModel->select('id_usuario, nombre, correo, rol, estado')->findAll();
        return $this->response->setJSON(['data' => $usuarios]);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id     = $this->request->getPost('id_usuario');
        $nombre = trim((string) $this->request->getPost('nombre'));
        $correo = trim((string) $this->request->getPost('correo'));
        $clave  = $this->request->getPost('clave');
        $rol    = $this->request->getPost('rol');
        $estado = $this->request->getPost('estado') ? 1 : 0;

        // Regla obligatoria para la contraseña únicamente en registros nuevos
        if (empty($id) && empty($clave)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['clave' => 'La contraseña es obligatoria para nuevos usuarios.']
            ]);
        }

        $data = [
            'nombre' => $nombre,
            'correo' => $correo,
            'rol'    => $rol,
            'estado' => $estado
        ];

        if (!empty($clave)) {
            $data['clave'] = $clave;
        }

        if (!empty($id)) {
            if (!$this->usuarioModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->usuarioModel->errors()
                ]);
            }
            $message = 'Usuario actualizado correctamente.';
        } else {
            if (!$this->usuarioModel->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->usuarioModel->errors()
                ]);
            }
            $message = 'Usuario registrado con éxito.';
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $message
        ]);
    }

    public function obtener($id)
    {
        $usuario = $this->usuarioModel->select('id_usuario, nombre, correo, rol, estado')->find($id);
        if (!$usuario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario no encontrado.']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $usuario]);
    }

    public function eliminar($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        try {
            if ($this->usuarioModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario eliminado con éxito.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No se puede eliminar el usuario porque tiene ventas o compras registradas asociadas.'
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al intentar eliminar.']);
    }
}