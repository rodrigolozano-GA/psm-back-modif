<?php

namespace App\Http\Controllers\Login;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function hashEnc(Request $request)
    {
         try
        {   
            $idUser = $request->has('Id') ? $request->input('Id') : '';
            $correo = $request->has('Correo') ? $request->input('Correo') : '';
            $pwd  = $request->has('Pwd') ? $request->input('Pwd') : '' ;
            $nombre_sp = 'USU_insCotrasenaHash';
            $cifrado =''; 
            if(strlen($correo) == 0 || strlen($pwd) == 0)
            {
                return $this->formatearRespuesta("",500,2,0,"Error: usuario y password obligatorios");			
            }
            else
            {
                $cifrado = password_hash(($correo.$pwd),PASSWORD_DEFAULT,array("cost"=>10));
                $response = DB::select("CALL ".$nombre_sp."(?,?);", [$correo,$cifrado]);
                return $this->formatearRespuesta($response, 200, 1);
            }

        }catch (\Throwable $th)
        {
        throw $th;
        return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
        }
        /*
        try
        {
            $idUser = $request->has('id') ? $request->input('Id') : '';
            $correo = $request->has('Correo') ? $request->input('Correo') : '';
            $pwd  = $request->has('Pwd') ? $request->input('Pwd') : '' ;
            $nombre_sp = 'USU_insCotrasenaHash';
            $cifrado =''; 
                      
            if(strlen($correo) == 0 || strlen($pwd) == 0)
            {
                return $this->formatearRespuesta("",500,2,0,"Error: usuario y password obligatorios");			
            }
            else
            {
                $cifrado = password_hash(($correo.$pwd),PASSWORD_DEFAULT,array("cost"=>10));
                $response = DB::select("CALL ".$nombre_sp."(?,?);", [$idUser,$cifrado]);
                return $this->formatearRespuesta($response, 200, 1);
            }
        }catch (\Throwable $th)
        {
        throw $th;
        return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");
        }*/
    }


    // Function to validate mail
    public function validateUser(Request $request)
    {
        try
        {  
            $nombre_sp = 'SEG_valUsuario';
            $arrayParametros = array();
            $arrayParametros[0] = $request ->has('Correo') ? $request->input('Correo') : null ;
            $lista = DB::select('CALL '.$nombre_sp.'(?);', [$arrayParametros[0]]);

            return $this->formatearRespuesta($lista, 200, 1);
        
        }catch (\Throwable $th)
        {
            throw $th;
            return $this->formatearRespuesta([], 500, 2, 0, "Error al procesar la solicitud");

        }

    }

    public function validatePwd(Request $request)
    {
        try
        {
            $array = array();
            $nombre_sp = 'SEG_obtUsuarioSession';
            $correo = $request -> has('Correo') ? $request->input('Correo') : '' ;
            $pwd = $request -> has('Pwd') ? $request->input('Pwd'): '';

            if(strlen($pwd) != 0 && strlen($correo)!=0)
            {
                $cifrado = password_hash(($correo.$pwd),PASSWORD_DEFAULT,array("cost"=>12));
                $responseSP = DB::select("CALL ".$nombre_sp."(?);",[$correo]);

                if(strlen($responseSP[0]->passwordhash) !=0)
                {
                    if(password_verify(($correo.$pwd),$responseSP[0]->passwordhash))
                    {
                        $responseSP[0]->passwordhash = '';
                        return $this->formatearRespuesta($responseSP, 200, 1);
                    }
                    else
                    {
                        return $this->formatearRespuesta([], 200, 1,0,'');
                    }
                }
                return $responseSP[0]->passwordhash; 
            }
        }catch (\Throwable $th)
        {
            throw $th;
            return $this->formatearRespuesta("Ocurrío un error", 500, 2,0);
        }
    }

    //Function to get menus
     public function obtMenus(Request $request)
     {
        $nombre_sp = 'SEG_obtMenusXUsuario';
        $submenus_sp = 'SEG_obtSubMenusXUsuario';
        
        $arrayParametros = array();
        $arrayParametros[0] = $request ->has('id') ? $request->input('id') : 0 ;
        $arrayParametros[1] = $request ->has('nombre') ? $request->input('nombre') : '';
        $responseSP = DB::select('CALL '.$nombre_sp.'(?);', [$arrayParametros[0]]);
        
        $menus = array();
        $datos = array();

        //Obtener Los menus
        foreach($responseSP as $index => $obj)
        {
            $menu[$index] = $obj;
            $submenus = DB::select('CALL '.$submenus_sp.'(?,?);', [$arrayParametros[0],$obj->id]);
            $array_submenus = array();
            foreach($submenus as $key => $val)
            {
                $array_submenus [$key] = $val;
            }
            $menus[$index] = new Menu($obj->id,$obj->nombre,$array_submenus);
        }
        return $this->formatearRespuesta((new Data($arrayParametros[0],$arrayParametros[1],null,null,1,$menus) ), 200, 1);
    
    }
  
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
		}
		return response()->json($info, 
		$codeHTTP, 
		['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], 
		JSON_UNESCAPED_UNICODE);
	}
}
