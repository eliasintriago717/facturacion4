<?php

namespace App\Controllers;

use App\Models\ProductoModel;
use App\Models\CategoriaModel;
use App\Models\MarcaModel;

class ProductosController extends BaseController
{
    protected $productoModel;
    protected $categoriaModel;
    protected $marcaModel;

    public function __construct()
    {
        $this->productoModel  = new ProductoModel();
        $this->categoriaModel = new CategoriaModel();
        $this->marcaModel     = new MarcaModel();
    }

    public function index()
    {
        $data = [
            'categorias' => $this->categoriaModel->findAll(),
            'marcas'     => $this->marcaModel->findAll()
        ];
        return view('productos/index', $data);
    }

    public function getProductos()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $productos = $this->productoModel->getProductosConRelaciones();
        return $this->response->setJSON(['data' => $productos]);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id           = $this->request->getPost('id_producto');
        $codigoBarras = trim((string) $this->request->getPost('codigo_barras'));
        $nombre       = trim((string) $this->request->getPost('nombre'));
        $idCategoria  = $this->request->getPost('id_categoria');
        $idMarca      = $this->request->getPost('id_marca');
        $precioVenta  = $this->request->getPost('precio_venta');
        $stock        = $this->request->getPost('stock');

        $data = [
            'codigo_barras' => empty($codigoBarras) ? null : $codigoBarras,
            'nombre'        => $nombre,
            'id_categoria'  => $idCategoria,
            'id_marca'      => $idMarca,
            'precio_venta'  => $precioVenta,
            'stock'         => $stock
        ];

        if (!empty($id)) {
            if (!$this->productoModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->productoModel->errors()
                ]);
            }
            $message = 'Producto actualizado correctamente.';
        } else {
            if (!$this->productoModel->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->productoModel->errors()
                ]);
            }
            $message = 'Producto registrado con éxito.';
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $message
        ]);
    }

    public function obtener($id)
    {
        $producto = $this->productoModel->find($id);
        if (!$producto) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Producto no encontrado.']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $producto]);
    }

    public function eliminar($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        try {
            if ($this->productoModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Producto eliminado con éxito.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No se puede eliminar el producto porque tiene detalle de ventas o compras asociadas.'
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al intentar eliminar.']);
    }
}