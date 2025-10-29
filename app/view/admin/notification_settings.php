<?php
declare(strict_types=1);

/**
 * @var int $radioActual
 * @var int $radioMin
 * @var int $radioMax
 * @var array<string,bool> $canales
 */
$radioMin = $radioMin ?? 1;
$radioMax = $radioMax ?? 20;
$radioActual = $radioActual ?? 5;
$canales = $canales ?? [
    'push' => true,
    'email' => true,
];
?>

<section class="mx-auto w-full max-w-4xl py-8">
    <header class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-600">Panel de administracion</p>
        <h1 class="mt-2 text-3xl font-semibold text-slate-900">Configuracion de notificaciones</h1>
        <p class="mt-2 max-w-2xl text-sm text-slate-500">
            Ajusta el radio de cobertura y los canales a traves de los cuales quieres recibir avisos.
        </p>
    </header>

    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-emerald-600/5">
        <div class="flex flex-col gap-6">
            <div>
                <label for="radio" class="block text-sm font-medium text-slate-700">
                    Radio de notificacion (km)
                </label>
                <div class="mt-3 flex items-center gap-4">
                    <input
                        type="range"
                        id="radio"
                        name="radio"
                        min="<?php echo htmlspecialchars((string)$radioMin, ENT_QUOTES, 'UTF-8'); ?>"
                        max="<?php echo htmlspecialchars((string)$radioMax, ENT_QUOTES, 'UTF-8'); ?>"
                        value="<?php echo htmlspecialchars((string)$radioActual, ENT_QUOTES, 'UTF-8'); ?>"
                        step="1"
                        class="h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-200 accent-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    <span id="radio-value" class="inline-flex h-10 min-w-[3rem] items-center justify-center rounded-full bg-emerald-100 px-4 text-sm font-semibold text-emerald-700">
                        <?php echo htmlspecialchars((string)$radioActual, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </div>
            </div>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-slate-700">Canales disponibles</legend>

                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100">
                    <input type="checkbox" name="canal_push" <?php echo !empty($canales['push']) ? 'checked' : ''; ?> class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                    Notificaciones push
                </label>

                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100">
                    <input type="checkbox" name="canal_email" <?php echo !empty($canales['email']) ? 'checked' : ''; ?> class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                    Notificaciones por email
                </label>
            </fieldset>

            <div class="flex flex-wrap gap-4 pt-2">
                <button type="button" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-500/30 transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                    Anadir
                </button>
                <button type="button" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                    Filtrar
                </button>
            </div>
        </div>
    </div>
</section>

<script defer src="/donapetit2/public/assets/js/notification_settings.js"></script>
