<?php

namespace App\Controllers;

use App\Models\VentaModel;
use App\Models\DetalleVentaModel;
use App\Models\ClienteModel;
use App\Models\ProductoModel;
use Dompdf\Dompdf;
use Dompdf\Options;

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
        $productos = $this->request->getPost('productos'); 
        $idUsuario = session()->get('id_usuario') ?? 1; 

        if (empty($idCliente)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Debe seleccionar un cliente.']);
        }

        if (empty($productos) || !is_array($productos)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Debe agregar al menos un producto a la factura.']);
        }

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

        $db = \Config\Database::connect();
        $db->transStart();

        $idVenta = $this->ventaModel->insert([
            'id_cliente' => $idCliente,
            'id_usuario' => $idUsuario,
            'total'      => $totalVenta
        ]);

        foreach ($detallesProcesados as $det) {
            $this->detalleVentaModel->insert([
                'id_venta'        => $idVenta,
                'id_producto'     => $det['id_producto'],
                'cantidad'        => $det['cantidad'],
                'precio_unitario' => $det['precio_unitario'],
                'subtotal'        => $det['subtotal']
            ]);

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

    // ==========================================
    // MÉTODO PARA GENERAR E IMPRIMIR EL PDF
    // ==========================================
    public function descargarPdf($idVenta)
    {
        // 1. Obtener la venta con información del cliente y usuario
        $venta = $this->ventaModel->select('venta.*, cliente.nombre AS cliente_nombre, cliente.identificacion AS cliente_identificacion, usuario.nombre AS usuario_nombre')
                                  ->join('cliente', 'cliente.id_cliente = venta.id_cliente')
                                  ->join('usuario', 'usuario.id_usuario = venta.id_usuario')
                                  ->where('venta.id_venta', $idVenta)
                                  ->first();

        if (!$venta) {
            return redirect()->back()->with('error', 'Factura no encontrada');
        }

        // 2. Obtener los detalles de la venta
        $detalles = $this->detalleVentaModel->getDetallesPorVenta($idVenta);

        // 3. Configurar Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        // 4. Pasar los datos a la plantilla HTML
        $data = [
            'venta'    => $venta,
            'detalles' => $detalles
        ];
        
        $html = view('facturas/pdf_template', $data);

        // 5. Generar y emitir el PDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("Factura_" . $idVenta . ".pdf", ["Attachment" => false]);
        exit();
    }
}