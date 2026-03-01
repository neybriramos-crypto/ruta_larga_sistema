<?php
require_once dirname(__DIR__) . "/model/inventarioModel.php";

class InventarioPresenter
{
    public function manejarPeticiones()
    {
        $objInv = new Inventario();

        if (isset($_POST['registrar'])) {
            $objInv->insertar($_POST['codigo'], $_POST['nombre'], $_POST['descripcion'], $_POST['cantidad'], $_POST['precio_unidad']);
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=reg");
            exit();
        }

        if (isset($_POST['editar'])) {
            $objInv->modificar($_POST['id_producto'], $_POST['codigo'], $_POST['nombre'], $_POST['descripcion'], $_POST['cantidad'], $_POST['precio_unidad']);
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=edit");
            exit();
        }

        if (isset($_GET['delete'])) {
            $objInv->eliminar(intval($_GET['delete']));
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=del");
            exit();
        }

        $result = $objInv->listar();

        return [
            'result' => $result
        ];
    }
}
