<?php

namespace App\Controllers;

use App\Models\CompraModel;
use App\Models\DetalleCompraModel;
use App\Models\ProveedorModel;
use App\Models\ProductoModel;

class CompraController extends BaseController
{
    protected $compraModel;
    protected $detalleCompraModel;
    protected $proveedorModel;
    protected $productoModel;

    public function __construct()
    {
        $this->compraModel        = new CompraModel();
        $this->detalleCompraModel = new DetalleCompraModel();
        $this->proveedorModel     = new ProveedorModel();
        $this->productoModel      = new ProductoModel();
    }

    public function index()
    {
        return view('compras/index');
    }

    public function getCompras()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $compras = $this->compraModel->getComprasConDetalles();
        return $this->response->setJSON(['data' => $compras]);
    }

    public function buscarProveedores()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $term = trim((string) $this->request->getGet('q'));
        if (empty($term)) {
            return $this->response->setJSON([]);
        }

        $proveedores = $this->proveedorModel->like('identificacion', $term)
                                             ->orLike('nombre', $term)
                                             ->findAll(10);

        return $this->response->setJSON($proveedores);
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

        // A diferencia de facturación, aquí no se filtra por stock disponible:
        // el objetivo de una compra es precisamente incrementarlo.
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

        $idProveedor = $this->request->getPost('id_proveedor');
        $productos   = $this->request->getPost('productos'); // id_producto, cantidad, costo_unitario
        $idUsuario   = session()->get('id_usuario') ?? 1;

        if (empty($idProveedor)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Debe seleccionar un proveedor.']);
        }

        if (empty($productos) || !is_array($productos)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Debe agregar al menos un producto a la compra.']);
        }

        // Validación de cada línea antes de iniciar la transacción
        $totalCompra = 0;
        $detallesProcesados = [];

        foreach ($productos as $item) {
            $prod = $this->productoModel->find($item['id_producto']);
            if (!$prod) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Uno de los productos seleccionados no existe.']);
            }

            $cant = (int) $item['cantidad'];
            if ($cant <= 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'La cantidad para "' . $prod['nombre'] . '" debe ser mayor a cero.']);
            }

            $costoUnitario = (float) $item['costo_unitario'];
            if ($costoUnitario < 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'El costo unitario para "' . $prod['nombre'] . '" no puede ser negativo.']);
            }

            $subtotal = $costoUnitario * $cant;
            $totalCompra += $subtotal;

            $detallesProcesados[] = [
                'id_producto'    => $prod['id_producto'],
                'cantidad'       => $cant,
                'costo_unitario' => $costoUnitario,
                'subtotal'       => $subtotal,
                'stock_actual'   => $prod['stock'],
            ];
        }

        // Transacción: cabecera + detalle + incremento de stock
        $db = \Config\Database::connect();
        $db->transStart();

        $idCompra = $this->compraModel->insert([
            'id_proveedor' => $idProveedor,
            'id_usuario'   => $idUsuario,
            'total'        => $totalCompra,
        ]);

        foreach ($detallesProcesados as $det) {
            $this->detalleCompraModel->insert([
                'id_compra'      => $idCompra,
                'id_producto'    => $det['id_producto'],
                'cantidad'       => $det['cantidad'],
                'costo_unitario' => $det['costo_unitario'],
                'subtotal'       => $det['subtotal'],
            ]);

            // Aumentar stock (a diferencia de la venta, que lo descuenta)
            $nuevoStock = $det['stock_actual'] + $det['cantidad'];
            $this->productoModel->update($det['id_producto'], ['stock' => $nuevoStock]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al procesar la compra.']);
        }

        return $this->response->setJSON([
            'status'    => 'success',
            'message'   => 'Compra registrada con éxito. El inventario fue actualizado.',
            'id_compra' => $idCompra,
        ]);
    }

    public function obtener($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $compra = $this->compraModel->select('compra.*, proveedor.nombre AS proveedor_nombre, proveedor.identificacion AS proveedor_identificacion, usuario.nombre AS usuario_nombre')
                                     ->join('proveedor', 'proveedor.id_proveedor = compra.id_proveedor')
                                     ->join('usuario', 'usuario.id_usuario = compra.id_usuario')
                                     ->where('compra.id_compra', $id)
                                     ->first();

        if (!$compra) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Compra no encontrada.']);
        }

        $detalles = $this->detalleCompraModel->getDetallesPorCompra($id);

        return $this->response->setJSON([
            'status'   => 'success',
            'compra'   => $compra,
            'detalles' => $detalles,
        ]);
    }
}
