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

$loginUrl = '?controller=Auth&action=mostrarLogin';
$email = $_SESSION['recuperacion_email'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Código — DonAppétit</title>
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

                <h1 class="text-2xl font-semibold text-brand">Verificar Código</h1>
                <p class="mt-1 text-sm text-slate-500">Revisá tu email e ingresá el código</p>
                <p class="mt-1 text-xs text-slate-400"><?= htmlspecialchars($email) ?></p>

                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_SESSION['success'])): ?>
                    <div class="mt-4 rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-700">
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="?controller=Auth&action=verificarCodigo" class="mt-6 space-y-4 text-left">
                    <div>
                        <label for="codigo" class="text-sm font-medium text-slate-700">Código de verificación</label>
                        <input id="codigo" name="codigo" type="text" required placeholder="123456" maxlength="6" pattern="[0-9]{6}" autocomplete="off"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-center text-2xl font-bold tracking-[0.5em] focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none">
                        <p class="mt-2 text-xs text-slate-500 text-center">Ingresá el código de 6 dígitos que recibiste por email</p>
                    </div>

                    <button type="submit"
                        class="mt-4 w-full rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-brand/50">
                        Verificar Código
                    </button>
                </form>

                <div class="mt-6 space-y-2 text-sm">
                    <p class="text-slate-500">
                        ¿No recibiste el código?
                        <a href="?controller=Auth&action=mostrarRecuperacion" class="font-semibold text-brand hover:text-brand/80">
                            Reenviar
                        </a>
                    </p>
                    <p class="text-slate-500">
                        <a href="?controller=Auth&action=mostrarLogin" class="font-semibold text-brand hover:text-brand/80">
                            ← Volver al login
                        </a>
                    </p>
                </div>

            </div>
        </section>
    </main>

    <script>
        // Auto-focus en el input del código
        document.getElementById('codigo').focus();
        
        // Solo permitir números
        document.getElementById('codigo').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>