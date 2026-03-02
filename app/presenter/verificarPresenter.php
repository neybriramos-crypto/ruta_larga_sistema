<?php
require_once dirname(__DIR__) . "/model/verificarModel.php";

class VerificarPresenter {
    public function manejarPeticion() {
        // Protección de ruta: Si no hay código en sesión, fuera.
        if (!isset($_SESSION['codigo_verificacion'])) {
            header("Location: recuperarcontrasena.php");
            exit();
        }

        $mensaje_status = "";
        $esError = false;

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['validar'])) {
            $codigo_ingresado = trim($_POST['codigo']);
            $codigo_sesion = $_SESSION['codigo_verificacion'];

            if ($codigo_ingresado == $codigo_sesion) {
                $_SESSION['paso_verificado'] = true;
                header("Location: cambiar_clave.php");
                exit();
            } else {
                $mensaje_status = "El código ingresado es incorrecto. Intente de nuevo.";
                $esError = true;
            }
        }

        return [
            'mensaje' => $mensaje_status,
            'error' => $esError
        ];
    }
}