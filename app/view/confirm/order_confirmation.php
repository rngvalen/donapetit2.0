<?php
declare(strict_types=1);

/**
 * @var array<string,string> $entrega
 * @var array<int,string> $productos
 */
$entrega = $entrega ?? [
    'fecha' => '15/06/2023',
    'hora' => '14:35',
    'donante' => 'Supermercado El Ahorro',
    'receptor' => 'Comedor Comunitario Las Flores',
];

$productos = $productos ?? [
    '5 kg de arroz',
    '3 kg de porotos',
    '10 paquetes de pasta',
    '2 kg de azucar',
];
?>

<section class="mx-auto flex w-full max-w-3xl flex-col items-center py-10 text-center">
    <div class="grid h-24 w-24 place-items-center rounded-full bg-emerald-100 text-emerald-600 shadow-inner">
        <svg class="h-14 w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 6L9 17l-5-5"></path>
        </svg>
    </div>

    <h1 class="mt-6 text-3xl font-semibold text-slate-900 sm:text-4xl">Entrega confirmada</h1>
    <p class="mt-3 max-w-xl text-sm text-slate-500">
        La donacion ha sido registrada correctamente y ambas partes recibiran una notificacion.
    </p>

    <dl class="mt-8 w-full rounded-3xl border border-slate-200 bg-white p-6 text-left shadow-xl shadow-emerald-600/5">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha</dt>
                <dd class="text-base font-semibold text-slate-900">
                    <?php echo htmlspecialchars($entrega['fecha'] ?? 'Sin fecha', ENT_QUOTES, 'UTF-8'); ?>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hora</dt>
                <dd class="text-base font-semibold text-slate-900">
                    <?php echo htmlspecialchars($entrega['hora'] ?? 'Sin hora', ENT_QUOTES, 'UTF-8'); ?>
                </dd>
            </div>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Donante</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-900">
                    <?php echo htmlspecialchars($entrega['donante'] ?? 'Sin dato', ENT_QUOTES, 'UTF-8'); ?>
                </dd>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Receptor</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-900">
                    <?php echo htmlspecialchars($entrega['receptor'] ?? 'Sin dato', ENT_QUOTES, 'UTF-8'); ?>
                </dd>
            </div>
        </div>

        <div class="mt-6">
            <h2 class="text-sm font-semibold text-slate-700">Productos entregados</h2>
            <ul class="mt-3 list-disc space-y-2 text-sm text-slate-600 sm:pl-5">
                <?php foreach ($productos as $producto): ?>
                    <li><?php echo htmlspecialchars($producto, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </dl>

    <div class="mt-8 flex flex-wrap justify-center gap-4">
        <button type="button" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-500/30 transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
            Ver mapa de donaciones
        </button>
        <button type="button" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
            Compartir confirmacion
        </button>
    </div>

    <div class="mt-8 max-w-xl rounded-3xl border border-emerald-100 bg-emerald-50 p-6 text-sm text-emerald-700">
        Esta confirmacion queda disponible en el historial de ambas partes. Gracias por reducir el desperdicio de alimentos.
    </div>
</section>
