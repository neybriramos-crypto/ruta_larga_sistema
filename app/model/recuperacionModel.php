<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class Recuperacion extends Conexion {
    private $id, $email, $token;

    public function __construct() {
        parent::__construct();
        $this->conectar();
    }

    public function buscarUsuarioPorEmail($email) {
        $stmt = $this->conexion->prepare("SELECT ID FROM usuarios WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function actualizarToken($id, $codigo) {
        $stmt = $this->conexion->prepare("UPDATE usuarios SET token_recuperacion = ? WHERE ID = ?");
        $stmt->bind_param("si", $codigo, $id);
        return $stmt->execute();
    }
}