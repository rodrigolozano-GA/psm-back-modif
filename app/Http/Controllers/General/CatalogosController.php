<?php

namespace App\Http\Controllers\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CatalogosController extends Controller
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
			case 3: $info = ["SUCCESS" => 1, "MESSAGE" => "", "DATA" => $respuestaSP]; break; 
		}
		return response()->json($info, 
		$codeHTTP, 
		['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], 
		JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * Retorna una lista de registros
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function clientes(Request $request)
	{
		try {
			$lista = DB::select('CALL CAT_obtInfoDBGenerales(?, ?)', [$request->has("opc") ? $request->input("opc") : 20, $request->has("buscar") ? $request->input("buscar") : ""]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}   
	}

	/**
	 * Retorna una lista de registros
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function sucursales(Request $request)
	{
		try {
			$lista = DB::select('CALL CAT_obtSucursalesXCliente(?)', [$request->has("opc") ? $request->input("opc") : 0]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}   
	}
	 /*
	public function sucursales(Request $request)
	{
		try {
			$lista = DB::select('CALL CAT_obtInfoDBGenerales(?, ?)', [$request->has("opc") ? $request->input("opc") : 20, $request->has("buscar") ? $request->input("buscar") : ""]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}   
	}
	*/

	/**
	 * Retorna una lista de registros
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function estados(Request $request)
	{
		try {
			$lista = DB::select('CALL CAT_obtInfoDBGenerales(?, ?)', [$request->has("opc") ? $request->input("opc") : 20, $request->has("buscar") ? $request->input("buscar") : ""]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}   
	}

	/**
	 * Retorna una lista de registros
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function ciudades(Request $request)
	{
		try {
			$lista = DB::select('CALL CAT_obtInfoDBGenerales(?, ?)', [$request->has("opc") ? $request->input("opc") : 20, $request->has("buscar") ? $request->input("buscar") : ""]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}   
	}

	/**
	 * Retorna una lista de registros
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function servicios(Request $request)
	{
		try {
			//$lista = DB::select('CALL CAT_obtInfoDBGenerales(?, ?)', [$request->has("opc") ? $request->input("opc") : 20, $request->has("buscar") ? $request->input("buscar") : ""]);
			$lista = array();
			$lista = array(
				array('id' => 1, 'clave' => 'S0001 Servicio 1'),
				array('id' => 2, 'clave' => 'S0002 Servicio 2'),
				array('id' => 3, 'clave' => 'S0003 Servicio 3'),
				array('id' => 4, 'clave' => 'S0004 Servicio 4'),
				array('id' => 5, 'clave' => 'S0005 Servicio 5')
			);
			return $this->formatearRespuesta($lista, 200, 3);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}   
	}

	/**
	 * Retorna una lista de registros
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function refacciones(Request $request)
	{
		try {
			//$lista = DB::select('CALL CAT_obtInfoDBGenerales(?, ?)', [$request->has("opc") ? $request->input("opc") : 20, $request->has("buscar") ? $request->input("buscar") : ""]);
			$lista = array();
			$lista = array(
				array('id' => 1, 'clave' => 'R0001 Refacción 1'),
				array('id' => 2, 'clave' => 'R0002 Refacción 2'),
				array('id' => 3, 'clave' => 'R0003 Refacción 3'),
				array('id' => 4, 'clave' => 'R0004 Refacción 4'),
				array('id' => 5, 'clave' => 'R0005 Refacción 5')
			);
			return $this->formatearRespuesta($lista, 200, 3);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}   
	}

	/**
	 * Retorna una lista de Servicios y Refacciones
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function serviciosRef(Request $request)
	{
		try {
			//$lista = DB::select('CALL CAT_obtInfoDBGenerales(?, ?)', [$request->has("opc") ? $request->input("opc") : 20, $request->has("buscar") ? $request->input("buscar") : ""]);
			$lista = array();
			$lista = array(
				array('id' => 1, 'nombre' => 'S0001 Servicio 1'),
				array('id' => 2, 'nombre' => 'S0002 Servicio 2'),
				array('id' => 3, 'nombre' => 'S0003 Servicio 3'),
				array('id' => 4, 'nombre' => 'S0004 Servicio 4'),
				array('id' => 5, 'nombre' => 'S0005 Servicio 5'),
				array('id' => 6, 'nombre' => 'R0001 Refacción 1'),
				array('id' => 7, 'nombre' => 'R0002 Refacción 2'),
				array('id' => 8, 'nombre' => 'R0003 Refacción 3'),
				array('id' => 9, 'nombre' => 'R0004 Refacción 4'),
				array('id' => 10, 'nombre' => 'R0005 Refacción 5')
			);
			return $this->formatearRespuesta($lista, 200, 3);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}   
	}

	/**
	 * Buscar el Servicio o Refaccion por id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function obtServiciosRef(Request $request)
	{
		try {
			//$lista = DB::select('CALL CAT_obtInfoDBGenerales(?, ?)', [$request->has("opc") ? $request->input("opc") : 20, $request->has("buscar") ? $request->input("buscar") : ""]);
			$lista = array();
			$lista = array(
				array('id' => 1, 'nombre' => 'S0001 Servicio 1'),
				array('id' => 2, 'nombre' => 'S0002 Servicio 2'),
				array('id' => 3, 'nombre' => 'S0003 Servicio 3'),
				array('id' => 4, 'nombre' => 'S0004 Servicio 4'),
				array('id' => 5, 'nombre' => 'S0005 Servicio 5'),
				array('id' => 6, 'nombre' => 'R0001 Refacción 1'),
				array('id' => 7, 'nombre' => 'R0002 Refacción 2'),
				array('id' => 8, 'nombre' => 'R0003 Refacción 3'),
				array('id' => 9, 'nombre' => 'R0004 Refacción 4'),
				array('id' => 10, 'nombre' => 'R0005 Refacción 5')
			);
			return $this->formatearRespuesta($lista, 200, 3);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}   
	}
}
