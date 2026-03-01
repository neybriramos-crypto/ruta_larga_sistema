<?php
require_once dirname(__DIR__) . "/model/vehiculoModel.php";

class VehiculoPresenter
{
    public function manejarPeticiones()
    {
        $vehiculoObj = new Vehiculo();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $vehiculoObj->setPlaca($_POST['placa']);
            $vehiculoObj->setModelo($_POST['modelo']);
            $vehiculoObj->setMarca($_POST['marca']);

            if (isset($_POST['registrar'])) {
                $vehiculoObj->insertar();
                header("Location: " . $_SERVER['PHP_SELF'] . "?status=reg");
                exit();
            }

            if (isset($_POST['editar'])) {
                $vehiculoObj->setId($_POST['id_vehiculo_post']); // Usamos un nombre de campo claro
                $vehiculoObj->modificar();
                header("Location: " . $_SERVER['PHP_SELF'] . "?status=edit");
                exit();
            }
        }

        if (isset($_GET['delete'])) {
            $id_borrar = intval($_GET['delete']);
            if ($id_borrar > 0) {
                $vehiculoObj->eliminar($id_borrar);
                header("Location: " . $_SERVER['PHP_SELF'] . "?status=del");
                exit();
            }
        }

        // Obtener resultados para la tabla
        $result = $vehiculoObj->listar();

        return [
            'result' => $result
        ];
    }
}