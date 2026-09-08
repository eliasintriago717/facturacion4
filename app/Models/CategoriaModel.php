<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table            = 'categoria';
    protected $primaryKey       = 'id_categoria';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nombre'];

    // Reglas centralizadas en el Modelo
    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[50]|is_unique[categoria.nombre,id_categoria,{id_categoria}]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El nombre de la categoría es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede exceder los 50 caracteres.',
            'is_unique'  => 'Esta categoría ya existe.',
        ],
    ];
}