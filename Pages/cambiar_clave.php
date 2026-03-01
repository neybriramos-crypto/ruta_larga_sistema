<?php
session_start();
require '../app/config/claseconexion.php';

// Bloqueo de seguridad: Si no ha validado el código, fuera
if (!isset($_SESSION['paso_verificado']) || !isset($_SESSION['id_usuario'])) {
    header("Location: recuperarcontrasena.php");
    exit();
}

$mensaje_status = "";
$esError = false;
$exito = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];

    if ($pass1 === $pass2 && strlen($pass1) >= 6) {
        $objConexion = new Conexion();
        $conn = $objConexion->conectar();
        
        // Generamos el hash para que sea compatible con tu login (password_verify)
        $nueva_clave_hash = password_hash($pass1, PASSWORD_DEFAULT);
        $id_usuario = $_SESSION['id_usuario'];

        // Actualizamos la clave y limpiamos el token de recuperación
        $sql = "UPDATE usuarios SET Contraseña = ?, token_recuperacion = NULL WHERE ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nueva_clave_hash, $id_usuario);

        if ($stmt->execute()) {
            $exito = true;
            $mensaje_status = "¡Contraseña actualizada con éxito!";
            // No destruimos la sesión aquí mismo para poder mostrar el mensaje, 
            // pero invalidamos los pasos de recuperación.
            unset($_SESSION['paso_verificado']);
            unset($_SESSION['codigo_verificacion']);
        } else {
            $mensaje_status = "Error técnico al actualizar la base de datos.";
            $esError = true;
        }
    } else {
        $mensaje_status = "Las contraseñas no coinciden o son menores a 6 caracteres.";
        $esError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña | Ruta Larga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/img/fondo.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
        }
    </style>
</head>

<body class="flex flex-col min-h-screen">

    <header class="fixed top-0 w-full px-10 py-5 flex justify-between items-center z-50 bg-[rgba(8,8,44,0.9)] shadow-lg">
        <h2 class="text-white text-2xl font-bold tracking-wider">RUTA LARGA</h2>
        <nav class="hidden md:flex gap-8 text-sm uppercase tracking-widest">
            <a href="soporte.php" class="text-white hover:text-gray-300 transition-colors">Soporte</a>
        </nav>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 pt-32 pb-12">
        <div class="glass-card w-full max-w-md p-10 rounded-2xl shadow-2xl border border-gray-100">

            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 border-b-4 border-gray-600 inline-block pb-2 italic">
                    Nueva Clave</h2>
                <p class="text-gray-500 mt-4 text-sm uppercase tracking-tighter">Cree una contraseña segura y fácil de recordar</p>
            </div>

            <?php if ($mensaje_status): ?>
                <div class="mb-6 p-4 border-l-4 shadow-sm rounded flex items-center 
                    <?php echo $exito ? 'bg-green-50 border-green-500 text-green-700' : 'bg-red-50 border-red-500 text-red-700 animate-pulse'; ?>">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <?php if ($exito): ?>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        <?php else: ?>
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        <?php endif; ?>
                    </svg>
                    <span class="font-medium text-sm"><?php echo $mensaje_status; ?></span>
                </div>
            <?php endif; ?>

            <?php if (!$exito): ?>
                <form action="" method="post" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-widest">Nueva Contraseña</label>
                        <input type="password" name="pass1" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-400 focus:bg-white outline-none transition-all placeholder-gray-300"
                            placeholder="••••••••">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-widest">Confirmar Contraseña</label>
                        <input type="password" name="pass2" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-400 focus:bg-white outline-none transition-all placeholder-gray-300"
                            placeholder="••••••••">
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-[#666666] hover:bg-[#444444] text-white font-bold py-3.5 rounded-lg shadow-lg transform hover:-translate-y-1 transition-all duration-300 uppercase tracking-widest text-sm">
                            Actualizar Contraseña
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center pt-4">
                    <a href="login.php"
                        class="block w-full bg-[#08082c] hover:bg-black text-white font-bold py-4 rounded-lg shadow-lg transition-all duration-300 uppercase tracking-widest text-sm">
                        Ir al Inicio de Sesión
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <footer class="bg-[rgb(8,8,44)] text-gray-500 py-6 text-center text-[10px] tracking-[0.2em] uppercase">
        &copy; 2026 RUTA LARGA FURGONES UNIDOS | Logística Segura
    </footer>

</body>
</html>