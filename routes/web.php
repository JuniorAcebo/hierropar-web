<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\TrasladoController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\GrupoClientesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TipoUnidadController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/',[homeController::class,'index'])->name('panel');

    Route::get('productos/historial-ajustes', [ProductoController::class, 'historialAjustes'])
    ->name('productos.historialAjustes');

    Route::get('productos/crear-ajuste', [ProductoController::class, 'createAjuste'])
    ->name('productos.createAjuste');

    Route::post('productos/store-ajuste', [ProductoController::class, 'storeAjuste'])
    ->name('productos.storeAjuste');

    Route::get('/ventas/check-stock', [VentaController::class, 'checkStock'])->name('ventas.check-stock');

Route::resources([
    'categorias' => CategoriaController::class,
    'marcas' => MarcaController::class,
    'productos' => ProductoController::class,
    'clientes' => ClienteController::class,
    'proveedores' => ProveedorController::class,
    'compras' => CompraController::class,
    'ventas' => VentaController::class,
    'users' => UserController::class,
    'roles' => RoleController::class,
    'profile' => ProfileController::class,
    'traslados' => TrasladoController::class,
    'almacenes' => AlmacenController::class,
    'grupoClientes' => GrupoClientesController::class,], [
    'parameters' => ['almacenes' => 'almacen']
]);


    Route::resource('tipounidades', TipoUnidadController::class)->parameters(['tipounidades' => 'tipounidad']);

    Route::patch('/almacenes/{almacen}/estado', [AlmacenController::class, 'updateEstado'])
    ->name('almacenes.updateEstado');

    Route::patch('productos/{producto}/estado', [ProductoController::class, 'updateEstado'])
    ->name('productos.updateEstado');

    Route::get('productos/{producto}/ajuste-cantidad', [ProductoController::class, 'ajusteCantidad'])
    ->name('productos.ajusteCantidad');

    Route::post('productos/{producto}/ajuste-cantidad', [ProductoController::class, 'updateCantidad'])
    ->name('productos.updateCantidad');

<<<<<<< HEAD
    // Rutas API para Traslados
    Route::get('traslados/api/productos', [TrasladoController::class, 'getProductos'])
    ->name('traslados.api.productos');

    Route::get('traslados/api/stock', [TrasladoController::class, 'getStockProducto'])
    ->name('traslados.api.stock');

    Route::get('traslados/api/almacenes', [TrasladoController::class, 'getAlmacenes'])
    ->name('traslados.api.almacenes');
=======
    Route::get('/productos/check-stock', [ProductoController::class, 'checkStock'])
    ->name('productos.checkStock');
>>>>>>> origin/AronDev



Route::get('/compras/pdf/{id}', [compraController::class, 'generarPdf'])->name('compras.pdf');
Route::get('/ventas/pdf/{id}', [ventaController::class, 'generarPdf'])->name('ventas.pdf');
Route::get('/login',[loginController::class,'index'])->name('login');
Route::post('/login',[loginController::class,'login']);
Route::get('/logout',[logoutController::class,'logout'])->name('logout');

Route::get('/401', function () {
    return view('pages.401');
});
Route::get('/404', function () {
    return view('pages.404');
});
Route::get('/500', function () {
    return view('pages.500');
});