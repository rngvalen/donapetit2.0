<?php
declare(strict_types=1);

/**
 * @var array<int,array<string,mixed>> $productos
 * @var string|null $titulo
 */
$productos = $productos ?? [
    ['nombre' => 'Producto 1', 'cantidad' => 0, 'vence' => '0/0'],
    ['nombre' => 'Producto 2', 'cantidad' => 0, 'vence' => '0/0'],
    ['nombre' => 'Producto 3', 'cantidad' => 0, 'vence' => '0/0'],
    ['nombre' => 'Producto 4', 'cantidad' => 0, 'vence' => '0/0'],
    ['nombre' => 'Producto 5', 'cantidad' => 0, 'vence' => '0/0'],
    ['nombre' => 'Producto 6', 'cantidad' => 0, 'vence' => '0/0'],
];

$titulo = $titulo ?? 'Mis productos';
?>

<section class="py-6 text-slate-800">
    <header class="flex items-center justify-between gap-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-400 px-4 py-3 text-white shadow-lg shadow-emerald-700/20">
        <button type="button" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 p-2 hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70" aria-label="Volver">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 18l-6-6 6-6"></path>
            </svg>
        </button>

        <h1 class="text-lg font-semibold tracking-tight sm:text-xl">
            <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>
        </h1>

        <button type="button" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 p-2 hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70" aria-label="Menu">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 6h18M3 12h18M3 18h18"></path>
            </svg>
        </button>
    </header>

    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($productos as $producto): ?>
            <?php
            $nombre = htmlspecialchars((string)($producto['nombre'] ?? 'Producto sin nombre'), ENT_QUOTES, 'UTF-8');
            $cantidad = $producto['cantidad'] ?? null;
            $cantidadLabel = $cantidad === null ? 'N/D' : (string)$cantidad;
            $vence = htmlspecialchars((string)($producto['vence'] ?? 'Sin fecha'), ENT_QUOTES, 'UTF-8');
            ?>
            <article class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                <div class="mb-4 grid h-36 place-items-center rounded-xl border-2 border-dashed border-slate-200 bg-gradient-to-br from-slate-50 to-white text-slate-400 transition group-hover:from-emerald-50 group-hover:text-emerald-500">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h3l2-3h8l2 3h3a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                </div>

                <h2 class="text-center text-base font-semibold text-slate-900">
                    <?php echo $nombre; ?>
                </h2>

                <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="flex flex-col rounded-xl bg-slate-50 px-3 py-2 text-center">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Cantidad</dt>
                        <dd class="text-base font-semibold text-slate-900"><?php echo htmlspecialchars($cantidadLabel, ENT_QUOTES, 'UTF-8'); ?></dd>
                    </div>
                    <div class="flex flex-col rounded-xl bg-slate-50 px-3 py-2 text-center">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Vence</dt>
                        <dd class="text-base font-semibold text-slate-900"><?php echo $vence; ?></dd>
                    </div>
                </dl>

                <div class="mt-5 flex items-center justify-center gap-3">
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        Ver
                    </button>
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                        </svg>
                        Editar
                    </button>
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                            <path d="M10 11v6M14 11v6"></path>
                            <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"></path>
                        </svg>
                        Eliminar
                    </button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <button type="button" class="fixed bottom-6 right-6 flex h-14 w-14 items-center justify-center rounded-full bg-indigo-600 text-white shadow-2xl shadow-indigo-500/40 transition hover:scale-110 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-300" aria-label="Agregar producto">
        <svg class="h-6 w-6" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2">
            <path d="M12 5v14M5 12h14"></path>
        </svg>
    </button>
</section>
