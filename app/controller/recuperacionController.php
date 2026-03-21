<?php
require_once dirname(__DIR__) . "/model/recuperacionModel.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Carga de PHPMailer desde la raíz del proyecto (Verifica que estas rutas existan en la nueva PC)
require __DIR__ . '/../../PHPMAILER/PHPMailer-master/src/Exception.php';
require __DIR__ . '/../../PHPMAILER/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../PHPMAILER/PHPMailer-master/src/SMTP.php';

class RecuperacionController {
    
    public function manejarPeticion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $recuObj = new Recuperacion();

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['enviar_peticion'])) {
            $correo = trim($_POST['email_recuperar']);
            $usuario = $recuObj->buscarUsuarioPorEmail($correo);

            if ($usuario) {
                // Generar código de 6 dígitos
                $codigo = random_int(100000, 999999);
                
                // Guardar código y fecha en la base de datos
                if ($recuObj->actualizarToken($usuario['ID'], $codigo)) {
                    $_SESSION['id_usuario_recu'] = $usuario['ID'];
                    $_SESSION['email_recu'] = $correo;

                    // Intentar enviar el correo
                    if ($this->enviarEmail($correo, $codigo, $usuario['nombre'] ?? 'Usuario')) {
                        // REDIRECCIÓN EXITOSA
                        echo "<script>
                                window.location.href = '/app/view/verificar_codigoView.php';
                              </script>";
                        exit();
                    } else {
                        // Error al enviar email (El Debug de PHPMailer se mostrará antes de este alert si hay error)
                        echo "<script>
                                alert('No se pudo enviar el correo. Verifique la configuración SMTP o su conexión a internet.');
                                window.location.href = '/app/view/recuperarcontrasena.php';
                              </script>";
                        exit();
                    }
                }
            } else {
                // Usuario no encontrado
                header("Location: /app/view/recuperarcontrasena.php?status=no_existe");
                exit();
            }
        }
    }

    private function enviarEmail($correo, $codigo, $nombre) {
        $mail = new PHPMailer(true);
        try {
            // --- CONFIGURACIÓN DEL SERVIDOR ---
            // Cambia a SMTP::DEBUG_OFF cuando ya funcione en la otra PC
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'soporte.rutalarga@gmail.com'; 
            $mail->Password   = 'lhyrofjopktqkzeh'; // Contraseña de aplicación
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // --- OPCIONES SSL (CRÍTICO PARA QUE FUNCIONE EN CUALQUIER PC LOCAL) ---
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // --- DESTINATARIOS ---
            $mail->setFrom('soporte.rutalarga@gmail.com', 'Soporte Ruta Larga');
            $mail->addAddress($correo);

            // --- CONTENIDO DEL CORREO ---
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
            // El error detallado se imprimirá en pantalla por SMTPDebug
            return false;
        }
    }
}

// Inicializar y ejecutar el proceso
$proceso = new RecuperacionController();
$proceso->manejarPeticion();
?>