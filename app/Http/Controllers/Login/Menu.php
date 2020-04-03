<?php 
namespace App\Http\Controllers\Login;
class Menu
{
    public $Id;
    public $Nombre;
    public $Submenus = array();
    
    public function __construct($Id,$Nombre,$Submenus)
    {
        $this ->Id = $Id;
        $this ->Nombre = $Nombre;
        $this ->Submenus = $Submenus;
    }
    
    public function getId()
    {
        return $this->Id;
    }

    public function getNombre()
    {
        return $this->Nombre;
    }

    public function getSubmenus()
    {
        return $this->Submenus;
    }

}

?>