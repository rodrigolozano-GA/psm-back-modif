<?php

use Illuminate\Http\Request;

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

//Llamar ruta uribase:(puerto)?/api/endpoint
Route::get('/foo', function () {
    return array('esto es un prueba ','de laravel');
});