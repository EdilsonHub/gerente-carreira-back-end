<?php

use App\Http\Controllers\ProjetoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Response(["erro" => "vazio"], 204)->json();
});

Route::get('/frifairei', function () {
    return ["nome" => "Ana Maria jogadora de frifairi"];
});




Route::resource('/', ProjetoController::class);
