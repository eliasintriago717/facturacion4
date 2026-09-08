<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Carga la vista principal/dashboard directamente
        return view('facturacion/index');
    }
}