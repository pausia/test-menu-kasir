<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PointOfSaleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/point-of-sale', [PointOfSaleController::class, 'index'])->name('point-of-sale.point-of-sale');
Route::post('/add-to-total', [PointOfSaleController::class, 'addToTotal'])->name('add-to-total');
