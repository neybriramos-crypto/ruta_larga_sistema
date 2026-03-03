<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class Vehiculo extends Conexion
{
    private $id, $placa, $modelo, $marca;

    public function __construct()
    {
        parent::__construct();
        $this->conectar();
    }

    public function setId($v)
    {
        $this->id = intval($v);
    }
    public function setPlaca($v)
    {
        $this->placa = strtoupper(substr(trim($v), 0, 15));
    }
    public function setModelo($v)
    {
        $this->modelo = substr(trim($v), 0, 50);
    }
    public function setMarca($v)
    {
        $this->marca = substr(trim($v), 0, 50);
    }

    public function listar()
    {
        // Usamos id_vehiculo en minúsculas como en tu SQL
        return $this->conexion->query("SELECT * FROM vehiculos ORDER BY id_vehiculo DESC");
    }

    public function insertar()
    {
        // Se añade cliente_id con valor 0 por defecto para evitar error de NOT NULL
        $stmt = $this->conexion->prepare("INSERT INTO vehiculos (placa, modelo, marca, cliente_id) VALUES (?, ?, ?, 0)");
        $stmt->bind_param("sss", $this->placa, $this->modelo, $this->marca);
        return $stmt->execute();
    }

    public function modificar()
    {
        // Corregido: id_vehiculo en minúsculas
        $stmt = $this->conexion->prepare("UPDATE vehiculos SET placa=?, modelo=?, marca=? WHERE id_vehiculo=?");
        $stmt->bind_param("sssi", $this->placa, $this->modelo, $this->marca, $this->id);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        // Corregido: id_vehiculo en minúsculas
        $stmt = $this->conexion->prepare("DELETE FROM vehiculos WHERE id_vehiculo = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}