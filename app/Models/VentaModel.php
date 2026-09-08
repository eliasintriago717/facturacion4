<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaModel extends Model
{
    protected $table            = 'venta';
    protected $primaryKey       = 'id_venta';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_cliente', 'id_usuario', 'total', 'fecha'];

    // Método para obtener el listado de ventas con nombres de cliente y usuario
    public function getVentasConDetalles()
    {
        return $this->select('venta.*, cliente.nombre AS cliente_nombre, cliente.identificacion AS cliente_identificacion, usuario.nombre AS usuario_nombre')
                    ->join('cliente', 'cliente.id_cliente = venta.id_cliente')
                    ->join('usuario', 'usuario.id_usuario = venta.id_usuario')
                    ->orderBy('venta.id_venta', 'DESC')
                    ->findAll();
    }
}