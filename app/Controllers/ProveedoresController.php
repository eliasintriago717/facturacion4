<?php

namespace App\Controllers;

use App\Models\ProveedorModel;

class ProveedoresController extends BaseController
{
    protected $proveedorModel;

    public function __construct()
    {
        $this->proveedorModel = new ProveedorModel();
    }

    public function index()
    {
        return view('proveedores/index');
    }

    public function getProveedores()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $proveedores = $this->proveedorModel->findAll();
        return $this->response->setJSON(['data' => $proveedores]);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id             = $this->request->getPost('id_proveedor');
        $identificacion = trim((string) $this->request->getPost('identificacion'));
        $nombre         = trim((string) $this->request->getPost('nombre'));
        $telefono       = trim((string) $this->request->getPost('telefono'));

        $data = [
            'identificacion' => $identificacion,
            'nombre'         => $nombre,
            'telefono'       => $telefono !== '' ? $telefono : null,
        ];

        if (!empty($id)) {
            // Ajustamos dinámicamente la regla is_unique para reemplazo estricto en edición
            $ruleUnique = "required|min_length[10]|max_length[20]|is_unique[proveedor.identificacion,id_proveedor,{$id}]";
            $this->proveedorModel->setValidationRule('identificacion', $ruleUnique);

            if (!$this->proveedorModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->proveedorModel->errors()
                ]);
            }
            $message = 'Proveedor actualizado correctamente.';
        } else {
            if (!$this->proveedorModel->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->proveedorModel->errors()
                ]);
            }
            $message = 'Proveedor registrado con éxito.';
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $message
        ]);
    }

    public function obtener($id)
    {
        $proveedor = $this->proveedorModel->find($id);
        if (!$proveedor) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Proveedor no encontrado.']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $proveedor]);
    }

    public function eliminar($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        try {
            if ($this->proveedorModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Proveedor eliminado con éxito.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No se puede eliminar el proveedor porque tiene compras asociadas.'
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al intentar eliminar.']);
    }
}