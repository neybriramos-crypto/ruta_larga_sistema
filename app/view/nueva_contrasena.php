<?php
session_start();
if (!isset($_SESSION['id_usuario_recu'])) {
    header("Location: /app/view/recuperarcontrasena.php");
    exit();
}

require_once "../controller/contrasenaController.php";
$controller = new ContrasenaController();
$resultado = $controller->procesarCambio();

$mostrarModal = false;
if (isset($resultado['status']) && $resultado['status'] === 'success') {
    $mostrarModal = true;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nueva Contraseña | Ruta Larga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        * { font-family: Georgia, 'Times New Roman', Times, serif; }
        body {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../../assets/img/fondo.jpg');
            background-size: cover; background-position: center; background-attachment: fixed;
        }
        .glass-card { background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); }
        .valid { color: #10b981; font-weight: bold; }
        .invalid { color: #9ca3af; }
        button:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }
        
        .fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <?php if ($mostrarModal): ?>
    <div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md">
        <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-sm w-full text-center border-t-8 border-gray-800 fade-in mx-4">
            <div class="mb-4 text-green-500">
                <i class="ph-fill ph-check-circle text-7xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2 italic">¡Todo listo!</h2>
            <p class="text-gray-600 mb-8 text-sm leading-relaxed">
                Tu contraseña ha sido actualizada con éxito. Ya puedes acceder a tu cuenta.
            </p>
            <a href="loginView.php" class="block w-full bg-gray-800 hover:bg-black text-white font-bold py-4 rounded-xl transition-all uppercase tracking-widest text-xs shadow-lg">
                Aceptar (Ir al Login)
            </a>
        </div>
    </div>
    <?php endif; ?>

    <div class="max-w-md w-full glass-card p-10 rounded-2xl shadow-2xl border border-gray-100">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 border-b-4 border-gray-600 inline-block pb-2 italic">Restablecer Clave</h1>
        </div>

        <?php if (!empty($resultado['mensaje']) && !$mostrarModal): ?>
            <div class="p-3 mb-4 rounded text-sm text-center bg-red-50 text-red-700 border border-red-200">
                <i class="ph ph-warning-circle mr-1"></i> <?= $resultado['mensaje'] ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" id="passwordForm" class="space-y-6">
            <div>
                <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-widest">Nueva Contraseña</label>
                <input type="password" name="nueva_pass" id="password" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-gray-400 outline-none transition-all"
                    placeholder="••••••••" />
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-widest">Confirmar Contraseña</label>
                <input type="password" name="confirmar_pass" id="confirm_password" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-gray-400 outline-none transition-all"
                    placeholder="Repita su contraseña" />
                <p id="match-msg" class="text-[9px] mt-1 hidden"></p>
            </div>

            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-inner">
                <p class="text-[10px] font-bold text-gray-400 uppercase mb-2 tracking-widest">Requisitos obligatorios:</p>
                <ul class="grid grid-cols-2 gap-x-2 gap-y-1 text-[10px] italic">
                    <li id="req-length" class="invalid flex items-center transition-colors"><span class="bullet mr-1">○</span> 8+ caracteres</li>
                    <li id="req-upper" class="invalid flex items-center transition-colors"><span class="bullet mr-1">○</span> Una Mayúscula</li>
                    <li id="req-lower" class="invalid flex items-center transition-colors"><span class="bullet mr-1">○</span> Una Minúscula</li>
                    <li id="req-symbol" class="invalid flex items-center transition-colors"><span class="bullet mr-1">○</span> Un Símbolo</li>
                </ul>
            </div>

            <button type="submit" name="guardar_clave" id="btn-submit" disabled
                class="w-full bg-gray-700 hover:bg-gray-900 text-white font-bold py-3.5 rounded-lg shadow-lg transform hover:-translate-y-1 transition-all duration-300 uppercase tracking-widest text-sm">
                Actualizar Contraseña
            </button>
        </form>

        <div class="text-center mt-6 border-t border-gray-100 pt-6">
            <a href="loginView.php" class="text-xs text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest italic">
                Cancelar y regresar
            </a>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        const matchMsg = document.getElementById('match-msg');
        const btnSubmit = document.getElementById('btn-submit');
        
        const requirements = {
            length: { el: document.getElementById('req-length'), regex: /.{8,}/ },
            upper: { el: document.getElementById('req-upper'), regex: /[A-Z]/ },
            lower: { el: document.getElementById('req-lower'), regex: /[a-z]/ },
            // Símbolo o caracter especial
            symbol: { el: document.getElementById('req-symbol'), regex: /[^A-Za-z0-9]/ } 
        };

        function validateForm() {
            const val = passwordInput.value;
            let allValid = true;

            // Validar cada requisito
            Object.keys(requirements).forEach(key => {
                const req = requirements[key];
                const isValid = req.regex.test(val);
                req.el.classList.toggle('valid', isValid);
                req.el.classList.toggle('invalid', !isValid);
                req.el.querySelector('.bullet').textContent = isValid ? '●' : '○';
                if (!isValid) allValid = false;
            });

            // Validar si coinciden
            const match = (val === confirmInput.value && val !== "");
            
            // UI de coincidencia
            if (confirmInput.value !== "") {
                matchMsg.classList.remove('hidden');
                matchMsg.textContent = match ? "Las contraseñas coinciden" : "Las contraseñas no coinciden";
                matchMsg.className = match ? "text-[9px] mt-1 text-green-600 font-bold" : "text-[9px] mt-1 text-red-500 italic";
            } else {
                matchMsg.classList.add('hidden');
            }

            // Habilitar botón solo si todo es correcto
            btnSubmit.disabled = !(allValid && match);
        }

        passwordInput.addEventListener('input', validateForm);
        confirmInput.addEventListener('input', validateForm);
    </script>
</body>
</html>