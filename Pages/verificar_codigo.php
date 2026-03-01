<?php
session_start();

// Si no hay un código en sesión, redirigir al inicio de la recuperación
if (!isset($_SESSION['codigo_verificacion'])) {
    header("Location: recuperarcontrasena.php");
    exit();
}

$mensaje_status = "";
$esError = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigo_ingresado = trim($_POST['codigo']);
    
    // Verificamos si el código coincide
    if ($codigo_ingresado == $_SESSION['codigo_verificacion']) {
        $_SESSION['paso_verificado'] = true; 
        header("Location: cambiar_clave.php");
        exit();
    } else {
        $mensaje_status = "El código ingresado es incorrecto. Intente de nuevo.";
        $esError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código | Ruta Larga</title>
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
        /* Estilo para que el input de número no muestre las flechitas */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
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
                    Verificación</h2>
                <p class="text-gray-500 mt-4 text-sm uppercase tracking-tighter">Ingrese el código de 6 dígitos enviado a su correo</p>
            </div>

            <?php if ($mensaje_status): ?>
                <div class="mb-6 p-4 border-l-4 shadow-sm rounded flex items-center animate-pulse
                    bg-red-50 border-red-500 text-red-700">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium text-sm"><?php echo $mensaje_status; ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-widest text-center">Código de Seguridad</label>
                    <input type="number" name="codigo" required 
                        class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-400 focus:bg-white outline-none transition-all text-center text-3xl font-bold tracking-[10px] placeholder-gray-300"
                        placeholder="000000">
                </div>

                <div class="flex flex-col gap-3 pt-2">
                    <button type="submit"
                        class="w-full bg-[#666666] hover:bg-[#444444] text-white font-bold py-3.5 rounded-lg shadow-lg transform hover:-translate-y-1 transition-all duration-300 uppercase tracking-widest text-sm">
                        Validar Código
                    </button>

                    <a href="recuperarcontrasena.php"
                        class="w-full border-2 border-[#666666] text-[#666666] hover:bg-[#666666] hover:text-white font-bold py-3 rounded-lg text-center transition-all duration-300 uppercase tracking-widest text-sm">
                        Reenviar Correo
                    </a>
                </div>
            </form>

            <div class="mt-8 text-center border-t border-gray-100 pt-6">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest italic">
                    Este código expira al cerrar la sesión
                </p>
            </div>
        </div>
    </main>

    <footer class="bg-[rgb(8,8,44)] text-gray-500 py-6 text-center text-[10px] tracking-[0.2em] uppercase">
        &copy; 2026 RUTA LARGA FURGONES UNIDOS | Logística Segura
    </footer>

</body>
</html>