<?php

namespace App\Http\Controllers\Catalogos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CotizacionesController extends Controller
{
	/**
	 * Función para retornar la respuesta para Errores, Validaciones en los controllers y Respuestas de la Base de datos
	 *
	 * Parámetros:
	 * 		$respuestaSP - Respuesta de la base de datos
	 * 		$codeHTTP 	 - Tipo de error
	 * 		$opc 		 - Acción a realizar
	 * 		$exito 		 - SUCCESS
	 *		$mensaje	 - MESSAGE: puede ser mensaje de error, mensaje de confirmación, etc.
	 *  
	 * @return \Illuminate\Http\Response
	 */
	public function formatearRespuesta($respuestaSP, $codeHTTP = 200, $opc = 0, $exito = 0, $mensaje = '')
	{
		// $info -> Variable para retornar de mensaje
		$info = Array();
		// Opciones: 
		// 0 - Respuesta de un SP
		// 1 - Retorna una lista
		// 2 - Retorna mensaje personalizado
		switch($opc) {
			case 0: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE]; break; 
			case 1: $info = ["SUCCESS" => 1, "MESSAGE" => '', "DATA" => $respuestaSP];break; 
			case 2: $info = ["SUCCESS" => $exito, "MESSAGE" => $mensaje]; break; 
		}
		return response()->json($info, 
		$codeHTTP, 
		['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], 
		JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * Retorna la lista de registros
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		abort(404);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		abort(404);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		abort(500);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		abort(404);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		abort(404);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		abort(500);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		abort(500);
	}
}
