<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('tables', \App\Http\Controllers\Api\TableController::class, [
    'parameters' => [
        'tables' => 'model'
    ]
])
->except(['create', 'edit']);
Route::get('open-tables', [\App\Http\Controllers\Api\TableController::class, 'getOpenTables'])->name('tables.open-tables');

Route::resource('menus', \App\Http\Controllers\Api\MenuController::class, [
    'parameters' => [
        'menus' => 'model'
    ]
])->except(['create', 'edit']);

Route::resource('orders', \App\Http\Controllers\Api\OrderController::class, [
    'parameters' => [
        'orders' => 'model'
    ]
])->except('create', 'edit');
Route::post('orders/payment', [\App\Http\Controllers\Api\OrderController::class, 'payment'])->name('orders.payment');

Route::resource('reservations', \App\Http\Controllers\Api\ReservationController::class, [
    'parameters' => [
        'reservations' => 'model'
    ]
])->except('create', 'edit');
