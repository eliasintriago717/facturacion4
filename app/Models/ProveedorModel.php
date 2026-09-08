<?php
namespace App\Models;

use CodeIgniter\Model;

class ProveedorModel extends Model
{
    protected $table            = 'proveedor';
    protected $primaryKey       = 'id_proveedor';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['identificacion', 'nombre', 'telefono'];

    // Reglas centralizadas en el Modelo
    protected $validationRules = [
        'identificacion' => 'required|min_length[10]|max_length[20]|is_unique[proveedor.identificacion,id_proveedor,{id_proveedor}]',
        'nombre'         => 'required|min_length[3]|max_length[100]',
        'telefono'       => 'permit_empty|min_length[7]|max_length[20]|numeric',
    ];

    protected $validationMessages = [
        'identificacion' => [
            'required'   => 'La identificación RUC/Cédula es obligatoria.',
            'min_length' => 'La identificación debe tener al menos 10 caracteres.',
            'max_length' => 'La identificación no puede exceder los 20 caracteres.',
            'is_unique'  => 'Esta identificación ya se encuentra registrada.',
        ],
        'nombre' => [
            'required'   => 'El nombre o razón social del proveedor es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede exceder los 100 caracteres.',
        ],
        'telefono' => [
            'min_length' => 'El teléfono debe tener al menos 7 dígitos.',
            'max_length' => 'El teléfono no puede exceder los 20 dígitos.',
            'numeric'    => 'El teléfono solo debe contener números.',
        ],
    ];
}