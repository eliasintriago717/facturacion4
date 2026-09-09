<?php

namespace App\Controllers;

use App\Models\VentaModel;
use App\Models\DetalleVentaModel;
use App\Models\ClienteModel;
use App\Models\ProductoModel;

class FacturacionController extends BaseController
{
    protected $ventaModel;
    protected $detalleVentaModel;
    protected $clienteModel;
    protected $productoModel;

    public function __construct()
    {
        $this->ventaModel        = new VentaModel();
        $this->detalleVentaModel = new DetalleVentaModel();
        $this->clienteModel      = new ClienteModel();
        $this->productoModel     = new ProductoModel();
    }

    public function index()
    {
        return view('facturacion/index');
    }

    public function getVentas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $ventas = $this->ventaModel->getVentasConDetalles();
        return $this->response->setJSON(['data' => $ventas]);
    }

    public function buscarClientes()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $term = trim((string) $this->request->getGet('q'));
        if (empty($term)) {
            return $this->response->setJSON([]);
        }

        $clientes = $this->clienteModel->like('identificacion', $term)
                                       ->orLike('nombre', $term)
                                       ->findAll(10);

        return $this->response->setJSON($clientes);
    }

    public function buscarProductos()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $term = trim((string) $this->request->getGet('q'));
        if (empty($term)) {
            return $this->response->setJSON([]);
        }

        $productos = $this->productoModel->like('codigo_barras', $term)
                                         ->orLike('nombre', $term)
                                         ->findAll(10);

        return $this->response->setJSON($productos);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $idCliente = $this->request->getPost('id_cliente');
        $productos = $this->request->getPost('productos'); // Array con id_producto, cantidad, precio_unitario
        $idUsuario = session()->get('id_usuario') ?? 1; // ID del usuario autenticado en sesión

        if (empty($idCliente)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Debe seleccionar un cliente.']);
        }

        if (empty($productos) || !is_array($productos)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Debe agregar al menos un producto a la factura.']);
        }

        // Validación estricta de stock antes de iniciar la transacción
        $totalVenta = 0;
        $detallesProcesados = [];

        foreach ($productos as $item) {
            $prod = $this->productoModel->find($item['id_producto']);
            if (!$prod) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Uno de los productos seleccionados no existe.']);
            }

            $cant = (int) $item['cantidad'];
            if ($cant <= 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'La cantidad para ' . $prod['nombre'] . ' debe ser mayor a cero.']);
            }

            if ($prod['stock'] < $cant) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Stock insuficiente para "' . $prod['nombre'] . '". Disponible: ' . $prod['stock'] . ', Solicitado: ' . $cant
                ]);
            }

            $precioUnitario = (float) $prod['precio_venta'];
            $subtotal = $precioUnitario * $cant;
            $totalVenta += $subtotal;

            $detallesProcesados[] = [
                'id_producto'     => $prod['id_producto'],
                'cantidad'        => $cant,
                'precio_unitario' => $precioUnitario,
                'subtotal'        => $subtotal,
                'stock_actual'    => $prod['stock']
            ];
        }

        // Inicio de transacción de base de datos
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Guardar Cabecera
        $idVenta = $this->ventaModel->insert([
            'id_cliente' => $idCliente,
            'id_usuario' => $idUsuario,
            'total'      => $totalVenta
        ]);

        // 2. Guardar Detalles y Descontar Stock
        foreach ($detallesProcesados as $det) {
            $this->detalleVentaModel->insert([
                'id_venta'        => $idVenta,
                'id_producto'     => $det['id_producto'],
                'cantidad'        => $det['cantidad'],
                'precio_unitario' => $det['precio_unitario'],
                'subtotal'        => $det['subtotal']
            ]);

            // Descontar stock
            $nuevoStock = $det['stock_actual'] - $det['cantidad'];
            $this->productoModel->update($det['id_producto'], ['stock' => $nuevoStock]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al procesar la factura.']);
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'message'  => 'Factura registrada con éxito.',
            'id_venta' => $idVenta
        ]);
    }

    public function obtener($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $venta = $this->ventaModel->select('venta.*, cliente.nombre AS cliente_nombre, cliente.identificacion AS cliente_identificacion, cliente.telefono, cliente.correo, usuario.nombre AS usuario_nombre')
                                  ->join('cliente', 'cliente.id_cliente = venta.id_cliente')
                                  ->join('usuario', 'usuario.id_usuario = venta.id_usuario')
                                  ->where('venta.id_venta', $id)
                                  ->first();

        if (!$venta) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Factura no encontrada.']);
        }

        $detalles = $this->detalleVentaModel->getDetallesPorVenta($id);

        return $this->response->setJSON([
            'status'   => 'success',
            'venta'    => $venta,
            'detalles' => $detalles
        ]);
    }
    public function imprimir($id)
    {
        $venta = $this->ventaModel->select('venta.*, cliente.nombre AS cliente_nombre, cliente.identificacion AS cliente_identificacion, cliente.direccion, cliente.telefono, cliente.correo, usuario.nombre AS usuario_nombre')
                                  ->join('cliente', 'cliente.id_cliente = venta.id_cliente')
                                  ->join('usuario', 'usuario.id_usuario = venta.id_usuario')
                                  ->where('venta.id_venta', $id)
                                  ->first();

        if (!$venta) {
            return redirect()->to(base_url('facturacion'))->with('error', 'Factura no encontrada.');
        }

        $detalles = $this->detalleVentaModel->getDetallesPorVenta($id);

        $data = [
            'venta'    => $venta,
            'detalles' => $detalles
        ];

        return view('facturacion/factura_pdf', $data);
    }
}