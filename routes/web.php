<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/api/items', [ItemController::class, 'getItemsJson']);


Route::get('/api/items/{id}/details', [ItemController::class, 'getItemDetails']);

Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::post('/items', [ItemController::class, 'store'])->name('items.store');
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');


Route::get('/test-react', function () {
    return view('test-react');
})->name('test-react');