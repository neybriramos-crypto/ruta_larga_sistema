<?php
require_once dirname(__DIR__) . "/model/fleteModel.php";

class FletePresenter
{
    public function manejarPeticiones()
    {
        $filtro_actual = $_GET['filtro'] ?? 'todo';
        $reporte = new ReporteFlete($filtro_actual);

        if (isset($_POST['registrar_flete'])) {
            $reporte->insertar(
                $_POST['fecha'],
                $_POST['origen'],
                $_POST['destino'],
                $_POST['valor']
            );
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=success&filtro=" . $filtro_actual);
            exit();
        }

        if (isset($_POST['editar_flete'])) {
            $reporte->actualizar($_POST['id_flete'], $_POST['fecha'], $_POST['origen'], $_POST['destino'], $_POST['valor']);
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=updated&filtro=" . $filtro_actual);
            exit();
        }

        if (isset($_GET['delete_id'])) {
            $reporte->eliminar($_GET['delete_id']);
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=deleted&filtro=" . $filtro_actual);
            exit();
        }

        if (isset($_GET['id_cambio']) && isset($_GET['valor'])) {
            $reporte->cambiarCancelado($_GET['id_cambio'], $_GET['valor']);
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=updated&filtro=" . $filtro_actual);
            exit();
        }

        $result = $reporte->mostrar();

        return [
            'result' => $result,
            'filtro_actual' => $filtro_actual
        ];
    }
}