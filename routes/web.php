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

Route::get('/','App\Http\Controllers\PollaController@home')->name('home');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/apuestas', function () {
        return view('panel.apuestas');
    });
    Route::get('/resultados', function () {
        return view('panel.resultados');
    });
    Route::get('/tabla', function () {
        return view('panel.tabla');
    });
     Route::get('/salir', function () {
        Auth::logout();
        return redirect(route('home'));
    });
});

Route::get('/token','App\Http\Controllers\PollaController@token');

Route::post('/crear_usuario','App\Http\Controllers\PollaController@crear_usuario');
Route::post('/crear_usuario_polla/{codigo}','App\Http\Controllers\PollaController@crear_usuario_polla');

Route::post('/pronostico','App\Http\Controllers\PollaController@pronostico');

Route::post('/login','App\Http\Controllers\PollaController@login');

Route::get('/getpartidos','App\Http\Controllers\PollaController@get_partidos');
Route::get('/logout','App\Http\Controllers\PollaController@logout');
Route::get('/gettabla','App\Http\Controllers\PollaController@get_tabla');

Route::get('/actualizar/partidos','App\Http\Controllers\PollaController@actualizar_estado_partidos');
Route::get('/actualizar/pronosticos','App\Http\Controllers\PollaController@actualizar_pronosticos');
Route::get('/actualizar/tabla','App\Http\Controllers\PollaController@actualizar_tabla');


//BORRAR
Route::get('/a', function () {
        return view('panel.template');
    });