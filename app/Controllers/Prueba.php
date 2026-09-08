<?php

namespace App\Controllers;

class Prueba extends BaseController
{
    public function index(): string    
    {
        //echo "HOLA";

        $datos["nombre"] = "ABC JOSE RODRIGUEZ";
        $datos["direccion"] = "ABC IBARRA";

        return view('prueba/index', $datos);   
    }

}
