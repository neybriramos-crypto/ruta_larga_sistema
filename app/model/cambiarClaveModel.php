<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class CambiarClave extends Conexion {
    public function __construct() {
        parent::__construct();
        $this->conectar();
    }

    public function actualizarPassword($id, $passwordHash) {
        // Actualizamos la clave y de paso limpiamos el token de recuperación
        $stmt = $this->conexion->prepare("UPDATE usuarios SET Contraseña = ?, token_recuperacion = NULL WHERE ID = ?");
        $stmt->bind_param("si", $passwordHash, $id);
        return $stmt->execute();
    }
}