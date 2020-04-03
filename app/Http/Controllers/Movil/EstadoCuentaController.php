<?php

namespace App\Http\Controllers\Movil;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Generales;

class EstadoCuentaController extends Controller
{

	/**
	 * Retorna una lista de registros
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function estadoCuenta(Request $request)
	{
		$respuestaGeneral = new Generales();
		try {
			//$lista = DB::select('CALL CAT_ObtEstadoCuentaMovil(?, ?)', [$request->has("opc") ? $request->input("opc") : 10, $request->has("buscar") ? $request->input("buscar") : ""]);
			$lista = array();
			$lista = array(array('numero' => 1001, 'total' => 150.50, 'estatus' => 1),
			array('numero' => 1002, 'total' => 205, 'estatus' => 1),
			array('numero' => 1003, 'total' => 302, 'estatus' => 1),
			array('numero' => 1004, 'total' => 107.00, 'estatus' => 0),
			array('numero' => 1005, 'total' => 94.50, 'estatus' => 1));
			return $respuestaGeneral->formatearRespuesta($lista, 200, 3);
		} catch (\Throwable $th) {
			throw $th;
			return $respuestaGeneral->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}
	
}
