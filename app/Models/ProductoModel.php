<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoModel extends Model
{
    protected $table            = 'producto';
    protected $primaryKey       = 'id_producto';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'codigo_barras',
        'nombre',
        'id_categoria',
        'id_marca',
        'precio_venta',
        'stock'
    ];

    // Reglas centralizadas en el Modelo
    protected $validationRules = [
        'codigo_barras' => 'permit_empty|max_length[50]|is_unique[producto.codigo_barras,id_producto,{id_producto}]',
        'nombre'        => 'required|min_length[3]|max_length[100]',
        'id_categoria'  => 'required|is_natural_no_zero|is_not_unique[categoria.id_categoria]',
        'id_marca'      => 'required|is_natural_no_zero|is_not_unique[marca.id_marca]',
        'precio_venta'  => 'required|decimal|greater_than_equal_to[0]',
        'stock'         => 'required|integer|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'codigo_barras' => [
            'max_length' => 'El código de barras no puede exceder los 50 caracteres.',
            'is_unique'  => 'Este código de barras ya se encuentra registrado.',
        ],
        'nombre' => [
            'required'   => 'El nombre del producto es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede exceder los 100 caracteres.',
        ],
        'id_categoria' => [
            'required'           => 'Debe seleccionar una categoría.',
            'is_natural_no_zero' => 'La categoría seleccionada no es válida.',
            'is_not_unique'      => 'La categoría seleccionada no existe en el sistema.',
        ],
        'id_marca' => [
            'required'           => 'Debe seleccionar una marca.',
            'is_natural_no_zero' => 'La marca seleccionada no es válida.',
            'is_not_unique'      => 'La marca seleccionada no existe en el sistema.',
        ],
        'precio_venta' => [
            'required'              => 'El precio de venta es obligatorio.',
            'decimal'               => 'El precio de venta debe ser un número decimal válido.',
            'greater_than_equal_to' => 'El precio de venta no puede ser menor a 0.',
        ],
        'stock' => [
            'required'              => 'El stock inicial es obligatorio.',
            'integer'               => 'El stock debe ser un número entero.',
            'greater_than_equal_to' => 'El stock no puede ser menor a 0.',
        ],
    ];

    /**
     * Obtiene los productos con sus nombres de categoría y marca asociados.
     */
    public function getProductosConRelaciones()
    {
        return $this->select('producto.*, categoria.nombre AS categoria_nombre, marca.nombre AS marca_nombre')
                    ->join('categoria', 'categoria.id_categoria = producto.id_categoria')
                    ->join('marca', 'marca.id_marca = producto.id_marca')
                    ->findAll();
    }
}