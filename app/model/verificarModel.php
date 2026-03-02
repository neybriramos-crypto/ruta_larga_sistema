<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class Verificar extends Conexion {
    public function __construct() {
        parent::__construct();
        $this->conectar();
    }
}