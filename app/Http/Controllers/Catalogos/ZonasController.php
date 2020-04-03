<?php

namespace App\Http\Controllers\Catalogos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ZonasController extends Controller
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
			case 3: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE, "ID" => $respuestaSP[0]->ID]; break;			
			case 4: $info = ["SUCCESS" => 1, "MESSAGE" => 'Info guardada con éxito', "ID" => 1]; break; 
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
	public function index(Request $request)
	{
		try {
			$lista = DB::select('CALL CAT_obtZonasG(?, ?)', [1, ""]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
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
	 * Inserta o Acutaliza un registro
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		try {
			$nombre_sp = '';
			$arrayParametros = Array();
			
			// Verificar si existe la accion
			// Accion Guardar: 0 - Editar: 1
			$nombre_sp = $request->input("accion") == 0 ? 'CAT_insZonaV2' : 'CAT_modZonaV2';

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;
			$arrayParametros[1] = $request->has("nombre") ? trim($request->input("nombre")) : "";
			$arrayParametros[2] = $request->has("estatus") ? ($request->input("estatus") ? 1 : 0) : 0;
			$arrayParametros[3] = $request->has("tipoServicio_id") ? $request->input("tipoServicio_id") : 0;
			$arrayParametros[4] = $request->has("coordinador_id") ? $request->input("coordinador_id") : 0;
			
			// Validar datos faltantes
			if(trim($arrayParametros[1]) == "" || $arrayParametros[3] == 0 || $arrayParametros[4] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			$tipoRespuesta = 3;
			if($nombre_sp != '') {
				// Ejecutar SP Base de datos
				if($nombre_sp == 'CAT_insZonaV2') {
					$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?);", [$arrayParametros[3], $arrayParametros[4], trim($arrayParametros[1])]);
				} else {
					$tipoRespuesta = 0;
					$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[4]]);
				}

				return $this->formatearRespuesta($responseSP, 200, $tipoRespuesta);
			} else {
				return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
			}
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Inserta o Acutaliza la lista de Técnicos a una Zona
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function ZonaTecnicos(Request $request)
	{
		try {
			$nombre_sp = '';
			$arrayParametros = Array();

			// Verificar si existe la accion
			// Accion Guardar: 0 - Editar: 1
			$nombre_sp = $request->input("accion") == 0 ? 'psm.TEC_insTecnicosAZona' : 'psm.CAT_modTecnicosV2';


			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("empleado_id") ? $request->input("empleado_id") : 0;
			$arrayParametros[1] = $request->has("zona_id") ? $request->input("zona_id") : 0;
			
			// Validar datos faltantes
			if( $arrayParametros[0] == 0 || $arrayParametros[1] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			if($nombre_sp != '') {
				// Ejecutar SP Base de datos
				if($nombre_sp == 'psm.TEC_insTecnicosAZona') {
					$responseSP = DB::select("CALL ".$nombre_sp."(?, ?);", [$arrayParametros[0], $arrayParametros[1]]);
				} else {
					$responseSP = DB::select("CALL ".$nombre_sp."(?, ?);", [$arrayParametros[0], $arrayParametros[1]]);
				}

				return $this->formatearRespuesta($responseSP, 200);
			} else {
				return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
			}
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Inserta o Acutaliza la lista de Estados a una Zona
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function ZonaEstados(Request $request)
	{
		try {
			$nombre_sp = '';
			$arrayParametros = Array();

			// Verificar si existe la accion
			// Accion Guardar: 0 - Editar: 1
			$nombre_sp = $request->input("accion") == 0 ? 'psm.EDO_insEstadoAZona' : 'psm.EDO_modEstadosAZona';

			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("estado_id") ? $request->input("estado_id") : 0;
			$arrayParametros[1] = $request->has("zona_id") ? $request->input("zona_id") : 0;
			
			// Validar datos faltantes
			if( $arrayParametros[0] == 0 || $arrayParametros[1] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			if($nombre_sp != '') {
				// Ejecutar SP Base de datos
				if($nombre_sp == 'psm.EDO_insEstadoAZona') {
					$responseSP = DB::select("CALL ".$nombre_sp."(?, ?);", [$arrayParametros[0], $arrayParametros[1]]);
				} else {
					$responseSP = DB::select("CALL ".$nombre_sp."(?, ?);", [$arrayParametros[0], $arrayParametros[1]]);
				}

				return $this->formatearRespuesta($responseSP, 200);
			} else {
				return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
			}
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener lista para Combos(selects)
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request)
	{
		try {
			$lista = DB::select('CALL CAT_obtZonasG(?, ?)', [$request->has("opc") ? $request->input("opc") : 10, $request->input("buscar")]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener lista de Estados por Tipo de Servicio
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function obtEstados(Request $request)
	{
		try {
			$lista = DB::select('CALL EDO_obtEstadosXAsignarXTipoServicio(?)', [$request->has("serivicio_id") ? $request->input("serivicio_id") : 0]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener lista de Estados y de una zona
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function estados(Request $request)
	{
		try {
			$lista = DB::select('CALL EDO_obtEstadosXZona(?, ?)', [
				$request->has("id") ? $request->input("id") : 0,
				$request->has("opc") ? $request->input("opc") : 0,
				]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener lista de Técnicos de una zona
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function tecnicos(Request $request)
	{
		try {
			$lista = DB::select('CALL TEC_obtTecnicosXZona(?, ?, ?)', [
				$request->has("id") ? $request->input("id") : 0,
				$request->has("nombre") ? $request->input("nombre") : "",
				$request->has("opc") ? $request->input("opc") : 0,
				]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
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
	 * Deshabilitar un registro por el id
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request)
	{
		try {
			// Ejecutar SP Base de datos
			$responseSP = DB::select('CALL CAT_eliZonasV2(?, ?)', [$request->input("id"), $request->input("estatus") ? 0 : 1]);
			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}
}
