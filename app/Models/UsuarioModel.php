<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuario';
    protected $primaryKey       = 'id_usuario';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nombre', 'correo', 'clave', 'rol', 'estado'];

    // Encriptación de contraseña mediante Callback antes de insertar/actualizar
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    // Reglas centralizadas de validación
    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[100]',
        'correo' => 'required|valid_email|max_length[100]|is_unique[usuario.correo,id_usuario,{id_usuario}]',
        'clave'  => 'permit_empty|min_length[6]',
        'rol'    => 'required|in_list[administrador,encargado]',
        'estado' => 'permit_empty|in_list[0,1,true,false]'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El nombre completo es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede exceder los 100 caracteres.'
        ],
        'correo' => [
            'required'    => 'El correo electrónico es obligatorio.',
            'valid_email' => 'Ingrese una dirección de correo válida.',
            'max_length'  => 'El correo no puede exceder los 100 caracteres.',
            'is_unique'   => 'Este correo electrónico ya se encuentra registrado.'
        ],
        'clave' => [
            'min_length' => 'La contraseña debe tener al menos 6 caracteres.'
        ],
        'rol' => [
            'required' => 'Debe seleccionar un rol válido.',
            'in_list'  => 'El rol seleccionado no es válido.'
        ]
    ];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['clave']) && !empty($data['data']['clave'])) {
            $data['data']['clave'] = password_hash($data['data']['clave'], PASSWORD_BCRYPT);
        } else {
            // Si en edición la clave viene vacía, no se sobreescribe la existente
            unset($data['data']['clave']);
        }
        return $data;
    }
}