<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleVentaModel extends Model
{
    protected $table            = 'detalle_venta';
    protected $primaryKey       = 'id_detalle_venta';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_venta', 'id_producto', 'cantidad', 'precio_unitario', 'subtotal'];

    public function getDetallesPorVenta($idVenta)
    {
        return $this->select('detalle_venta.*, producto.nombre AS producto_nombre, producto.codigo_barras')
                    ->join('producto', 'producto.id_producto = detalle_venta.id_producto')
                    ->where('detalle_venta.id_venta', $idVenta)
                    ->findAll();
    }
}