<?php

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
    return view('welcome');
});

Route::get('/grupos', function () {
    return view('panel.grupos');
});

Route::get('/token','App\Http\Controllers\PollaController@token');

Route::post('/crear_usuario','App\Http\Controllers\PollaController@crear_usuario');
Route::post('/crear_usuario_polla/{codigo}','App\Http\Controllers\PollaController@crear_usuario_polla');

Route::post('/login','App\Http\Controllers\PollaController@login');
