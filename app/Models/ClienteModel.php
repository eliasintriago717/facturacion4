<?php
namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table            = 'cliente';
    protected $primaryKey       = 'id_cliente';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['identificacion', 'nombre', 'telefono', 'correo'];

    // Reglas centralizadas en el Modelo
    protected $validationRules = [
        'identificacion' => 'required|validar_cedula_ec|is_unique[cliente.identificacion,id_cliente,{id_cliente}]',
        'nombre'         => 'required|min_length[3]|max_length[100]',
        'telefono'       => 'permit_empty|min_length[7]|max_length[20]|numeric',
        'correo'         => 'permit_empty|valid_email|max_length[100]',
    ];

    protected $validationMessages = [
        'identificacion' => [
            'required'          => 'La cédula de identidad es obligatoria.',
            'validar_cedula_ec' => 'El número de cédula ingresado no es válido.',
            'is_unique'         => 'Esta cédula ya se encuentra registrada.',
        ],
        'nombre' => [
            'required'   => 'El nombre del cliente es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede exceder los 100 caracteres.',
        ],
        'telefono' => [
            'min_length' => 'El teléfono debe tener al menos 7 dígitos.',
            'max_length' => 'El teléfono no puede exceder los 20 dígitos.',
            'numeric'    => 'El teléfono solo debe contener números.',
        ],
        'correo' => [
            'valid_email' => 'Ingrese una dirección de correo electrónico válida.',
            'max_length'  => 'El correo no puede exceder los 100 caracteres.',
        ],
    ];
}