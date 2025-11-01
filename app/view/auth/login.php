<?php
declare(strict_types=1);

/**
 * @var string|null $action
 * @var string|null $registerUrl
 * @var string|null $forgotUrl
 */
$action = $action ?? '#';
$registerUrl = $registerUrl ?? '#';
$forgotUrl = $forgotUrl ?? '#';
?>

<section class="flex min-h-[70vh] w-full flex-col items-center justify-center py-10">
    <div class="w-full max-w-sm rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-xl shadow-slate-900/5">
        <div class="mx-auto mb-6 h-16 w-16 rounded-2xl bg-emerald-100">
            <span class="sr-only">Logo DonAppetit</span>
            <img src="/donapetit2/public/assets/img/logo-don.png" alt="Logo DonAppetit" class="h-full w-full object-contain" />
        </div>

        <h1 class="text-2xl font-semibold text-slate-900">DonAppetit</h1>
        <p class="mt-1 text-sm text-slate-500">Ingresar al panel</p>

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
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500" />
            </div>

            <div>
                <label for="password" class="text-sm font-medium text-slate-700">Contrasena</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500" />
            </div>

            <a href="<?php echo htmlspecialchars($forgotUrl, ENT_QUOTES, 'UTF-8'); ?>" class="inline-block text-sm font-medium text-emerald-600 hover:text-emerald-700">
                Olvidaste tu contrasena?
            </a>

            <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-500/30 transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                Iniciar sesion
            </button>
        </form>

        <p class="mt-6 text-sm text-slate-500">
            No tienes cuenta?
            <a href="<?php echo htmlspecialchars($registerUrl, ENT_QUOTES, 'UTF-8'); ?>" class="font-semibold text-emerald-600 hover:text-emerald-700">
                Registrate
            </a>
        </p>
    </div>
</section>
