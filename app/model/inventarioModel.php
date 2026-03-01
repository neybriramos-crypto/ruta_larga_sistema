<?php

require_once dirname(__DIR__) . "/config/claseconexion.php";
class Inventario extends Conexion
{
    public function __construct()
    {
        parent::__construct();
        $this->conectar();
    }

    public function listar()
    {
        return $this->conexion->query("SELECT * FROM inventario ORDER BY id_producto DESC");
    }

    public function insertar($cod, $nom, $des, $can, $pre)
    {
        $stmt = $this->conexion->prepare("INSERT INTO inventario (codigo, nombre, descripcion, cantidad, precio_unidad, fecha_actualizacion) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssid", $cod, $nom, $des, $can, $pre);
        return $stmt->execute();
    }

    public function modificar($id, $cod, $nom, $des, $can, $pre)
    {
        $stmt = $this->conexion->prepare("UPDATE inventario SET codigo=?, nombre=?, descripcion=?, cantidad=?, precio_unidad=?, fecha_actualizacion=NOW() WHERE id_producto=?");
        $stmt->bind_param("sssidi", $cod, $nom, $des, $can, $pre, $id);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM inventario WHERE id_producto = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}