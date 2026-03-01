<?php
require_once dirname(__DIR__) . "/model/clientesModel.php";

class ClientesPresenter
{
    public function manejarPeticiones()
    {
        $clienteObj = new Cliente();
        $msg_js = "";

        // PROCESAMIENTO
        if (isset($_POST['registrar']) || isset($_POST['editar'])) {
            // Unir RIF (Tipo + Número)
            $tipo_doc = $_POST['tipo_doc'] ?? 'V';
            $num_rif = preg_replace('/[^0-9]/', '', $_POST['RIF_cedula']);
            $rif_final = $tipo_doc . $num_rif;

            // Unir Teléfono (Operadora + Número)
            $operadora = $_POST['operadora'] ?? '0414';
            $num_telf = preg_replace('/[^0-9]/', '', $_POST['telefono_num']);
            $telf_final = $operadora . $num_telf;

            $nombre = trim($_POST['nombre']);

            if (strlen($num_rif) < 6) {
                $msg_js = "swalError('El documento es muy corto.');";
            } elseif (strlen($num_telf) != 7) {
                $msg_js = "swalError('El número de teléfono debe tener 7 dígitos después de la operadora.');";
            } else {
                $clienteObj->setRif($rif_final);
                $clienteObj->setNombre($nombre);
                $clienteObj->setTelefono($telf_final);

                if (isset($_POST['registrar'])) {
                    $clienteObj->insertar();
                    header("Location: " . $_SERVER['PHP_SELF'] . "?status=reg");
                } else {
                    $clienteObj->setId($_POST['ID_cliente']);
                    $clienteObj->modificar();
                    header("Location: " . $_SERVER['PHP_SELF'] . "?status=edit");
                }
                exit();
            }
        }

        if (isset($_GET['delete'])) {
            $clienteObj->eliminar(intval($_GET['delete']));
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=del");
            exit();
        }

        $result = $clienteObj->listar();

        return [
            'result' => $result,
            'msg_js' => $msg_js
        ];
    }
}
