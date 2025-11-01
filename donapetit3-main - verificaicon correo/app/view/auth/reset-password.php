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

                <form method="post" action="?controller=Auth&action=cambiarContrasena" class="mt-6 space-y-4 text-left" id="resetForm">
                    <div>
                        <label for="password" class="text-sm font-medium text-slate-700">Nueva Contraseña</label>
                        <input id="password" name="password" type="password" required placeholder="Mínimo 8 caracteres" minlength="8"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none">
                        <p class="mt-1 text-xs text-slate-500">Debe tener al menos 8 caracteres</p>
                    </div>

                    <div>
                        <label for="confirm_password" class="text-sm font-medium text-slate-700">Confirmar Contraseña</label>
                        <input id="confirm_password" name="confirm_password" type="password" required placeholder="Repetir contraseña" minlength="8"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none">
                        <p id="matchError" class="mt-1 text-xs text-red-500 hidden">Las contraseñas no coinciden</p>
                    </div>

                    <button type="submit"
                        class="mt-4 w-full rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-brand/50">
                        Cambiar Contraseña
                    </button>
                </form>

            </div>
        </section>
    </main>

    <script>
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const matchError = document.getElementById('matchError');
        const form = document.getElementById('resetForm');

        password.focus();

        // Validar que coincidan las contraseñas
        function validatePasswords() {
            if (confirmPassword.value && password.value !== confirmPassword.value) {
                matchError.classList.remove('hidden');
                confirmPassword.classList.add('border-red-300');
                return false;
            } else {
                matchError.classList.add('hidden');
                confirmPassword.classList.remove('border-red-300');
                return true;
            }
        }

        confirmPassword.addEventListener('input', validatePasswords);
        password.addEventListener('input', validatePasswords);

        form.addEventListener('submit', function(e) {
            if (!validatePasswords()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
```

---

## **Resumen de archivos:**
```
/app/view/auth/
├── forgot-password.php    ← Solicitar email
├── verify-code.php        ← Ingresar código del email (ya lo tenés)
└── reset-password.php     ← Cambiar contraseña