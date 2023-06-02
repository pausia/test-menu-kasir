<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PointOfSaleController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/point-of-sale', [PointOfSaleController::class, 'index'])->name('point-of-sale.point-of-sale');
Route::post('/add-to-total', [PointOfSaleController::class, 'addToTotal'])->name('add-to-total');
Route::post('/remove-from-total', [PointOfSaleController::class, 'removeFromTotal'])->name('remove-from-total');

