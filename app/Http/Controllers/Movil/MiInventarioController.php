<?php

namespace App\Http\Controllers\Movil;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Generales;

class MiInventarioController extends Controller
{
	/**
	 * Retorna una lista de registros
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function inventario(Request $request)
	{
		$respuestaGeneral = new Generales();
		try {
			//$lista = DB::select('CALL CAT_ObtMiInventarioMovil(?, ?)', [$request->has("opc") ? $request->input("opc") : 10, $request->has("buscar") ? $request->input("buscar") : ""]);
			$lista = array();
			$lista = array(array('codigo' => 1001, 'cantidad' => 150.50, 'concepto' => 'Definicion de conceptos.'),
			array('codigo' => 1002, 'cantidad' => 205, 'concepto' => 'Definicion de conceptos.'),
			array('codigo' => 1003, 'cantidad' => 302, 'concepto' => 'Definicion de conceptos.'),
			array('codigo' => 1004, 'cantidad' => 107.00, 'concepto' => 'Definicion de conceptos.'),
			array('codigo' => 1005, 'cantidad' => 94.50, 'concepto' => 'Definicion de conceptos.'));
			return $respuestaGeneral->formatearRespuesta($lista, 200, 3);
		} catch (\Throwable $th) {
			throw $th;
			return $respuestaGeneral->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
		}
	}
	
}
