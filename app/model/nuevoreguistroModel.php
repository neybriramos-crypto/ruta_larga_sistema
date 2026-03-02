<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";
class Usuario
{
    private $email;
    private $password;
    private $conexion;

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    public function registrar()
    {
        $hash = password_hash($this->password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (Email, Contraseña) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ss", $this->email, $hash);
        return $stmt->execute();
    }
}