<?php
namespace App\Http\Controllers\Login;

class Data {
    public $IdUsuario;
    public $NombreUsuario;
    public $Correo;
    public $Password;
    public $Rol;
    public $Menus = array();

    

    public function __construct($IdUsuario, $NombreUsuario, $Correo, $Password,$Rol ,$Menus)
    {
        $this ->IdUsuario = $IdUsuario;
        $this ->NombreUsuario = $NombreUsuario;
        $this ->Correo = $Correo;
        $this ->Password = $Password;
        $this ->Rol = $Rol;
        $this ->Menus = $Menus;

    }
/*
    public function setData($IdUsuario, $NombreUsuario, $Correo, $Password,$Rol ,$Menus)
    {
        $this ->IdUsuario = $IdUsuario;
        $this ->NombreUsuario = $NombreUsuario;
        $this ->Correo = $Correo;
        $this ->Password = $Password;
        $this ->Rol = $Rol;
        $this ->Menus = $Menus;

    }*/

}
?>