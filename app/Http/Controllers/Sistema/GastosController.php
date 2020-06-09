<?php

namespace App\Http\Controllers\Sistema;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GastosController extends Controller
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
		// 4 - Retorna el ID del nuevo registro
		// 5 - Retorna campos personalizados de respuesta para realizar pruebas
		switch($opc) {
			case 0: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE]; break;
			case 1: $info = ["SUCCESS" => 1, "MESSAGE" => '', "DATA" => $respuestaSP];break;
			case 2: $info = ["SUCCESS" => $exito, "MESSAGE" => $mensaje]; break;
			case 3: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE, "ID" => $respuestaSP[0]->ID, "FOLIO" => $respuestaSP[0]->FOLIO]; break;
			case 4: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE, "ID" => $respuestaSP[0]->ID]; break;
			case 5: $info = ["SUCCESS" => 1, "MESSAGE" => "guardado éxito - ".$mensaje, "ID" => 1, "FOLIO" => "123456789"]; break;
		}
		return response()->json($info, 
		$codeHTTP, 
		['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], 
		JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Obtener la lista de Tipos de Gasto
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtTiposGastoCombo(Request $request)
	{
		try {
			$nombre_sp = 'TGA_obtTiposGastosG';
			
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
	 * Obtener Empleados Activos
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtEmpleadosCombo(Request $request)
	{
		try {
			$nombre_sp = 'TEC_obtTecnicos';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			//$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."();");
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener Estado de Cuenta del empleado 
	 * Solicitar info del regiosis
	 */
	public function solEdoCuenta(Request $request)
	{
		try {
			$nombre_sp = 'GAS_insSolicitarEdoCuentaEmpleado';
			$arrayParametros =  array();
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;
			$responseSP = DB::Select("CALL ".$nombre_sp."(?);",[$arrayParametros[0]]);
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([],500,2,0,"Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener el estado de cuenta 
	 */
	public function obtEdoCuenta(Request $request)
	{
		try {
			$nombre_sp = 'GAS_obtTotalAdeudoEmpleado';
			$arrayParametros = array();
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;
			$responseSP = DB::Select("CALL ".$nombre_sp."(?)",[$arrayParametros[0]]);
			return $this->formatearRespuesta($responseSP, 200, 1);
			
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([],500,2,0,"Error al procesar la solicitud");
		}

	}

	/**
	 * Obtener la lista de Servcicios de un Empleado Seleccionado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtServiciosEmpleado(Request $request)
	{
		try {
			$nombre_sp = 'FOL_obtFoliosXEmpleado';
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
	 * Obtener la lista de Servcicios de un Empleado Seleccionado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtProductosEmpleado(Request $request)
	{
		try {
			//$nombre_sp = 'PRO_obtProductos';
			$nombre_sp = 'PRO_obtProductosGastos';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			//$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."();");
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener la lista de Tipo de Gasto para los Conceptos
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtTipoGastoConcepto(Request $request)
	{
		try {
			$nombre_sp = 'TCO_obtTiposConceptoGastosG';
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
	 * Obtener la lista de Conceptos de Gasto de acuerdo al Tipo de Gasto seleccionado del Concepto
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtConceptosGastoConcepto(Request $request)
	{
		try {
			$nombre_sp = 'GDE_obtGastosDetalle';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("tipo") ? $request->input("tipo") : 0;
			$arrayParametros[1] = $request->has("id") ? $request->input("id") : 0;
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?);", [$arrayParametros[0], $arrayParametros[1]]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar Gasto general
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveGastoGeneral(Request $request)
	{
		try {
			//GAS_insGasto ( p_folio p_idTipoGasto - p_idTecnico - p_abonoAdeudo - p_totalAdeudo - 
			// p_totalDepositado - p_descripcionMaterial - p_idUsuario) 
			//retorna SUCCESS - MESSAGE -FOLIO - ID

			$nombre_sp = 'GAS_insGasto';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("folio") ? $request->input("folio") : ""; // Folio
			$arrayParametros[1] = $request->has("tipo_gasto") ? $request->input("tipo_gasto") : 0;
			$arrayParametros[2] = $request->has("n_empleado") ? $request->input("n_empleado") : 0;
			$arrayParametros[3] = $request->has("abono_adeudo") ? $request->input("abono_adeudo") : 0;
			$arrayParametros[4] = $request->has("total_adeudo") ? $request->input("total_adeudo") : 0;
			$arrayParametros[5] = $request->has("total_depositado") ? $request->input("total_depositado") : 0;
			$arrayParametros[6] = $request->has("descripcion") ? $request->input("descripcion") : "";
			$arrayParametros[7] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			
			// Validar datos faltantes
			if($arrayParametros[0] == "" || $arrayParametros[1] == 0 || $arrayParametros[2] == 0 || $arrayParametros[7] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?, ?, ?, ?);", [
				$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3],
				$arrayParametros[4], $arrayParametros[5], $arrayParametros[6], $arrayParametros[7]
			]);

			return $this->formatearRespuesta($responseSP, 200, 3);
			//return $this->formatearRespuesta("", 200, 5);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar Servicios (OdeS) seleccionados de un Gasto
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveGastoServicios(Request $request)
	{
		try {
			// GAS_insFoliosXGastos(p_idGasto - p_idFolio - p_idUsuario)
			// retorno SUCCESS - MESSAGE

			$nombre_sp = 'GAS_insFoliosXGastos';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("gasto_id") ? $request->input("gasto_id") : 0; // id del gasto
			$arrayParametros[1] = $request->has("servicio_id") ? $request->input("servicio_id") : 0; // id del servicio seleccionado
			$arrayParametros[2] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?);", [ $arrayParametros[0], $arrayParametros[1], $arrayParametros[2] ]);
			
			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar Productos para un Gasto
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveGastoProductos(Request $request)
	{
		try {
			// GPR_insRelacionProductoGasto (p_idGasto - p_idProducto - p_cantidad - p_idUsuario) 
			// retorno SUCCESS- MESSAGE
			//$nombre_sp = 'GPR_insRelacionProductoGasto';
			$nombre_sp = 'GPR_insRelacionProductoGastoV2';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("gasto_id") ? $request->input("gasto_id") : 0; // id del gasto
			$arrayParametros[1] = $request->has("producto_id") ? $request->input("producto_id") : 0; // id del producto
			$arrayParametros[2] = $request->has("cantidad") ? $request->input("cantidad") : 0;
			$arrayParametros[3] = $request->has("total") ? $request->input("total") : 0;
			$arrayParametros[4] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			
			// Validar datos faltantes
			
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
	 * Guardar Concepto para un Gasto
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveGastoConcepto(Request $request)
	{
		try {
			// GDM_insDetalleMontoConcepto (p_idConceptoGasto,p_idGasto,p_idUsuario) 
			// retorna SUCCESS - MESSAGE - ID ( detalleNontoConcepto)
			$nombre_sp = 'GDM_insDetalleMontoConcepto';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("concepto_id") ? $request->input("concepto_id") : 0;
			$arrayParametros[1] = $request->has("gasto_id") ? $request->input("gasto_id") : 0;
			$arrayParametros[2] = $request->has("monto") ? $request->input("monto") : 0;
			$arrayParametros[3] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			
			// Validar datos faltantes
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?);", [ 
				$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3] 
			]);
			
			return $this->formatearRespuesta($responseSP, 200, 4);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar el detalle del Concepto para un Gasto
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveGastoConceptoDetalle(Request $request)
	{
		try {
			// FMO_insFoliosXGastosMontos ( p_idGastoMontoDetalle - p_idFolio - p_monto - p_idUsuario ) 
			// retorno SUCCESS - MESSAGE
			$nombre_sp = 'FMO_insFoliosXGastosMontos';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			//$arrayParametros[0] = $request->has("gasto_id") ? $request->input("gasto_id") : 0; // id del gasto
			$arrayParametros[0] = $request->has("gasto_concepto_id") ? $request->input("gasto_concepto_id") : 0; // id del concepto del gasto
			$arrayParametros[1] = $request->has("servicio_id") ? $request->input("servicio_id") : 0;
			$arrayParametros[2] = $request->has("monto") ? $request->input("monto") : 0;
			$arrayParametros[3] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			
			// Validar datos faltantes
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?);", [ 
				$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3] 
			]);
			
			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener la lista de Gastos Generales
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtGastoGeneral(Request $request)
	{
		try {
			$nombre_sp = 'GAS_obtGastosG';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			//$arrayParametros[0] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."();");
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener la lista de Estatus para los Gastos
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtEstatusGastos(Request $request)
	{
		try {
			$nombre_sp = 'CAT_obtEstatusG';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			//$arrayParametros[0] = $request->has("p_opcion") ? $request->input("p_opcion") : 0;
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?);", [16, 0]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener la lista de Servicios del Gasto seleccionado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtServiciosSegGastos(Request $request)
	{
		try {
			$nombre_sp = 'FOG_obtFoliosXGasto';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del Gasto general
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	public function obtAdeudoEmpleado(Request $request)
	{
		try {
			$nombre_sp = 'GAS_obtTotalAdeudoEmpleado';
			$arrayParametros = array();
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0;
			$responseSP = DB::select("CALL ".$nombre_sp."(?);",[$arrayParametros[0]]);
			return $this->formatearRespuesta($responseSP,200,1);

		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener la lista de Productos del Gasto seleccionado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtProductosSegGastos(Request $request)
	{
		try {
			$nombre_sp = 'GPR_obtProductosXGastos';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del Gasto general

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener la lista de Conceptos del Gasto seleccionado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtConceptosSegGastos(Request $request)
	{
		try {
			$nombre_sp = 'GDM_obtDetalleConceptosXGasto';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del Gasto general
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener el Detalle del Concepto seleccionado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtConceptosDetSegGastos(Request $request)
	{
		try {
			// FMO_obtFoliosMontosXDetalle(p_idGastoDetalleMonto) retorna id - folio - monto
			$nombre_sp = 'FMO_obtFoliosMontosXDetalle';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del Concepto de Gasto seleccionado
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar Estatus del Gasto
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveEstatusGasto(Request $request)
	{
		try {
			// GAS_modEstatusGastos (p_idGasto - p_idEstatus - p_idUsuario) retorna SUCCESS - MESSAGE
			$nombre_sp = 'GAS_modEstatusGastos';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del gasto
			$arrayParametros[1] = $request->has("estatus") ? $request->input("estatus") : 0; // id del estatus
			$arrayParametros[2] = 1; //$request->has("usuario_id") ? $request->input("usuario_id") : 0; // id del estatus
			
			// Validar datos faltantes
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?);", [ $arrayParametros[0], $arrayParametros[1], $arrayParametros[2] ]);
			
			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener los Motivos para el Estatus Cancelado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtMotivoEstatus(Request $request)
	{
		try {
			//CAT_ObtMotivosG( p_opcion(11)- p_catalogo('')- p_id) retorna id - nombre
			$nombre_sp = 'CAT_ObtMotivosG';
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?, ?, ?);", [11, '', $request->has("id") ? $request->input("id") : 0]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar Motivo de Estatus para el Gasto
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveMotivoEstatus(Request $request)
	{
		try {
			// MOT_insMotivosAServicio (p_idMotivo - p_idServicio( en este caso id gasto)- p_idUsuario- p_opcion(3) retorna SUCCESS - MESSAGE
			$nombre_sp = 'MOT_insMotivosAServicio';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("motivo") ? $request->input("motivo") : 0; // id del motivo
			$arrayParametros[1] = $request->has("id") ? $request->input("id") : 0; // id del gasto
			$arrayParametros[2] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			
			// Validar datos faltantes
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?);", [ $arrayParametros[0], $arrayParametros[1], $arrayParametros[2], 3 ]);
			
			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar los Gastos a Reportar
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveReportarGasto(Request $request)
	{
		try {
			//GAS_insGastoAReportar (p_idGasto - p_idUsuario) retorna SUCCESS - MESSAGE
 
			$nombre_sp = 'GAS_insGastoAReportar';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del gasto
			$arrayParametros[1] = $request->has("usuario_id") ? $request->input("usuario_id") : 0;
			
			// Validar datos faltantes
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?);", [ $arrayParametros[0], $arrayParametros[1] ]);
			
			return $this->formatearRespuesta($responseSP, 200,1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	public function obtDatosReportar(Request $request)
	{
		try {
			$nombre_sp='GAS_obtDatosReportar';
			$arrayParametros = array();
				
			//Obtener parametros del request
			$arrayParametros[0] = $request->has("idGasto") ? $request-> input("idGasto") : 0;
			$arrayParametros[1] = $request->has("idUsuario") ? $request-> input("idUsuario") : 0;
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?);", [ $arrayParametros[0], $arrayParametros[1] ]);
			return $this->formatearRespuesta($responseSP, 200,1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	public function respuestaGasto(Request $request)
	{
		try {
			$nombre_sp='GAS_repGasto';
			$arrayParametros = array();

			//Obtener parametros del request
			$arrayParametros[0] = $request->has("idGasto") ? $request-> input("idGasto") : 0;
			$arrayParametros[1] = $request->has("idFolio") ? $request-> input("idFolio") : 0;
			$arrayParametros[2] = $request->has("idCliente") ? $request-> input("idCliente") : 0;
			$arrayParametros[3] = $request->has("idSucursal") ? $request-> input("idSucursal") : 0;
			$arrayParametros[4] = $request->has("folio") ? $request-> input("folio") : 0;
			$arrayParametros[5] = $request->has("tipoGasto") ? $request-> input("tipoGasto") : 0;
			$arrayParametros[6] = $request->has("folioGasto") ? $request-> input("folioGasto") : 0;
			$arrayParametros[7] = $request->has("codigoAlmacen") ? $request-> input("codigoAlmacen") : 0;
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?, ?, ?,?);", [ $arrayParametros[0], $arrayParametros[1],$arrayParametros[2],$arrayParametros[3],$arrayParametros[4],$arrayParametros[5],$arrayParametros[6],$arrayParametros[7] ]);
			return $this->formatearRespuesta($responseSP,200,1);
			
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}
	/*
	 *Obtener gasto a cancelar
	*/
	public function obtGastosCancelar(Request $request)
	{
		try {
			$nombre_sp='GAS_obtDatosCancelar';
			$arrayParametros = array();
				
			//Obtener parametros del request
			$arrayParametros[0] = $request->has("idGasto") ? $request-> input("idGasto") : 0;
			$arrayParametros[1] = $request->has("idUsuario") ? $request-> input("idUsuario") : 0;
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?);", [ $arrayParametros[0], $arrayParametros[1] ]);
			return $this->formatearRespuesta($responseSP, 200,1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	public function repGastoCancelado(Request $request)
	{
		try {
			$nombre_sp='GAS_canGasto';
			$arrayParametros = array();

			//Obtener parametros del request
			$arrayParametros[0] = $request->has("idGasto") ? $request-> input("idGasto") : 0;
			$arrayParametros[1] = $request->has("idFolio") ? $request-> input("idFolio") : 0;
			$arrayParametros[2] = $request->has("idCliente") ? $request-> input("idCliente") : 0;
			$arrayParametros[3] = $request->has("idSucursal") ? $request-> input("idSucursal") : 0;
			$arrayParametros[4] = $request->has("folio") ? $request-> input("folio") : 0;
			$arrayParametros[5] = $request->has("tipoGasto") ? $request-> input("tipoGasto") : 0;
			$arrayParametros[6] = $request->has("folioGasto") ? $request-> input("folioGasto") : 0;
			$arrayParametros[7] = $request->has("codigoAlmacen") ? $request-> input("codigoAlmacen") : 0;
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?, ?, ?,?);", [ $arrayParametros[0], $arrayParametros[1],$arrayParametros[2],$arrayParametros[3],$arrayParametros[4],$arrayParametros[5],$arrayParametros[6],$arrayParametros[7] ]);
			return $this->formatearRespuesta($responseSP,200,1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}
}
