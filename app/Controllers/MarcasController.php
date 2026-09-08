<?php
namespace App\Controllers;

use App\Models\MarcaModel;

class MarcasController extends BaseController
{
    protected $marcaModel;

    public function __construct()
    {
        $this->marcaModel = new MarcaModel();
    }

    public function index()
    {
        return view('marcas/index');
    }

    public function getMarcas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $marcas = $this->marcaModel->findAll();
        return $this->response->setJSON(['data' => $marcas]);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id     = $this->request->getPost('id_marca');
        $nombre = trim((string) $this->request->getPost('nombre'));

        $data = [
            'nombre' => $nombre
        ];

        if (!empty($id)) {
            // En edición usas update(). Pasa el ID explícitamente para que {id_marca} se reemplace
            if (!$this->marcaModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->marcaModel->errors()
                ]);
            }
            $message = 'Marca actualizada correctamente.';
        } else {
            // En inserción usas insert()
            if (!$this->marcaModel->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->marcaModel->errors()
                ]);
            }
            $message = 'Marca registrada con éxito.';
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $message
        ]);
    }

    public function obtener($id)
    {
        $marca = $this->marcaModel->find($id);
        if (!$marca) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Marca no encontrada.']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $marca]);
    }

    public function eliminar($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        try {
            if ($this->marcaModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Marca eliminada con éxito.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No se puede eliminar la marca porque tiene productos asociados.'
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al intentar eliminar.']);
    }
}