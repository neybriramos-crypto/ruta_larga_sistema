<?php
require_once dirname(__DIR__) . "/model/recuperacionModel.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/../../PHPMAILER/PHPMailer-master/src/Exception.php';
require __DIR__ . '/../../PHPMAILER/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../PHPMAILER/PHPMailer-master/src/SMTP.php';

class RecuperacionController {
    
    public function manejarPeticion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $recuObj = new Recuperacion();

        // Inyectamos el script de SweetAlert2 (CDN) para que funcione en las respuestas
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['enviar_peticion'])) {
            $correo = trim($_POST['email_recuperar']);
            $usuario = $recuObj->buscarUsuarioPorEmail($correo);

            if ($usuario) {
                $codigo = random_int(100000, 999999);
                
                if ($recuObj->actualizarToken($usuario['ID'], $codigo)) {
                    $_SESSION['id_usuario_recu'] = $usuario['ID'];
                    $_SESSION['email_recu'] = $correo;

                    if ($this->enviarEmail($correo, $codigo, $usuario['nombre'] ?? 'Usuario')) {
                        echo "<script>
                            Swal.fire({
                                icon: 'success',
                                title: '¡Código Enviado!',
                                text: 'Revisa tu bandeja de entrada.',
                                confirmButtonColor: '#08082c'
                            }).then(() => {
                                window.location.href = '/app/view/verificar_codigoView.php';
                            });
                        </script>";
                        exit();
                    } else {
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de envío',
                                text: 'No se pudo enviar el correo. Revise su conexión.',
                                confirmButtonColor: '#08082c'
                            }).then(() => {
                                window.location.href = '/app/view/recuperarcontrasena.php';
                            });
                        </script>";
                        exit();
                    }
                }
            } else {
                // Usuario no encontrado con SweetAlert
                echo "<script>
                    Swal.fire({
                        icon: 'warning',
                        title: 'Usuario no encontrado',
                        text: 'El correo ingresado no está registrado en nuestro sistema.',
                        confirmButtonColor: '#08082c'
                    }).then(() => {
                        window.location.href = '/app/view/recuperarcontrasena.php';
                    });
                </script>";
                exit();
            }
        }
    }

    private function enviarEmail($correo, $codigo, $nombre) {
        $mail = new PHPMailer(true);
        try {
            // Desactiva el Debug para producción o si el modal no se visualiza bien
            $mail->SMTPDebug = 0; 
    
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'soporte.rutalarga@gmail.com'; 
            $mail->Password   = 'lhyrofjopktqkzeh'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';
    
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
    
            $mail->setFrom('soporte.rutalarga@gmail.com', 'Soporte Ruta Larga');
            $mail->addAddress($correo);
    
            $mail->isHTML(true);
            $mail->Subject = 'Código de recuperación - Ruta Larga';
            $mail->Body    = "
                <div style='font-family: sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 500px;'>
                    <h2 style='color: #08082c;'>Hola {$nombre},</h2>
                    <p>Has solicitado restablecer tu contraseña en el sistema <b>Ruta Larga</b>.</p>
                    <p>Tu código de seguridad es:</p>
                    <div style='background: #f4f4f4; padding: 15px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #08082c;'>
                        {$codigo}
                    </div>
                    <p style='color: #666; font-size: 12px; margin-top: 20px;'>Este código expirará en 15 minutos.</p>
                </div>";
            
            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
}

$proceso = new RecuperacionController();
$proceso->manejarPeticion();