<?php 
namespace App\Http\Controllers\Login;
class Submenu
{
    public $Id;
    public $Nombre;

    public function __construct($Id, $Nombre)
    {
        $this->Id = $Id;
        $this->Nombre = $Nombre;
    }

    public function getId()
    {
        return $this->Id;
    }

    public function getNombre()
    {
        return $this->Nombre;
    }
}

?>