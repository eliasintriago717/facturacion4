<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleCompraModel extends Model
{
    protected $table            = 'detalle_compra';
    protected $primaryKey       = 'id_detalle_compra';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_compra', 'id_producto', 'cantidad', 'costo_unitario', 'subtotal'];

    /**
     * Detalle de productos de una compra puntual, con datos del producto asociado.
     */
    public function getDetallesPorCompra($idCompra)
    {
        return $this->select('detalle_compra.*, producto.nombre AS producto_nombre, producto.codigo_barras')
                    ->join('producto', 'producto.id_producto = detalle_compra.id_producto')
                    ->where('detalle_compra.id_compra', $idCompra)
                    ->findAll();
    }
}
