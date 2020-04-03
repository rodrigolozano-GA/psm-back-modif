<?php

namespace App\Http\Controllers\Sistema;

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
		// 3 - Retorna el ID del nuevo registro
		switch($opc) {
			case 0: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE]; break;
			case 1: $info = ["SUCCESS" => 1, "MESSAGE" => '', "DATA" => $respuestaSP];break;
			case 2: $info = ["SUCCESS" => $exito, "MESSAGE" => $mensaje]; break;
			case 3: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE, "ID" => $respuestaSP[0]->ID, "FOLIO" => $respuestaSP[0]->FOLIO]; break;
		}
		return response()->json($info,
		$codeHTTP,
		['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
		JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Obtener la sucursal al seleccionar un cliente
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function obtsucursal(Request $request)
	{
		try {
			$lista = DB::select('CALL CAT_obtSucursalesXCliente(?)', [$request->has("id") ? $request->input("id") : 0]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener los Costos al seleccionar un cliente, costos viáticos, hospedaje, por km y casetas
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function obtCostosCliente(Request $request)
	{
		try {
			// COT_obtCostosViaticos (p_idCliente) retorno km - alimentos - hospedaje - casetas
			$lista = DB::select('CALL COT_obtCostosViaticos(?)', [$request->has("id") ? $request->input("id") : 0]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener el detalle de una sucursal seleccionada
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function sucursalDetalle(Request $request)
	{
		try {
			$lista = DB::select('CALL CAT_obtDatosSucursal(?)', [$request->has("id") ? $request->input("id") : 0]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener todos los Servicios y/o Refacciones de un Cliente
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function obtservicios(Request $request)
	{
		try {
			$nombre_sp = 'PRO_obtProductosXCliente';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("cliente_id") ? $request->input("cliente_id") : 0;

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?);", [$arrayParametros[0]]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Insertar una nueva cotización
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function cotizacion(Request $request)
	{ 
		try {
			$nombre_sp = '';
			$arrayParametros = array();

			// Verificar si existe un id
			$nombre_sp = $request->input("id") == 0 ? 'COT_insCotizaciones' : 'COT_insCotizaciones';

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;
			
			$arrayParametros[1] = $request->has("sucursal_id") ? $request->input("sucursal_id") : 0;
			$arrayParametros[2] = $request->has("tipoServicio_id") ? $request->input("tipoServicio_id") : 0;
			$arrayParametros[3] = $request->has("usuario_id") ? $request->has("usuario_id") : 0;
			$arrayParametros[4] = $request->has("costokm") ? $request->input("costokm") : 0;
			$arrayParametros[5] = $request->has("viaticoAlimento") ? $request->input("viaticoAlimento") : 0;
			$arrayParametros[6] = $request->has("viaticoHospedaje") ? $request->input("viaticoHospedaje") : 0;
		
			$arrayParametros[7] = $request->has("folio") ? $request->input("folio") : "";
	
			// Validar datos faltantes
			if($arrayParametros[1] == 0 || $arrayParametros[2] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}
			if($nombre_sp != '') {
				// Ejecutar SP Base de datos
				if($nombre_sp == 'COT_insCotizaciones') {
					$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?, ?, ?, ?, ?);", [$arrayParametros[1], $arrayParametros[2], $arrayParametros[3], $arrayParametros[4], $arrayParametros[5], $arrayParametros[6], $arrayParametros[7]]);
				} else {
					$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?, ?, ?, ?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3], $arrayParametros[4], $arrayParametros[5], $arrayParametros[6], $arrayParametros[7]]);
				}

				return $this->formatearRespuesta($responseSP, 200, 3);
			} else {
				return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
			}
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar los Productos/Servicios de una cotización
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function servicios(Request $request)
	{
		try {
			$nombre_sp = 'CPR_modCotizacionesServicios';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;
			$arrayParametros[1] = $request->has("cantidad") ? (int) $request->input("cantidad") : 0;
			$arrayParametros[2] = $request->has("cotizacion_id") ? $request->input("cotizacion_id") : 0;
			$arrayParametros[3] = $request->has("codigo") ? $request->input("codigo") : "";
			$arrayParametros[4] = $request->has("concepto") ? $request->input("concepto") : "";
			$arrayParametros[5] = $request->has("precio") ? $request->input("precio") : 0;

			// Validar datos faltantes
			if($arrayParametros[2] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?, ?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3], $arrayParametros[4], $arrayParametros[5]]);

			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar los Traslados de una cotización
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function traslados(Request $request)
	{
		try {
			$nombre_sp = 'CTR_modCotizacionesTraslados';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("origen") ? $request->input("origen") : "";
			$arrayParametros[1] = $request->has("destino") ? $request->input("destino") : "";
			$arrayParametros[2] = $request->has("distancia") ? $request->input("distancia") : 0;
			$arrayParametros[3] = $request->has("casetas") ? $request->input("casetas") : 0;
			$arrayParametros[4] = $request->has("cotizacion_id") ? $request->input("cotizacion_id") : 0;

			// Validar datos faltantes
			if(trim($arrayParametros[0]) == "" || trim($arrayParametros[1]) == "" || $arrayParametros[4] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3], $arrayParametros[4]]);

			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar los Viaticos de una cotización
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function viaticos(Request $request)
	{
		try {
			$nombre_sp = 'CVI_modCotizacionesViaticos';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("personas") ? $request->input("personas") : 0;
			$arrayParametros[1] = $request->has("dias") ? $request->input("dias") : 0;
			$arrayParametros[2] = $request->has("noches") ? $request->input("noches") : 0;
			$arrayParametros[3] = $request->has("origen") ? $request->input("origen") : "";
			$arrayParametros[4] = $request->has("destino") ? $request->input("destino") : "";
			$arrayParametros[5] = $request->has("costokm") ? $request->input("costokm") : 0;
			$arrayParametros[6] = $request->has("cotizacion_id") ? $request->input("cotizacion_id") : 0;

			// Validar datos faltantes
			if($arrayParametros[0] == 0 ||  trim($arrayParametros[3]) == "" || trim($arrayParametros[4]) == "" || $arrayParametros[6] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?, ?, ?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3], $arrayParametros[4], $arrayParametros[5], $arrayParametros[6]]);

			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Mostrar todas las Cotizaciones
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function cotizaciones(Request $request)
	{
		try {
			$nombre_sp = 'COT_obtCotizaciones';
			$arrayParametros = array();

			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?);", [$request->has("opc") ? $request->input("opc") : 1, $request->has("id") ? $request->input("id") : 0]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar el estatus de una Cotización
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function modestatus(Request $request)
	{
		try {
			$nombre_sp = 'COT_modEstatusCotizacion';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;
			$arrayParametros[1] = $request->has("estatus_id") ? $request->input("estatus") : 0;
			$arrayParametros[2] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;

			// Validar datos faltantes
			if($arrayParametros[0] == 0 || $arrayParametros[1] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2]]);

			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar el motivo de una Cotización
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function modmotivo(Request $request)
	{
		try {
			$nombre_sp = 'MOT_insMotivosAServicio';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("motivo_id") ? $request->input("motivo_id") : 0;
			$arrayParametros[1] = $request->has("id") ? $request->input("id") : 0; // id del folio
			$arrayParametros[2] = $request->has("usuario_id") ? $request->input("usuario_id") : 1;
			$arrayParametros[3] = 0; // opcion 1 = motivos para cotización
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3]]);

			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}
}
