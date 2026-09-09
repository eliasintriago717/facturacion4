<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Manejo directo del favicon para evitar interceptarlo con auth
$routes->get('favicon.ico', static function() {
    return response()->setStatusCode(204);
});

// ==========================================
// 1. RUTAS PÚBLICAS
// ==========================================
$routes->get('login', 'AuthController::index');
$routes->post('login/authenticate', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');

// ==========================================
// 2. RUTAS PARA AMBOS ROLES (Administrador y Encargado)
// ==========================================
$routes->group('', ['filter' => ['auth', 'role:administrador,encargado']], static function ($routes) {
    $routes->get('/', 'Home::index');
    $routes->get('dashboard', 'Home::index');
    $routes->get('facturas', 'FacturacionController::index');
    $routes->get('facturas/nueva', 'FacturacionController::index');
    $routes->get('facturacion/imprimir/(:num)', 'FacturacionController::imprimir/$1');
});

// Endpoints AJAX - Dashboard
$routes->group('dashboard', ['filter' => ['auth', 'ajax', 'role:administrador,encargado']], static function ($routes) {
    $routes->get('data', 'Home::getDashboardData');
});

// Endpoints AJAX - Facturación
$routes->group('facturas', ['filter' => ['auth', 'ajax', 'role:administrador,encargado']], static function ($routes) {
    $routes->get('getVentas', 'FacturacionController::getVentas');
    $routes->get('buscarClientes', 'FacturacionController::buscarClientes');
    $routes->get('buscarProductos', 'FacturacionController::buscarProductos');
    $routes->post('guardar', 'FacturacionController::guardar');
    $routes->get('obtener/(:num)', 'FacturacionController::obtener/$1');
});

// ==========================================
// 3. RUTAS EXCLUSIVAS PARA ADMINISTRADOR
// ==========================================
$routes->group('admin', ['filter' => ['auth', 'role:administrador']], static function ($routes) {
    $routes->get('categorias', 'CategoriasController::index');
    $routes->get('marcas', 'MarcasController::index');
    $routes->get('clientes', 'ClientesController::index');
    $routes->get('proveedores', 'ProveedoresController::index');
    $routes->get('compras', 'CompraController::index');
    $routes->get('usuarios', 'UsuariosController::index');
    $routes->get('productos', 'ProductosController::index');
});

// Endpoints AJAX - Gestión Admin
$routes->group('categorias', ['filter' => ['auth', 'ajax', 'role:administrador']], static function ($routes) {
    $routes->get('getCategorias', 'CategoriasController::getCategorias');
    $routes->post('guardar', 'CategoriasController::guardar');
    $routes->get('obtener/(:num)', 'CategoriasController::obtener/$1');
    $routes->delete('eliminar/(:num)', 'CategoriasController::eliminar/$1');
});

$routes->group('marcas', ['filter' => ['auth', 'ajax', 'role:administrador']], static function ($routes) {
    $routes->get('getMarcas', 'MarcasController::getMarcas');
    $routes->post('guardar', 'MarcasController::guardar');
    $routes->get('obtener/(:num)', 'MarcasController::obtener/$1');
    $routes->delete('eliminar/(:num)', 'MarcasController::eliminar/$1');
});

$routes->group('clientes', ['filter' => ['auth', 'ajax', 'role:administrador']], static function ($routes) {
    $routes->get('getClientes', 'ClientesController::getClientes');
    $routes->post('guardar', 'ClientesController::guardar');
    $routes->get('obtener/(:num)', 'ClientesController::obtener/$1');
    $routes->delete('eliminar/(:num)', 'ClientesController::eliminar/$1');
});

$routes->group('proveedores', ['filter' => ['auth', 'ajax', 'role:administrador']], static function ($routes) {
    $routes->get('getProveedores', 'ProveedoresController::getProveedores');
    $routes->post('guardar', 'ProveedoresController::guardar');
    $routes->get('obtener/(:num)', 'ProveedoresController::obtener/$1');
    $routes->delete('eliminar/(:num)', 'ProveedoresController::eliminar/$1');
});

$routes->group('compras', ['filter' => ['auth', 'ajax', 'role:administrador']], static function ($routes) {
    $routes->get('getCompras', 'CompraController::getCompras');
    $routes->get('buscarProveedores', 'CompraController::buscarProveedores');
    $routes->get('buscarProductos', 'CompraController::buscarProductos');
    $routes->post('guardar', 'CompraController::guardar');
    $routes->get('obtener/(:num)', 'CompraController::obtener/$1');
});

$routes->group('usuarios', ['filter' => ['auth', 'ajax', 'role:administrador']], static function ($routes) {
    $routes->get('getUsuarios', 'UsuariosController::getUsuarios');
    $routes->post('guardar', 'UsuariosController::guardar');
    $routes->get('obtener/(:num)', 'UsuariosController::obtener/$1');
    $routes->delete('eliminar/(:num)', 'UsuariosController::eliminar/$1');
});

$routes->group('productos', ['filter' => ['auth', 'ajax', 'role:administrador']], static function ($routes) {
    $routes->get('getProductos', 'ProductosController::getProductos');
    $routes->post('guardar', 'ProductosController::guardar');
    $routes->get('obtener/(:num)', 'ProductosController::obtener/$1');
    $routes->delete('eliminar/(:num)', 'ProductosController::eliminar/$1');
});