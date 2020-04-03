<?php

namespace App\Http\Controllers\Sistema;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Catalogos\TiposServiciosController;

use Zipper;
use File;
use ZipArchive;

class MesaControlController extends Controller
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
			case 3: $info = ["SUCCESS" => $respuestaSP[0]->SUCCESS, "MESSAGE" => $respuestaSP[0]->MESSAGE, "ID" => $respuestaSP[0]->ID, "FOLIO" => $respuestaSP[0]->FOLIO]; break;
			case 4: $info = ["SUCCESS" => 1, "MESSAGE" => "guardado éxito - ".$mensaje, "ID" => 1, "FOLIO" => "123456789"]; break;
		
		}
		return response()->json($info,
		$codeHTTP,
		['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
		JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Obtener al coordinador de una sucursal
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function coordinador(Request $request)
	{
		try {
			//COR_obrCordinadorXSucursal
			$lista = DB::select('CALL COR_obrCordinadorXSucursal(?, ?)', [
				$request->has("sucursal_id") ? $request->input("sucursal_id") : 0,
				$request->has("id") ? $request->input("id") : 0,
			]);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener servicios de una sucursal
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function servicios(Request $request)
	{
		try {
			//TSE_obtTipoServicioXCoordinador
			$lista = DB::select('CALL TSE_obtTiposServiciosSucursal(?)', [
					$request->has("sucursal_id") ? $request->input("sucursal_id") : 0
				]
			);
			return $this->formatearRespuesta($lista, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener Folios de una sucursal
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtFolioSucursal(Request $request)
	{
		try {
			//TSE_obtTipoServicioXCoordinador
			$lista = DB::select('CALL FOL_obtFoliosXSucursal(?)', [
					$request->has("sucursal_id") ? $request->input("sucursal_id") : 0
				]
			);
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
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Insertar un nuevo folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function nuevofolio(Request $request)
	{
		try {
			$nombre_sp = 'FOL_insFolio2';
			$arrayParametros = array();
			
			// Obtener Archivo
			$archivoFolio = $request->file('archivoFolio');
			
			// Validar extensión del archivo
			if(strtolower($archivoFolio->getClientOriginalExtension())!='pdf' && strtolower($archivoFolio->getClientOriginalExtension())!='msg' 
			&& strtolower($archivoFolio->getClientOriginalExtension())!='xls' && strtolower($archivoFolio->getClientOriginalExtension())!='xlsx' 
			&& strtolower($archivoFolio->getClientOriginalExtension())!='doc' && strtolower($archivoFolio->getClientOriginalExtension())!='docx')
			{
				return $this->formatearRespuesta([], 200, 2, 0, "El documento debe ser del tipo: excel, word, pdf o correo (.msg)");
			}

			/* // Se comenta Codigo para pruebas
			$filename = 'MESA_CONTROL/1234/'.$archivoFolio->getClientOriginalName();
			Storage::disk('psmfiles')->put($filename, file_get_contents($archivoFolio));

			return $this->formatearRespuesta([], 200, 4, 1, $filename);*/

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("sucursal_id") ? $request->input("sucursal_id") : 0;
			$arrayParametros[1] = $request->has("tiposervicio_id") ? $request->input("tiposervicio_id") : 0;
			$arrayParametros[2] = $request->has("usuario_id") ? $request->input("usuario_id") : 1;
			$arrayParametros[3] = $request->has("descripcion") ? trim($request->input("descripcion")) : "";
			$arrayParametros[4] = $request->has("observaciones") ? trim($request->input("observaciones")) : "";
			$arrayParametros[5] = $request->has("coordinador_id") ? $request->input("coordinador_id") : 0;
			$arrayParametros[6] = $request->has("fecha_programada_folio") ? $request->input("fecha_programada_folio") : "";
			$arrayParametros[7] = $request->has("medio_id") ? $request->input("medio_id") : 0;
			$arrayParametros[8] = $request->has("ot") ? trim($request->input("ot")) : "";
			$arrayParametros[9] = $request->has("ticket") ? trim($request->input("ticket")) : "";
			$arrayParametros[10] = $request->has("folio") ? trim($request->input("folio")) : "";
		
			if((int)$arrayParametros[0] == 0 && (int)$arrayParametros[2] == 0 && $arrayParametros[3] == '' && $arrayParametros[4] == '' && (int)$arrayParametros[5] == 0 && $arrayParametros[6] == '' && (int)$arrayParametros[7] == 0)
			{
				//|| (int)$arrayParametros[1] == 0 || (int)$arrayParametros[2] == 0 || (int)$arrayParametros[3] == '' || $arrayParametros[4] == ''|| (int)$arrayParametros[5] == 0 || $arrayParametros[6] == '' || (int)$arrayParametros[7] == 0
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
				$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3],
				$arrayParametros[4], $arrayParametros[5], $arrayParametros[6], $arrayParametros[7],
				$arrayParametros[8], $arrayParametros[9], $arrayParametros[10]
			]);
			// Validar si se ha guardado el Folio
			if((int)$responseSP[0]->SUCCESS == 1) {
				// Guardar Archivo en Base de Datos
				$filename = 'MESA_CONTROL/'.$responseSP[0]->FOLIO.'/'.$archivoFolio->getClientOriginalName();

				$arrayParametrosArchivo[0] = 0;
				$arrayParametrosArchivo[1] = $responseSP[0]->ID;
				$arrayParametrosArchivo[2] = $filename;
				$arrayParametrosArchivo[3] = $request->has("usuario_id") ? $request->input("usuario_id") : 1;

				$responseSPArchivo = DB::select("CALL psm.FOD_insDocumentoAFolio(?, ?, ?, ?)", [
					$arrayParametrosArchivo[0], $arrayParametrosArchivo[1], $arrayParametrosArchivo[2], $arrayParametrosArchivo[3]
				]);
				// Si se guardo en base de datos, guardar el documento en disco
				if((int)$responseSPArchivo[0]->SUCCESS == 1) {
					$responseSP[0]->MESSAGE = $responseSP[0]->MESSAGE.'. Se ha guardado el documento';
				Storage::disk('psmfiles')->put($filename, file_get_contents($archivoFolio)); 
				//Storage::disk('local')->put($filename, file_get_contents($archivoFolio));
				} else {
					$responseSP[0]->MESSAGE = $responseSP[0]->MESSAGE.'. No se pudo guardar el documento';
				}
			}
			// Respuesta EndPoint
			return $this->formatearRespuesta($responseSP, 200, 3);

		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar los Productos/Servicios de un folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
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

			// Validar datos faltantes
			if($arrayParametros[2] == 0) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3], $arrayParametros[4], $arrayParametros[5]]);

			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener todos los folios
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtFolios(Request $request)
	{    
		try {
			$nombre_sp = 'FOL_obtFolios';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("opc") ? $request->input("opc") : 1;
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);
             
			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener Productos/Servicios por Folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtProductosFolio(Request $request)
	{
		try {
			$nombre_sp = 'FPR_obtProductosXFolio';
			$arrayParametros = array();

			// Obtener los parametros del request

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?);", [$request->input("id")]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener los Técnicos por Folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtTecnicosFolio(Request $request)
	{
		try {
			$nombre_sp = 'FTE_obtTecnicosXFolio';
			$arrayParametros = array();

			// Obtener los parametros del request

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?);", [$request->input("id")]);
			
			return $this->formatearRespuesta($responseSP, 200, 1);

		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener los Técnicos por Zona
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtTecnicosZonaFolio(Request $request)
	{
		try {
			$nombre_sp = 'TEC_obtTecnicosXZona';
			$arrayParametros = array();

			// Obtener los parametros del request

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ? , ?);", [$request->input("zona_id"), "", 1]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar los Técnicos de un folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveTecnicosFolio(Request $request)
	{
		try {
			$nombre_sp = 'FTE_insTecnicosAFolio';
			$arrayParametros = array();
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("folio_id") ? $request->input("folio_id") : 0;
			$arrayParametros[1] = $request->has("id") ? $request->input("id") : 0;
			$arrayParametros[2] = $request->has("tipo") ? $request->input("tipo") : 0;
			$arrayParametros[3] = $request->has("accion") ? $request->input("accion") : 0;
			$arrayParametros[4] = $request->has("usuario_id") ? $request->input("usuario_id") : 1;
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
	 * Obtener los Tipos de Documentos Catálogos
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtTiposDocumentos(Request $request)
	{
		try {
			$nombre_sp = 'CAT_obtTipos_DocumentosG';
			$arrayParametros = array();

			// Obtener los parametros del request

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?);", [10]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener los Documentos por Folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtDocumentosFolio(Request $request)
	{
		try {
			$nombre_sp = 'FOD_obtDocumentosXFolios';
			$arrayParametros = array();

			// Obtener los parametros del request

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?);", [$request->input("id")]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar documento para un folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveDocumentofolio(Request $request)
	{
		try {
			$arrayParametrosArchivo = array();
			$filename = "";

			// Obtener Archivo
			$archivoFolio = $request->file('archivoFolio');

			// Validar extensión del archivo
			if(strtolower($archivoFolio->getClientOriginalExtension()) != "pdf" && strtolower($archivoFolio->getClientOriginalExtension()) != "msg" && strtolower($archivoFolio->getClientOriginalExtension()) != "xls" && strtolower($archivoFolio->getClientOriginalExtension()) != "xlsx" && strtolower($archivoFolio->getClientOriginalExtension()) != "doc" && strtolower($archivoFolio->getClientOriginalExtension()) != "docx") {
				return $this->formatearRespuesta([], 200, 2, 0, "El documento debe ser del tipo: excel, word, pdf o correo (.msg)");
			}

			/* // Se comenta Codigo para pruebas
			$filename = 'MESA_CONTROL/1234/'.$archivoFolio->getClientOriginalName();
			Storage::disk('psmfiles')->put($filename, file_get_contents($archivoFolio));

			return $this->formatearRespuesta([], 200, 4, 1, $filename);*/

			$filename = 'MESA_CONTROL/'.$request->input('folio').'/'.$archivoFolio->getClientOriginalName();
			// Obtener los parametros del request
			$arrayParametrosArchivo[0] = $request->has('tipoDocumento_id') ? $request->input('tipoDocumento_id') : 0;
			$arrayParametrosArchivo[1] = $request->has('folio_id') ? $request->input('folio_id') : 0;
			$arrayParametrosArchivo[2] = $filename;
			$arrayParametrosArchivo[3] = $request->has("usuario_id") ? $request->input("usuario_id") : 1;
			//$arrayParametros[10] = $request->has("tipoequipo_id") ? $request->input("tipoequipo_id") : 0;

			// Validar datos faltantes
			if( $arrayParametrosArchivo[0] == 0 || $arrayParametrosArchivo[1] == 0 || $arrayParametrosArchivo[2] == "" ) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			// Ejecutar SP Base de datos
			// Guardar Archivo en Base de Datos
			$responseSPArchivo = DB::select("CALL psm.FOD_insDocumentoAFolio(?, ?, ?, ?)", [
				$arrayParametrosArchivo[0], $arrayParametrosArchivo[1], $arrayParametrosArchivo[2], $arrayParametrosArchivo[3]
			]);

			$pathFile = str_replace('/', '\\', $filename);

			// Validar si se ha guardado el Folio
			if((int)$responseSPArchivo[0]->SUCCESS == 1) {
				if(Storage::disk('psmfiles')->exists($pathFile)) {
					Storage::delete($filename);
				}
				Storage::disk('psmfiles')->put($filename, file_get_contents($archivoFolio));
			}

			// Respuesta EndPoint
			return $this->formatearRespuesta($responseSPArchivo, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener detalle de un Técnico asignado a un Folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtDetalleTecnico(Request $request)
	{
		try {
			$nombre_sp = 'TEC_obtInformacionG';
			$arrayParametros = array();

			// Obtener los parametros del request

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?);", [$request->input("id")]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener inventario de un Técnico asignado a un Folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtDetalleInventarioTecnico(Request $request)
	{
		try {
			$nombre_sp = 'TEC_obtInventarioTecnico';
			$arrayParametros = array();

			$responseSP = DB::select("CALL psm.".$nombre_sp."(?);", [$request->input("idInventario")]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener refacciones de un Técnico asignadas a un Folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtDetalleRefaccionesTecnico(Request $request)
	{  
		try {
			$nombre_sp =  'FTP_obtRefaccionesFolioXTecnicos';
			$arrayParametros = array();
		
			//LLAMAR POR CADA EMPLEADO  N LISTA DE SERVICIOS
			// retorna id(relacion) - codigo - concepto - cantidad
			//$nombre_sp = 'TEC_obtRefaccionesTecnico';
			

			// Obtener los parametros del request
			// Ejecutar SP Base de datos
			//$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?);", [ $request->input("id"), $request->input("folio") ]);
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?);", [ $request->input("id"),$request->input("folio")]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * End Point para obtener el estado de cuenta de un técnico.
	 * 
	 */
	public function obtEdoCuenta(Request $request)
	{
		try{
			$nombre_sp ='TEC_obtEdoCuentaTecnico';
			$arrayParametros = array();
			$arrayParametros[0] = $request->has("idEdoCuenta") ? $request->input("idEdoCuenta") : 0; // id Empleado
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL ".$nombre_sp."(?);", [$arrayParametros[0]]);
			return $this->formatearRespuesta($responseSP, 200, 1);

		}catch(\Throwable $th){
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0,"Error al procesar la solicitud");
		}
		/*
		try {
		
			$nombre_sp = 'TEC_obtEdoCuentaTecnico';
			$responseSP = DB::stream_select("CALL psm.".$nombre_sp."(?)",[$request->input("idEdoCuenta")]);
			return $this->formatearRespuesta($responseSP,500,2,0);
		
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([],500,2,0,"Error al procesar la solicitud");
		}*/
	}


	/**
	 * Obtener refacciones de un Técnico para asignar a un Folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtRefaccionesTecnico(Request $request)
	{
		try {
			
			$nombre_sp = 'TEC_obtRefaccionesTecnico'; 
			$arrayParametros = array();

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?);", [$request->input("id")]); // id_ztt del empleado

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Insertar las refacciones de un Técnico asignado a un Folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function insDetalleRefaccionesTecnico(Request $request)
	{
		try {
			// FTP_insRefaccionesFoliosXTecnico(
			// - p_idFolios 
			// - p_idEmpleados 
			// - p_idProducto(el id que te retorno en TEC_obtRefaccionesTecnico) 
			// - p_cantidad 
			// - p_idUsuario)
			$nombre_sp = 'FTP_insRefaccionesFoliosXTecnico';
			$arrayParametros = array();
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?);", [
				$request->input("folio_id"), $request->input("empleado"), $request->input("refaccion"), 
				$request->input("cantidad"), $request->input("usuario_id")
			]);
			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Elimina las Refacciones / Servicios asignados a un Folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function eliRefaServsFolioTecnico(Request $request)
	{
		try {
			// FOL_eliProductosRefaccionesAFolios
			// - p_opcion (1 eliminar producto a folio, 2 o mas eliminar refaccionde tecnico a folio ) 
			// - p_idRelacion
			// retorna SUCCESS - MESSAGE
			$nombre_sp = 'FOL_eliProductosRefaccionesAFolios';
			$arrayParametros = array();

			// Obtener los parametros del request

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?);", [
				$request->input("opc"), $request->input("id")
			]);

			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener detalle de una Sucursal asignada a un Folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtDetalleSucursal(Request $request)
	{
		try {
			$nombre_sp = 'CAT_obtSucursalG';
			$arrayParametros = array();

			// Obtener los parametros del request

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?);", [$request->input("id")]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Validar existencia del documento a descargar de un folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function validarDocumentoFolio(Request $request)
	{
		try {
			$pathFile = str_replace('/', '\\', $request->input("nombre"));

			if(Storage::disk('psmfiles')->exists($pathFile)) {
				return $this->formatearRespuesta([], 200, 2, 1, "");
			} else {
				return $this->formatearRespuesta([], 200, 2, 0, "No se encontró el documento");
			}
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}
	/**
	 * Obtener documento a descargar de un folio
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function downDocumentoFolio(Request $request)
	{
		try {
			$pathFileServer = Storage::disk('psmfiles')->getDriver()->getAdapter()->getPathPrefix();
			$pathFileServer = str_replace('\\', "/", $pathFileServer);
			$pathFile = $pathFileServer.$request->input("nombre"); // Full path
			$pathFile2 = str_replace('/', '\\', $request->input("nombre"));

			if(Storage::disk('psmfiles')->exists($pathFile2))
			{   
				$file=Storage::disk('psmfiles')->get($pathFile2);
 				return (new Response($file, 200));
				//return response()->download($pathFile);
			} 
			else 
			{ 
				return $this->formatearRespuesta([], 200, 2, 0, $pathFileServer." No se encontró el documento");
			}
			
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/** Comprimir y descargar archivos ZIP */
	public function downloadZip(Request $request)
	{  
		//Nombre del archivo zip a generar	
		$zipcreated = time().".zip"; 
		//Ruta de la carpeta con los archivos a comprimir
		$files = glob(('C:/ARCHIVOSPSM/MESA_CONTROL/'.$request->input("fol").'/*'));
		//Agregar todos los archivos al .zip creado
        Zipper::make(('C:/ARCHIVOSPSM/MESA_CONTROL/ZIP/'.$zipcreated))->add($files)->close();
	
		//**********PARA DESCARGA********************/
		$base = "MESA_CONTROL/ZIP/";
		$pathFileServer = Storage::disk('psmfiles')->getDriver()->getAdapter()->getPathPrefix();
		$pathFileServer = str_replace('\\', "/", $pathFileServer);
		$pathFile = $pathFileServer.$base."/".$request->input("fol")."/".$zipcreated; 
		$pathFile2 = str_replace('/', '\\', $base."/".$zipcreated);
		
		if(Storage::disk('psmfiles')->exists($pathFile2))
		{
			$file=Storage::disk('psmfiles')->get($pathFile2);
			File::delete(File::glob('C:/ARCHIVOSPSM/MESA_CONTROL/ZIP/*'));
			return (new Response($file,200));
		}
		else
		{
		 return $this->formatearRespuesta([], 200, 2, 0, " No se encontró el documento");
		}
	}
	

	/**
	 * Guardar el motivo de un estatus para un folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveMotivoEstatusFolio(Request $request)
	{
		try {
			$nombre_sp = 'MOT_insMotivosAServicio';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("motivo_id") ? $request->input("motivo_id") : 0;
			$arrayParametros[1] = $request->has("id") ? $request->input("id") : 0; // id del folio
			$arrayParametros[2] = $request->has("usuario_id") ? $request->input("usuario_id") : 1;
			$arrayParametros[3] = 1; // opcion 1 = motivos para folios

			// Validar datos faltantes
			if( $arrayParametros[0] == 0 || $arrayParametros[2] == 0 ) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3]]);

			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener los motivos de estatus de un Folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtMotivoEstatusFolio(Request $request)
	{
		try {
			$nombre_sp = 'CAT_ObtMotivosG';
			$arrayParametros = array();

			// Obtener los parametros del request

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?);", [
				11, "", $request->input("estatus_id")
			]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar la información general de un folio
	 * fecha_programada
	 * ot
	 * ticket
	 * estatus
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveInfoGeneralFolio(Request $request)
	{		
		try {
			$nombre_sp = 'FOL_modFolios';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del folio
			$arrayParametros[1] = $request->has("fecha_programada_value") ? $request->input("fecha_programada_value") : "";
			$arrayParametros[2] = $request->has("ot") ? trim($request->input("ot")) : "";
			$arrayParametros[3] = $request->has("ticket") ? trim($request->input("ticket")) : "";
			$arrayParametros[4] = $request->has("estatus") ? $request->input("estatus") : 0;
			$arrayParametros[5] = $request->has("usuario_id") ? $request->input("usuario_id") : 1;
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?, ?);", [
				$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3], $arrayParametros[4], $arrayParametros[5]
			]);

			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Guardar Actualizaciones de un folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveActualizacioFolio(Request $request)
	{
		try {
			$nombre_sp = 'FAC_insActualizacionAFolio';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del folio
			$arrayParametros[1] = $request->has("usuario_id") ? $request->input("usuario_id") : 1;
			$arrayParametros[2] = $request->has("descripcion") ? trim($request->input("descripcion")) : "";

			// Validar datos faltantes
			if( $arrayParametros[1] == "" ) {
				return $this->formatearRespuesta([], 200, 2, 0, "Ingrese los datos faltantes");
			}

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2]]);

			return $this->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener Actualizaciones de un folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtActualizacioesFolio(Request $request)
	{
		try {
			$nombre_sp = 'FAC_obtActualizacionXFolio';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del folio
			$arrayParametros[1] = $request->has("usuario_id") ? $request->input("usuario_id") : 1;
			$arrayParametros[2] = 1;

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2]]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener el número de Actualizaciones de un folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtNumActualizacioesFolio(Request $request)
	{
		try {
			$nombre_sp = 'FAC_obtActualizacionXFolio';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del folio
			$arrayParametros[1] = $request->has("usuario_id") ? $request->input("usuario_id") : 1;
			$arrayParametros[2] = 2;

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2]]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener las Actividades de un Folio
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtActividades(Request $request)
	{
		try {
			$nombre_sp = 'FOA_obtActividadesXFolio';
			$arrayParametros = array();

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del folio
			//$arrayParametros[1] = $request->has("usuario_id") ? $request->input("usuario_id") : 1;

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?);", [$arrayParametros[0]]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Obtener Actas de un Técnico (Empleado) seleccionado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function obtActasTecnico(Request $request)
	{
		try {
			$nombre_sp = 'AEM_obtActasXTecnico';
			$arrayParametros = array();
			//(idTecnico, idFormatoActa) retorna id_acta - folio_actas

			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("tecnico_id") ? $request->input("tecnico_id") : 0; // id del empleado
			$arrayParametros[1] = $request->has("formato_id") ? $request->input("formato_id") : 0;

			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?);", [$arrayParametros[0], $arrayParametros[1]]);

			return $this->formatearRespuesta($responseSP, 200, 1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
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

	public function saveServices(Request $request)
	{
		try {
			$nombre_sp = 'FOL_insFolio2';
			$arrayParametros = array();
			$arrayParametros[0] = $request->has("sucursal_id") ? $request->input("sucursal_id") : 0;
			$arrayParametros[1] = $request->has("tiposervicio_id") ? $request->input("tiposervicio_id") : 0;
			$arrayParametros[2] = $request->has("usuario_id") ? $request->input("usuario_id") : 1;
			$arrayParametros[3] = $request->has("descripcion") ? trim($request->input("descripcion")) : "";
			$arrayParametros[4] = $request->has("observaciones") ? trim($request->input("observaciones")) : "";
			$arrayParametros[5] = $request->has("coordinador_id") ? $request->input("coordinador_id") : 0;
			$arrayParametros[6] = $request->has("fecha_programada_folio") ? $request->input("fecha_programada_folio") : "";
			$arrayParametros[7] = $request->has("medio_id") ? $request->input("medio_id") : 0;
			$arrayParametros[8] = $request->has("ot") ? trim($request->input("ot")) : "";
			$arrayParametros[9] = $request->has("ticket") ? trim($request->input("ticket")) : "";
			$info = Array();

			$medios = DB::select('CALL CAT_ObtMediosG(?, ?)', [10, ""]);
		
			$info['DATA'] = ["SUCCESS" => $medios[0]->DATA];
			return $this->formatearRespuesta($info['DATA'], 200, 1);
			
			foreach($medios as $key =>$row) {
				$encontrado[$key] = $row['DATA'];
			}
		
			return $this->formatearRespuesta($encontrado, 200, 1);     
			
			//return $this->formatearRespuesta($medios, 200, 1);
			

			//return( $this->formatearRespuesta(app('App\Http\Controllers\Catalogos\TiposServiciosController')->show(),200,1));
			//return $this->formatearRespuesta(implode(",",$arrayParametros),200,1); Catalogos\TiposServiciosController@show
			
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([],500,2,0,"Error al procesar la solicitud");
		}
	}


	public function saveFolio(Request $request)
	{
		$nombre_sp = 'porDefinir';
		$usuario_id = $request->has("usuario_id") ? $request->input("usuario_id") : 8;

		$arrayParametros = array();
		$arrayFails = array();

		$countFails = 0;
		foreach($request['datos'] as $index =>$row) 
		{
			$con = 0;
			foreach($row as $indice => $value)
			{
				$arrayParametros[$con] = $value;
				$con++;
			}
			$countFails = 10; //comentar
			
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
				$arrayParametros[0], $arrayParametros[1], $arrayParametros[2], $arrayParametros[3],
				$arrayParametros[4], $arrayParametros[5], $arrayParametros[6], $arrayParametros[7],
				$arrayParametros[8], $usuario_id
			]);

			if((int)$responseSP[0]->SUCCESS != 1)
			{
				$arrayFails[$countFails] = $arrayParametros;
				$countFails++;
			} 
		}
		if($countFails>0)
		{
			//return $this->formatearRespuesta($arrayFails, 200, 1);
			return $this->formatearRespuesta($request['datos'], 200, 1);      
		}
		else
		{
			return $this->formatearRespuesta("OK", 200, 1);     
		}
		return $countFails;
	}

	//Contador Folios
	public function obtContFol(Request $request)
	{
		try {
			$nombre_sp = 'FOL_obtContadoresFolios ';
			$responseSP = DB::select("CALL psm.".$nombre_sp."()", []);
			return $this->formatearRespuesta($responseSP,200,1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([],500,2,0,"Error al procesar la solicitud");
		}

	}

	//Contador de cotizaciones
	public function obtContCot(Request $request)
	{
		try {
			$nombre_sp = 'COT_obtContadoresCotizaciones ';
			$responseSP = DB::select("CALL psm.".$nombre_sp."()", []);
			return $this->formatearRespuesta($responseSP,200,1);
		} catch (\Throwable $th) {
			throw $th;
			return $this->formatearRespuesta([],500,2,0,"Error al procesar la solicitud");
		}

	}

}
