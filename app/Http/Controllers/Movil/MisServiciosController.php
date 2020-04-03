<?php

namespace App\Http\Controllers\Movil;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Generales;

class MisServiciosController extends Controller
{
	/**
	 * Retorna una lista de registros
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function servicios(Request $request)
	{
		$respuestaGeneral = new Generales();
		try {
			//$lista = DB::select('CALL CAT_ObtMisServiciosMovil(?, ?)', [$request->has("opc") ? $request->input("opc") : 10, $request->has("buscar") ? $request->input("buscar") : ""]);
			$lista = array();
			$lista = array(
				array('id' => 1, 'folio' => 12456789124, 'fecha' => '2019/02/15 09:54:05', 'direccion' => 'Av. Heroes Ferrocarrileros #1320, Guadalajara, Jal.', 'sucursal_id' => 1001, 'sucursal' => 'Matriz', 'cliente_id' => 1, 'cliente' => 'Sabritas', 'tipo_servicio' => 'Transporte', 'rubro' => 'Matriz', 'servicio' => 'Carga de mercancia'),
				array('id' => 2, 'folio' => 12456789125, 'fecha' => '2019/03/04 11:05:41', 'direccion' => 'Av. Heroes Ferrocarrileros #1320, Guadalajara, Jal.', 'sucursal_id' => 1002, 'sucursal' => 'Matriz', 'cliente_id' => 1, 'cliente' => 'Sabritas', 'tipo_servicio' => 'Transporte', 'rubro' => 'Matriz', 'servicio' => 'Carga de mercancia'),
				array('id' => 3, 'folio' => 12456789126, 'fecha' => '2019/04/09 16:33:12', 'direccion' => 'Av. Heroes Ferrocarrileros #1320, Guadalajara, Jal.', 'sucursal_id' => 1003, 'sucursal' => 'Matriz', 'cliente_id' => 1, 'cliente' => 'Sabritas', 'tipo_servicio' => 'Transporte', 'rubro' => 'Matriz', 'servicio' => 'Carga de mercancia'),
				array('id' => 4, 'folio' => 12456789127, 'fecha' => '2019/06/28 14:23:18', 'direccion' => 'Av. Heroes Ferrocarrileros #1320, Guadalajara, Jal.', 'sucursal_id' => 1004, 'sucursal' => 'Matriz', 'cliente_id' => 1, 'cliente' => 'Sabritas', 'tipo_servicio' => 'Transporte', 'rubro' => 'Matriz', 'servicio' => 'Carga de mercancia'),
				array('id' => 5, 'folio' => 12456789128, 'fecha' => '2019/07/24 10:06:46', 'direccion' => 'Av. Heroes Ferrocarrileros #1320, Guadalajara, Jal.', 'sucursal_id' => 1005, 'sucursal' => 'Matriz', 'cliente_id' => 1, 'cliente' => 'Sabritas', 'tipo_servicio' => 'Transporte', 'rubro' => 'Matriz', 'servicio' => 'Carga de mercancia')
			);
			return $respuestaGeneral->formatearRespuesta($lista, 200, 3);
		} catch (\Throwable $th) {
			throw $th;
			return $respuestaGeneral->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Retorna el detalle del registro seleccionado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function serviciosDetalle(Request $request)
	{
		$respuestaGeneral = new Generales();
		try {
			//$lista = DB::select('CALL CAT_ObtMisServiciosMovil(?, ?)', [$request->has("opc") ? $request->input("opc") : 10, $request->has("buscar") ? $request->input("buscar") : ""]);
			$lista = array();
			$lista = array(
				array('id' => 1, 'folio' => 12456789124, 'fecha' => '2019/02/15 09:54:05', 'direccion' => 'Av. Heroes Ferrocarrileros #1320, Guadalajara, Jal.', 'sucursal_id' => 1001, 'sucursal' => 'Matriz', 'cliente_id' => 1, 'cliente' => 'Sabritas', 'tipo_servicio' => 'Transporte', 'rubro' => 'Matriz', 'servicio' => 'Carga de mercancia'),
			);
			return $respuestaGeneral->formatearRespuesta($lista, 200, 3);
		} catch (\Throwable $th) {
			throw $th;
			return $respuestaGeneral->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

	/**
	 * Actualizar la ubicación de un Empleado
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveubicacion(Request $request)
	{
		try {
			$nombre_sp = 'TGP_insUbicacionATecnico';
			$arrayParametros = array();
			
			// Obtener los parametros del request
			$arrayParametros[0] = $request->has("id") ? $request->input("id") : 0; // id del empleado
			$arrayParametros[1] = $request->has("latitud") ? $request->input("latitud") : "";
			$arrayParametros[2] = $request->has("longitud") ? trim($request->input("longitud")) : "";

			// Validar datos faltantes
			if($arrayParametros[1] == "" || $arrayParametros[2] == "") {
				return $respuestaGeneral->formatearRespuesta([], 200, 2, 0, "Faltan datos obligatorios para actualizar la ubicación actual");
			}
			
			// Ejecutar SP Base de datos
			$responseSP = DB::select("CALL psm.".$nombre_sp."(?, ?, ?);", [$arrayParametros[0], $arrayParametros[1], $arrayParametros[2]]);
			
			return $respuestaGeneral->formatearRespuesta($responseSP, 200);
		} catch (\Throwable $th) {
			throw $th;
			return $respuestaGeneral->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}

}
