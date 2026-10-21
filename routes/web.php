<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;

Route::get('/', function () {
    return view('welcome');
});

// --- MÓDULO DE CATEGORÍAS ---
Route::get('/categorias', [CategoriaController::class, 'getCategorias']);
Route::post('/categorias', [CategoriaController::class, 'createCategoria'])->name('categorias.store');
Route::get('/categorias/{id}/edit', [CategoriaController::class, 'editCategoria'])->name('categorias.edit');
Route::put('/categorias/{id}', [CategoriaController::class, 'updateCategoria'])->name('categorias.update');
Route::delete('/categorias/{id}', [CategoriaController::class, 'deleteCategoria'])->name('categorias.delete');

// --- MÓDULO DE PRODUCTOS ---
Route::get('/productos', [ProductoController::class, 'getProductos']);
Route::post('/productos', [ProductoController::class, 'createProducto'])->name('productos.store');
Route::get('/productos/{id}/edit', [ProductoController::class, 'editProducto'])->name('productos.edit');
Route::put('/productos/{id}', [ProductoController::class, 'updateProducto'])->name('productos.update');
Route::delete('/productos/{id}', [ProductoController::class, 'deleteProducto'])->name('productos.delete');

// --- MÓDULO DE CLIENTES ---
Route::get('/clientes', [ClienteController::class, 'getClientes']);
Route::post('/clientes', [ClienteController::class, 'createCliente'])->name('clientes.store');
Route::get('/clientes/{id}/edit', [ClienteController::class, 'editCliente'])->name('clientes.edit');
Route::put('/clientes/{id}', [ClienteController::class, 'updateCliente'])->name('clientes.update');
Route::delete('/clientes/{id}', [ClienteController::class, 'deleteCliente'])->name('clientes.delete');

// --- MÓDULO DE VENTAS ---
Route::get('/ventas', [VentaController::class, 'getVentas']);
Route::post('/ventas', [VentaController::class, 'createVenta'])->name('ventas.store');
Route::get('/ventas/{id}/edit', [VentaController::class, 'editVenta'])->name('ventas.edit');
Route::put('/ventas/{id}', [VentaController::class, 'updateVenta'])->name('ventas.update');
Route::delete('/ventas/{id}', [VentaController::class, 'deleteVenta'])->name('ventas.delete');