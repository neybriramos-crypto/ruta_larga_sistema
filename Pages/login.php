<?php
// 1. Iniciar sesión para rastrear los intentos fallidos
session_start();

// 2. Lógica de rutas y carga del Presenter
require_once dirname(__DIR__) . "/app/presenter/LoginPresenter.php";

$presenter = new LoginPresenter();
$mensaje = null;
$esError = false;
$bloqueado = false;

// --- INICIO LÓGICA DE BLOQUEO ---

// Definir constantes de seguridad
$max_intentos = 3;
$tiempo_bloqueo = 5 * 60; // 5 minutos en segundos

// Verificar si el usuario ya está bloqueado
if (isset($_SESSION['bloqueo_expira']) && time() < $_SESSION['bloqueo_expira']) {
    $bloqueado = true;
    $segundos_restantes = $_SESSION['bloqueo_expira'] - time();
    $minutos = ceil($segundos_restantes / 60);
    $mensaje = "Acceso restringido. Por seguridad, espera $minutos minuto(s) para intentar de nuevo.";
    $esError = true;
}

// Procesar el formulario si se envió y NO está bloqueado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$bloqueado) {
    $mensaje = $presenter->iniciarSesion();

    if ($mensaje) {
        // El login falló: aumentar contador
        $esError = true;
        $_SESSION['intentos_fallidos'] = ($_SESSION['intentos_fallidos'] ?? 0) + 1;

        if ($_SESSION['intentos_fallidos'] >= $max_intentos) {
            $_SESSION['bloqueo_expira'] = time() + $tiempo_bloqueo;
            $_SESSION['intentos_fallidos'] = 0; // Reiniciar contador para el siguiente ciclo
            $bloqueado = true;
            $mensaje = "Has superado los 3 intentos. Acceso bloqueado por 5 minutos.";
        }
    } else {
        // Login exitoso: limpiar rastros de intentos previos
        $_SESSION['intentos_fallidos'] = 0;
        unset($_SESSION['bloqueo_expira']);
    }
}

// Lógica para mensaje de cierre de sesión
if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'sesion_cerrada') {
    $mensaje = "Has salido del sistema correctamente.";
    $esError = false;
}

// --- FIN LÓGICA DE BLOQUEO ---
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema | Ruta Larga</title>
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
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
        }
    </style>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">

    <header class="fixed top-0 w-full px-10 py-5 flex justify-between items-center z-50 bg-[rgba(8,8,44,0.9)] shadow-lg">
        <h2 class="text-white text-2xl font-bold tracking-wider">RUTA LARGA</h2>
        <nav class="hidden md:flex gap-8 text-sm uppercase tracking-widest">
            <a href="soporte.php" class="text-white hover:text-gray-300 transition-colors">Soporte</a>
        </nav>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 pt-32 pb-12">
        <div class="bg-white w-full max-w-md p-10 rounded-2xl shadow-2xl border border-gray-100">

            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 border-b-4 border-gray-600 inline-block pb-2 italic">Bienvenido</h2>
                <p class="text-gray-500 mt-4 text-sm uppercase tracking-tighter">Gestión de Flota y Logística</p>
            </div>

            <?php if ($mensaje): ?>
                <div class="mb-6 p-4 border-l-4 shadow-sm rounded flex items-center animate-pulse
                    <?php echo $esError ? 'bg-red-50 border-red-500 text-red-700' : 'bg-green-50 border-green-500 text-green-700'; ?>">
                    
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <?php if ($esError): ?>
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        <?php else: ?>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        <?php endif; ?>
                    </svg>
                    <span class="font-medium text-sm"><?php echo $mensaje; ?></span>
                </div>
            <?php endif; ?>

            <?php if (!$bloqueado): ?>
                <form action="" method="post" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-widest">Correo Electrónico</label>
                        <input type="email" name="correo" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-400 focus:bg-white outline-none transition-all placeholder-gray-300"
                            placeholder="usuario@rutalarga.com">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-widest">Contraseña</label>
                        <input type="password" name="clave" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-400 focus:bg-white outline-none transition-all placeholder-gray-300"
                            placeholder="••••••••">

                        <div class="text-right mt-2">
                            <a href="recuperarcontrasena.php"
                                class="text-[10px] text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest italic">
                                ¿Olvidó su contraseña?
                            </a>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 pt-4">
                        <button type="submit"
                            class="w-full bg-[#666666] hover:bg-[#444444] text-white font-bold py-3.5 rounded-lg shadow-lg transform hover:-translate-y-1 transition-all duration-300 uppercase tracking-widest text-sm">
                            Iniciar Sesión
                        </button>

                        <a href="nuevoregistro1.php"
                            class="w-full border-2 border-[#666666] text-[#666666] hover:bg-[#666666] hover:text-white font-bold py-3 rounded-lg text-center transition-all duration-300 uppercase tracking-widest text-sm">
                            Registrarme
                        </a>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center p-6 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <p class="text-gray-400 text-sm italic">Intento de acceso deshabilitado temporalmente.</p>
                    <button onclick="location.reload()" class="mt-4 text-xs text-blue-500 font-bold uppercase tracking-widest hover:underline">
                        Verificar si ya puedo entrar
                    </button>
                </div>
            <?php endif; ?>

            <div class="mt-8 text-center border-t border-gray-100 pt-6">
                <a href="soporte.php"
                    class="text-xs text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest italic">
                    ¿Problemas con su cuenta? Soporte técnico
                </a>
            </div>
        </div>
    </main>

    <footer class="bg-[rgb(8,8,44)] text-gray-500 py-6 text-center text-[10px] tracking-[0.2em] uppercase">
        &copy; 2026 RUTA LARGA FURGONES UNIDOS | Logística Segura
    </footer>

</body>
</html>