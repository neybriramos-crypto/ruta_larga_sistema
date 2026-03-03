<?php
require_once dirname(__DIR__) . "/model/recuperacionModel.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carga de PHPMailer (ajusta las rutas según tu estructura)
require __DIR__ . '/../PHPMAILER/PHPMailer-master/src/Exception.php';
require __DIR__ . '/../PHPMAILER/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../PHPMAILER/PHPMailer-master/src/SMTP.php';

class RecuperacionPresenter {
    public function manejarPeticion() {
        $recuObj = new Recuperacion();
        $mensaje_status = "";
        $esError = false;

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['enviar_codigo'])) {
            $correo = trim($_POST['email']);
            $usuario = $recuObj->buscarUsuarioPorEmail($correo);

            if ($usuario) {
                $codigo = random_int(100000, 999999);
                $recuObj->actualizarToken($usuario['ID'], $codigo);

                $_SESSION['codigo_verificacion'] = $codigo;
                $_SESSION['id_usuario'] = $usuario['ID'];

                if ($this->enviarEmail($correo, $codigo)) {
                    header("Location: verificar_codigo.php");
                    exit();
                } else {
                    $mensaje_status = "Error al enviar el correo.";
                    $esError = true;
                }
            } else {
                $mensaje_status = "El correo no está registrado.";
                $esError = true;
            }
        }

        return ['mensaje' => $mensaje_status, 'error' => $esError];
    }

    private function enviarEmail($correo, $codigo) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'soporte.rutalarga@gmail.com'; 
            $mail->Password   = 'lhyrofjopktqkzeh'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom('soporte.rutalarga@gmail.com', 'Soporte Ruta Larga');
            $mail->addAddress($correo);
            $mail->isHTML(true);
            $mail->Subject = 'Codigo de recuperacion - Ruta Larga';
            $mail->Body    = "Su codigo de verificacion es: <b>{$codigo}</b>";
            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
}