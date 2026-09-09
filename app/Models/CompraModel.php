<?php

namespace App\Models;

use CodeIgniter\Model;

class CompraModel extends Model
{
    protected $table            = 'compra';
    protected $primaryKey       = 'id_compra';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_proveedor', 'id_usuario', 'total', 'fecha'];

    /**
     * Listado de compras con el nombre del proveedor y del usuario que la registró.
     */
    public function getComprasConDetalles()
    {
        return $this->select('compra.*, proveedor.nombre AS proveedor_nombre, proveedor.identificacion AS proveedor_identificacion, usuario.nombre AS usuario_nombre')
                    ->join('proveedor', 'proveedor.id_proveedor = compra.id_proveedor')
                    ->join('usuario', 'usuario.id_usuario = compra.id_usuario')
                    ->orderBy('compra.id_compra', 'DESC')
                    ->findAll();
    }
}
