<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class ReporteFlete extends Conexion
{
    private $filtro;
    public function __construct($filtro = 'todo')
    {
        parent::__construct();
        $this->conectar();
        $this->filtro = $filtro;
    }

    public function mostrar()
    {
        switch ($this->filtro) {
            case 'dia':
                $sql = "SELECT * FROM fletes WHERE fecha = CURDATE() ORDER BY fecha DESC";
                break;
            case 'semana':
                $sql = "SELECT * FROM fletes WHERE YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1) ORDER BY fecha DESC";
                break;
            case 'mes':
                $sql = "SELECT * FROM fletes WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE()) ORDER
BY fecha DESC";
                break;
            default:
                $sql = "SELECT * FROM fletes ORDER BY fecha DESC";
                break;
        }
        return $this->conexion->query($sql);
    }

    public function insertar($fecha, $origen, $destino, $valor)
    {
        $stmt = $this->conexion->prepare("INSERT INTO fletes (fecha, origen, destino, valor, cancelado) VALUES (?, ?, ?, ?,
0)");
        $stmt->bind_param("sssd", $fecha, $origen, $destino, $valor);
        return $stmt->execute();
    }

    public function actualizar($id, $fecha, $origen, $destino, $valor)
    {
        $stmt = $this->conexion->prepare("UPDATE fletes SET fecha=?, origen=?, destino=?, valor=? WHERE id=?");
        $stmt->bind_param("sssdi", $fecha, $origen, $destino, $valor, $id);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM fletes WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function cambiarCancelado($id, $valorActual)
    {
        $nuevoEstado = ($valorActual == 1) ? 0 : 1;
        $stmt = $this->conexion->prepare("UPDATE fletes SET cancelado = ? WHERE id = ?");
        $stmt->bind_param("ii", $nuevoEstado, $id);
        return $stmt->execute();
    }
}