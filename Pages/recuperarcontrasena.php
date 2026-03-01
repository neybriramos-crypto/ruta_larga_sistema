<?php
// ================================
// CARGAR PHPMailer
// ================================
require '../PHPMAILER/src/Exception.php';
require '../PHPMAILER/src/PHPMailer.php';
require '../PHPMAILER/src/SMTP.php';

// Cargar conexión
require '../app/config/claseconexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$mensaje_status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {

    // ================================
    // CONECTAR A LA BASE DE DATOS
    // ================================
    $objConexion = new Conexion();
    $db = $objConexion->conectar();

    $email = $db->real_escape_string($_POST['email']);

    // ================================
    // VERIFICAR SI EXISTE EL USUARIO
    // ================================
    $checkUser = $db->query("SELECT id FROM usuarios WHERE email = '$email'");

    if ($checkUser && $checkUser->num_rows > 0) {

        // ================================
        // GENERAR TOKEN
        // ================================
        $token = bin2hex(random_bytes(20));

        $sql_update = "UPDATE usuarios SET 
                       token_recuperacion = '$token',
                       token_expiracion = DATE_ADD(NOW(), INTERVAL 1 HOUR)
                       WHERE email = '$email'";

        if ($db->query($sql_update)) {

            // ================================
            // CONFIGURAR PHPMailer
            // ================================
            $mail = new PHPMailer(true);

            try {

                // DEBUG (puedes cambiar a 0 cuando funcione)
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->Debugoutput = 'html';

                // CONFIGURACIÓN SMTP GMAIL
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'soporte.rutalarga@gmail.com';
                $mail->Password   = 'zteotxirfisecsxn'; // contraseña de aplicación
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
                $mail->CharSet    = 'UTF-8';

                // REMITENTE
                $mail->setFrom('soporte.rutalarga@gmail.com', 'RutaLarga Support');

                // DESTINATARIO
                $mail->addAddress($email);

                // ================================
                // ENLACE DE RECUPERACIÓN
                // ================================
                $url = "http://localhost/ruta_larga_sistema/Pages/cambiar_clave.php?token=" . $token;

                // CONTENIDO DEL CORREO
                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de contraseña - Ruta Larga';

                $mail->Body = "
                <div style='font-family: Arial; padding:20px'>
                    <h2 style='color:#1d4ed8'>Recuperación de contraseña</h2>
                    <p>Haz clic en el siguiente botón para cambiar tu contraseña:</p>
                    
                    <a href='$url' 
                       style='background:#1d4ed8;
                              color:white;
                              padding:12px 20px;
                              text-decoration:none;
                              border-radius:5px;
                              display:inline-block'>
                       Cambiar contraseña
                    </a>

                    <p style='margin-top:20px;font-size:12px;color:#777'>
                    Este enlace expirará en 1 hora.
                    </p>
                </div>
                ";

                // ENVIAR
                $mail->send();

                $mensaje_status = "Correo enviado correctamente. Revisa tu bandeja o spam.";

            } catch (Exception $e) {

                $mensaje_status = "Error al enviar correo: " . $mail->ErrorInfo;

            }

        } else {

            $mensaje_status = "Error al guardar el token en la base de datos.";

        }

    } else {

        $mensaje_status = "El correo no existe.";

    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recuperar contraseña</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100">

<div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md">

<h2 class="text-2xl font-bold mb-4 text-center">
Recuperar contraseña
</h2>

<?php if ($mensaje_status): ?>

<div class="mb-4 p-3 rounded text-center
<?php echo strpos($mensaje_status,'✅') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
<?php echo $mensaje_status; ?>
</div>

<?php endif; ?>

<form method="POST">

<input type="email"
name="email"
placeholder="Correo electrónico"
required
class="w-full p-3 border rounded mb-4">

<button type="submit"
class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700">

Enviar enlace

</button>

</form>

</div>

</body>
</html>