<?php

namespace App\Controllers;

use App\Models\ClienteModel;

class ClientesController extends BaseController
{
    protected $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
    }

    public function index()
    {
        return view('clientes/index');
    }

    public function getClientes()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $clientes = $this->clienteModel->findAll();
        return $this->response->setJSON(['data' => $clientes]);
    }


    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id             = $this->request->getPost('id_cliente');
        $identificacion = trim((string) $this->request->getPost('identificacion'));
        $nombre         = trim((string) $this->request->getPost('nombre'));
        $telefono       = trim((string) $this->request->getPost('telefono'));
        $correo         = trim((string) $this->request->getPost('correo'));

        $data = [
            'identificacion' => $identificacion,
            'nombre'         => $nombre,
            'telefono'       => $telefono !== '' ? $telefono : null,
            'correo'         => $correo !== '' ? $correo : null,
        ];

        if (!empty($id)) {
            // Ajustamos dinámicamente la regla is_unique para que reemplace {id_cliente} por el ID real en la edición
            $ruleUnique = "required|validar_cedula_ec|is_unique[cliente.identificacion,id_cliente,{$id}]";
            $this->clienteModel->setValidationRule('identificacion', $ruleUnique);

            if (!$this->clienteModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->clienteModel->errors()
                ]);
            }
            $message = 'Cliente actualizado correctamente.';
        } else {
            if (!$this->clienteModel->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->clienteModel->errors()
                ]);
            }
            $message = 'Cliente registrado con éxito.';
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $message
        ]);
    }


    public function obtener($id)
    {
        $cliente = $this->clienteModel->find($id);
        if (!$cliente) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cliente no encontrado.']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $cliente]);
    }

    public function eliminar($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        try {
            if ($this->clienteModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Cliente eliminado con éxito.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No se puede eliminar el cliente porque tiene ventas asociadas.'
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al intentar eliminar.']);
    }
}
