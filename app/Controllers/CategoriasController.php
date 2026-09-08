<?php

namespace App\Controllers;

use App\Models\CategoriaModel;
use CodeIgniter\HTTP\ResponseInterface;

class CategoriasController extends BaseController
{
    protected $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

    public function index()
    {
        return view('categorias/index');
    }

    public function getCategorias()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $categorias = $this->categoriaModel->findAll();
        return $this->response->setJSON(['data' => $categorias]);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id     = $this->request->getPost('id_categoria');
        $nombre = trim((string) $this->request->getPost('nombre'));

        $data = [
            'nombre' => $nombre
        ];

        if (!empty($id)) {
            // En edición usas update(). Pasa el ID explícitamente para que {id_categoria} se reemplace
            if (!$this->categoriaModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->categoriaModel->errors()
                ]);
            }
            $message = 'Categoría actualizada correctamente.';
        } else {
            // En inserción usas insert()
            if (!$this->categoriaModel->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->categoriaModel->errors()
                ]);
            }
            $message = 'Categoría registrada con éxito.';
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $message
        ]);
    }

    public function obtener($id)
    {
        $categoria = $this->categoriaModel->find($id);
        if (!$categoria) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Categoría no encontrada.']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $categoria]);
    }

    public function eliminar($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        try {
            if ($this->categoriaModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Categoría eliminada con éxito.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No se puede eliminar la categoría porque tiene productos asociados.'
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al intentar eliminar.']);
    }
}
