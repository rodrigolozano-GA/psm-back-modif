<?php

namespace App\Http\Controllers\Sistema;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdministracionController extends Controller
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
		// 3 - Retorna el ID y Folio del nuevo registro
		// 4 - Retorna el TICKET, FOLIO, INICIO, FIN del nuevo registro
		// 5 - Retorna campos personalizados de respuesta para realizar pruebas
		switch($opc) {
			case 0: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE]; break;
			case 1: $info = ["SUCCESS" => 1, "MESSAGE" => '', "DATA" => $respuestaSP];break;
			case 2: $info = ["SUCCESS" => $exito, "MESSAGE" => $mensaje]; break;
			case 3: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE, "ID" => $respuestaSP[0]->ID, "FOLIO" => $respuestaSP[0]->FOLIO]; break;
			case 4: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE, "TICKET" => $respuestaSP[0]->TICKET, "INICIO" => $respuestaSP[0]->INICIO, "FIN" => $respuestaSP[0]->FIN]; break;
			case 5: $info = ["SUCCESS" => 1, "MESSAGE" => "guardado éxito - ".$mensaje, "ID" => 1, "FOLIO" => "123456789"]; break;
		}
		return response()->json($info, 
		$codeHTTP, 
		['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], 
		JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Obtener todas las solicitudes de actas aprobadas
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtSolicitudesAprobadas(Request $request)
	{
		try {
			$nombre_sp = 'ASO_obtSolicitudesAprovar';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);",[2]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener el detalle de una solicitud seleccionada
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtDetalleActas(Request $request)
	{
		try {
			$nombre_sp = 'AEM_obtActasValidar';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);",[$arrayParametros[0]]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener los motivos al validar actas aprobadas
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtMotivosActas(Request $request)
	{
		try {
			$nombre_sp = 'CAT_ObtMotivosG';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?);",[
				11, 'cat_actas', 50
			]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Validar Acta de una solicitud
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveValidarActas(Request $request)
	{
		try {
			$nombre_sp = 'AEM_modActa';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("opc") ? $request->input("opc") : 2;
			$arrayParametros[1] = $request->has("acta") ? $request->input("acta") : 0;
			$arrayParametros[2] = $request->has("motivo") ? $request->input("motivo") : 0;
			
			// Validar datos faltantes
			if($arrayParametros[1] == 0 || $arrayParametros[2] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?, ?);",[ $arrayParametros[0], $arrayParametros[1], $arrayParametros[2], "" ]);
			
			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener todas las solicitudes de actas por aprobar / no aprobar
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtSolicitudesAutorizar(Request $request)
	{
		try {
			$nombre_sp = 'ASO_obtSolicitudesAprovar';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);",[1]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener el listado de Advertencias para un Acta/s de acuerdo al Estatus
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtAdvertenciasActa(Request $request)
	{
		try {
			// REP_obtReporteAdvertencias
			// (p_idSolicitudEmpleado(opcion 1 - idSolicitud opcion 2 idEmpleado) - 
			// p_idEstatusActa(opcion 1 idEstatus opcion 2 idActa) - 
			// p_opcion 1 autorizacion 2 solicitud)
			$nombre_sp = 'REP_obtReporteAdvertencias';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id Acta
			$arrayParametros[1] = $request->has("estatus") ? $request->input("estatus") : 0; // id Estatus
			$arrayParametros[2] = 1; // Autorizacion

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2]]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Aprobar / no aprobar las solicitudes del técnico seleccionado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveAprobarSolicitud(Request $request)
	{
		try {
			$nombre_sp = 'ASO_modEstatusSolicitudActas';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id de la solicitud
			$arrayParametros[1] = $request->has("estatus") ? $request->input("estatus") : 0;
			$arrayParametros[2] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			$arrayParametros[3] = $request->has("ticket") ? $request->input("ticket") : "";
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3]]);
			
			return $this->formatearRespuesta($responseSP, 200, 4);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar el motivo al no aprobar las solicitudes del técnico seleccionado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveSolicitudMotivo(Request $request)
	{
		try {
			$nombre_sp = 'MOT_insMotivosAServicio';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id de la solicitud
			$arrayParametros[1] = $request->has("solicitud") ? $request->input("solicitud") : 0;
			$arrayParametros[2] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			$arrayParametros[3] = $request->has("opc") ? $request->input("opc") : 2;
			
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3]]);
			
			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener paquete de Actas enviadas desde el Armado de Actas
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtActasEnviadas(Request $request)
	{
		try {
			$nombre_sp = 'AEN_obtFoliosEnvios';
			$arrayParametros = array();
			
			// Obtener los parametros del request

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."();");
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener el detalle de un paquete de Actas enviado desde el Armado de Actas
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtDetalleActasEnviadas(Request $request)
	{
		try {
			$nombre_sp = 'AEN_obtActasXEnvio';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("paquete_id") ? $request->input("paquete_id") : 0;
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Confirmar las Actas de la recepción
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveConfirmarActas(Request $request)
	{
		try {
			$nombre_sp = 'AEM_modEstatusRecibidas';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id Acta
			$arrayParametros[1] = $request->has("estatus") ? $request->input("estatus") : 0;
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?);", [$arrayParametros[0], $arrayParametros[1]]);
			
			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener lista de Folios (Servicios) para el Seguimiento de Folios de Servicio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtSeguimFolios(Request $request)
	{
		try {
			$nombre_sp = 'FOL_obtFolios';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [2]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar los Folios a Reportar
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveReporteServicios(Request $request)
	{
		try {
			$nombre_sp = 'FOL_modFoliosReportados';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del Folio
			$arrayParametros[1] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			
			// Validar datos faltantes
			if($arrayParametros[1] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?);", [$arrayParametros[0], $arrayParametros[1]]);
			
			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

}
