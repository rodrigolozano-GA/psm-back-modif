<?php

namespace App\Http\Controllers\Sistema;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;

class OperacionesController extends Controller
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
		// 4 - Retorna campos personalizados de respuesta para realizar pruebas
		switch($opc) {
			case 0: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE]; break;
			case 1: $info = ["SUCCESS" => 1, "MESSAGE" => '', "DATA" => $respuestaSP];break;
			case 2: $info = ["SUCCESS" => $exito, "MESSAGE" => $mensaje]; break;
			case 3: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE, "ID" => $respuestaSP[0]->ID]; break;
			case 4: $info = ["SUCCESS" => 1, "MESSAGE" => "guardado éxito - ".$mensaje, "ID" => 1, "FOLIO" => "123456789"]; break;
		}
		return response()->json($info,
		$codeHTTP,
		['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
		JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Guardar número de Actas para un folio al cambiar el estatus a Realizado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveActasFolio(Request $request)
	{
		try {
			$nombre_sp = 'AEM_modActa';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("acta_id") ? $request->input("acta_id") : 0;
			$arrayParametros[1] = $request->has("id") ? $request->input("id") : 0; // id del folio
			$arrayParametros[2] = $request->has("descripcion") ? trim($request->input("descripcion")) : "";

			// Validar datos faltantes
			if( $arrayParametros[0] == 0 || $arrayParametros[2] == "" ) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?);", [
				1, $arrayParametros[0], $arrayParametros[1], $arrayParametros[2]
			]);

			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener todos los servicios por usuario
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtServicios(Request $request)
	{
		try {
			$nombre_sp = 'FOL_obtFoliosXUsuario';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener todos los servicios por usuario - para el calendario
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtServsCalendario(Request $request)
	{
		try {
			$nombre_sp = 'FOL_obtFoliosCalendario';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			$arrayParametros[1] = $request->has("mes") ? $request->input("mes") : 0;

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?);", [
				$arrayParametros[0], $arrayParametros[1]
			]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener todos los tecnicos
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtTecnicos(Request $request)
	{
		try {
			$nombre_sp = 'TEC_obtTecnicosXCoordinador';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar Solicitud de Actas para un folio al cambiar el estatus a Realizado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveActasTecnico(Request $request)
	{
		try {
			$nombre_sp = 'ASO_insSolicitudActa';
			$arrayParametros = array();

			// Obtener los parametros del request

			$arrayParametros[0] = $request->has("empleado") ? $request->input("empleado") : 0;
			$arrayParametros[1] = $request->has("cantidad") ? $request->input("cantidad") : 0;
			$arrayParametros[2] = $request->has("formato_id") ? $request->input("formato_id") : 0;
			$arrayParametros[3] = $request->has("solicitante") ? $request->input("solicitante") : 0;
			$arrayParametros[4] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?);", [
				$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3], $arrayParametros[4]
			]);

			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener las Actas de un Técnico que pertenecen a un Folio (Servicio) con estatus REALIZADO
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtActasFolioTecnico(Request $request)
	{
		try {
			$nombre_sp = 'AEM_obtActasXTecnicosFolio';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener el Estado de Cuenta de un Técnico
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	
	public function obtEstadoCuentaTecnico(Request $request)
	{
		try {
			$nombre_sp = 'AEM_obtEstadoCuentaXTecnicos';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}
  
	/**
	 * Obtener el listado de Advertencias para un Acta
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
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id Empleado
			$arrayParametros[1] = $request->has("acta") ? $request->input("acta") : 0; // id Acta
			$arrayParametros[2] = 2; // Solicitud

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2]]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener todas las actas de la opción Armado de Actas
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtActas(Request $request)
	{
		try {
			$nombre_sp = 'AEM_obtActasXTecnicosFolio';
			$arrayParametros = array();

			// Obtener los parametros del request

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [0]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}
	
	/**
	 * Obtener los Estadis de Cuenta de un Tecnico
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtEstadosCuenta(Request $request)
	{
		try{
			$nombre_sp ='TEC_obtEdoCuentaTecnico';
			$arrayParametros = array();
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id Empleado
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);
			return $this->formatearRespuesta($responseSP, 200, 1);
	

		}catch(\Throwable $th){
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0,"Error al procesar la solicitud");
		}
	}

	/**
	 * Enviar actas desde el Armado de Actas (Operaciones)
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function enviarActas(Request $request)
	{
		try {
			$nombre_sp = 'AEM_insEnvioActas';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("acta_id") ? $request->input("acta_id") : 0; // id del acta
			$arrayParametros[1] = $request->has("folio_id") ? $request->input("folio_id") : 0;
			$arrayParametros[2] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2]]);

			return $this->formatearRespuesta($responseSP, 200, 3);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Enviar correo desde el Armado de Actas (Operaciones)
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function enviarCorreo(Request $request)
	{
		try {
			Mail::send('emails.enviarCorreo', ['name' => 'Prueba test'], function (Message $message) {
				$message->to('usuario@prueba.mx', 'Prueba test')
				->from('pruebas@correo.mx', 'Prueba test')
				->subject('prueba correo');
			});

			return $this->formatearRespuesta([], 200, 2, 1, "Se ha enviado un correo");
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	public function servicefilter(Request $request)
	{
		$nombre_sp = 'FPR_insProductoFolio';
		$arrayParametros = array();
		$arrayParametros[0] = $request->has("folio_id") ? $request->input("folio_id") : 0; 
		$servicios = $request ['servicios'];
		$cadRes = "";
		foreach($servicios as $servicio)
		{
			$index = 1;
			foreach($servicio as $value)
			{
				$arrayParametros[$index] = $value ;
				$index++;
			}
			if((int)$arrayParametros[6] == 1)
			{
				$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?, ?);", [$arrayParametros[1], $arrayParametros[5], $arrayParametros[0], $arrayParametros[2], $arrayParametros[3], $arrayParametros[4]]);
				if((int)$responseSP[0]->SUCCESS != 1)
				{
					return $this->formatearRespuesta($responseSP, 200);
					$cadRes .= $responseSP[0]->MESSAGE . " | " . implode("," ,$arrayParametros) ;
				}
			}
		}
		return $this->formatearRespuesta([], 200, 2, 0, $cadRes);
	}


	/**
	 * Guardar los Productos/Servicios de un folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	/*
	public function serviciosfolio(Request $request)
	{
		try {
			$nombre_sp = 'FPR_insProductoFolio';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;
			$arrayParametros[1] = $request->has("cantidad") ? (int) $request->input("cantidad") : 0;
			$arrayParametros[2] = $request->has("folio_id") ? $request->input("folio_id") : 0;
			$arrayParametros[3] = $request->has("codigo") ? $request->input("codigo") : "";
			$arrayParametros[4] = $request->has("concepto") ? trim($request->input("concepto")) : "";
			$arrayParametros[5] = $request->has("precio") ? $request->input("precio") : 0;

			// FILTRANDO SOLO LOS NUEVOS
			$paramnew =(int)$request['nuevo'];
			//END

			// Validar datos faltantes
			if($arrayParametros[2] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			//Filtrando solo los nuevos
			if($paramnew == 1)
			{
				$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3], $arrayParametros[4], $arrayParametros[5]]);
				return $this->formatearRespuesta($responseSP, 200);

			}
			else
			{
				return $this->formatearRespuesta([], 200, 2, 0, "Datos actualizados.");
			}
			//end

			// Ejecutar SP Base de datos
			//$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3], $arrayParametros[4], $arrayParametros[5]]);

			//return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	public function serviciosFol(Request $request)
	{
		try {
			$nombre_sp = 'FPR_insProductoFolio';
			$arrayParametros = array();
			$arrayParametros[0] = $request->has("folio_id") ? $request->input("folio_id") : 0; // id folio
			$servicios = $request ['servicios'];
			$cadRes = '';
			foreach($servicios as $servicio)
			{
				$index = 1;
				foreach($servicio as $value)
				{
					$arrayParametros[$index] = $value ;

					$index++;
				}
				$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?, ?);", [$arrayParametros[1], $arrayParametros[5], $arrayParametros[0], $arrayParametros[2], $arrayParametros[3], $arrayParametros[4]]);
				if((int)$responseSP[0]->SUCCESS == 1)
				{
					$cadRes .= "Si se guardo.   ";
				}
				
				
			}
			return $cadRes;

		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([],500,2,0,"Error al procesar la solicitud");
		}
	}
 */
}
