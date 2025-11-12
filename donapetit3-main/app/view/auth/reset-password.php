<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$scriptDir = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$projectBase = $scriptDir;
$posApp = strpos($projectBase, '/app/');
if ($posApp !== false) {
    $projectBase = substr($projectBase, 0, $posApp);
}
if ($projectBase === '') { $projectBase = '/'; }

$tryPublic = rtrim($projectBase, '/') . '/public/assets/img/logo-don.png';
$tryAssets = rtrim($projectBase, '/') . '/assets/img/logo-don.png';

$logoPath = $tryPublic;
if (!is_file($_SERVER['DOCUMENT_ROOT'] . $logoPath)) {
    $logoPath = $tryAssets;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva Contraseña — DonAppétit</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: '#3D538F' },
                    fontFamily: { sans: ['Montserrat','Inter','system-ui','sans-serif'] },
                    boxShadow: { soft: '0 20px 60px rgba(15,22,41,.12)' }
                }
            }
        };
    </script>
    <style>*{transition:all .15s ease-in-out}</style>
</head>

<body class="bg-slate-100 text-slate-900 min-h-screen flex items-center justify-center font-sans">
    <main class="w-full max-w-6xl px-4">
        <section class="flex items-center justify-center py-10">
            <div class="w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-xl shadow-slate-900/5">

                <div class="mx-auto mb-4 h-20 w-20 overflow-hidden rounded-2xl shadow-md bg-white">
                    <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo DonAppétit" class="h-full w-full object-contain">
                </div>

                <h1 class="text-2xl font-semibold text-brand">Nueva Contraseña</h1>
                <p class="mt-1 text-sm text-slate-500">Ingresá tu nueva contraseña</p>

                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="?controller=Auth&action=cambiarContrasena" class="mt-6 space-y-4 text-left">
                    <div>
                        <label for="password" class="text-sm font-medium text-slate-700">Nueva Contraseña</label>
                        <div class="relative mt-2">
                            <input id="password" name="password" type="password" required placeholder="Mínimo 8 caracteres"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 pr-12 text-sm focus:border-brand focus:ring-brand/60 focus:outline-none">

                            <button
                                type="button"
                                onclick="togglePassword('password', 'eye-closed-1', 'eye-open-1')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors"
                                aria-label="Mostrar contraseña">
                                <svg id="eye-closed-1" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                                <svg id="eye-open-1" class="w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password" class="text-sm font-medium text-slate-700">Confirmar Contraseña</label>
                        <div class="relative mt-2">
                            <input id="confirm_password" name="confirm_password" type="password" required placeholder="Repetir contraseña"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 pr-12 text-sm focus:border-brand focus:ring-brand/60 focus:outline-none">

                            <button
                                type="button"
                                onclick="togglePassword('confirm_password', 'eye-closed-2', 'eye-open-2')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors"
                                aria-label="Mostrar contraseña">
                                <svg id="eye-closed-2" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                                <svg id="eye-open-2" class="w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="mt-2 w-full rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand/90">
                        Cambiar Contraseña
                    </button>
                </form>

            </div>
        </section>
    </main>

    <script>
        function togglePassword(inputId, eyeClosedId, eyeOpenId) {
            const passwordInput = document.getElementById(inputId);
            const eyeClosed = document.getElementById(eyeClosedId);
            const eyeOpen = document.getElementById(eyeOpenId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeClosed.classList.add('hidden');
                eyeOpen.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeClosed.classList.remove('hidden');
                eyeOpen.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
