<?php
declare(strict_types=1);

/**
 * @var string|null $action
 * @var string|null $loginUrl
 */
$action = $action ?? '#';
$loginUrl = $loginUrl ?? '#';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar contraseña • DonAppetit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: { brand: { DEFAULT: '#0f1629' } },
                    boxShadow: { soft: '0 8px 30px rgba(0,0,0,.08)' }
                }
            }
        }
    </script>
    <style>
        * { transition: all .15s ease-in-out }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen flex flex-col">
    <section class="flex min-h-[70vh] w-full flex-col items-center justify-center py-10">
        <div class="w-full max-w-sm rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-xl shadow-slate-900/5">
            <div class="mx-auto mb-6 h-16 w-16 rounded-2xl bg-emerald-100">
                <span class="sr-only">Logo DonAppetit</span>
                <img src="/donapetit2/public/assets/img/logo-don.png" alt="Logo DonAppetit" class="h-full w-full object-contain" />
            </div>

            <h1 class="text-2xl font-semibold text-slate-900">DonAppetit</h1>
            <p class="mt-1 text-sm text-slate-500">Recuperar contraseña</p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700">
                    <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700">
                    <?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>" class="mt-6 space-y-4 text-left">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                <div>
                    <label for="email" class="text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" required autocomplete="email"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:outline-none" />
                </div>

                <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-500/30 transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                    Enviar enlace de recuperación
                </button>
            </form>

            <p class="mt-6 text-sm text-slate-500">
                ¿Recordaste tu contraseña?
                <a href="<?php echo htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8'); ?>" class="font-semibold text-indigo-600 hover:text-indigo-700">
                    Iniciar sesión
                </a>
            </p>
        </div>
    </section>
</body>
</html>
