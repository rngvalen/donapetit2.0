<?php
declare(strict_types=1);

/**
 * @var array<int,array<string,string>> $ofertas
 * @var string|null $titulo
 */
$ofertas = $ofertas ?? [
    ['nombre' => 'Producto 1', 'origen' => 'Almacen La Esquina', 'distancia' => '1.2 km'],
    ['nombre' => 'Producto 2', 'origen' => 'Sup. San Martin', 'distancia' => '2.3 km'],
    ['nombre' => 'Producto 3', 'origen' => 'Sup. Central', 'distancia' => '4.5 km'],
    ['nombre' => 'Producto 4', 'origen' => 'Supermercado Dona Rosa', 'distancia' => '5.0 km'],
    ['nombre' => 'Producto 5', 'origen' => 'Supermercado Mi Sol', 'distancia' => '2.0 km'],
    ['nombre' => 'Producto 6', 'origen' => 'Supermercado La Esperanza', 'distancia' => '0.4 km'],
];

$titulo = $titulo ?? 'Productos disponibles';
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

        <div class="flex items-center gap-2">
            <button type="button" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 p-2 hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70" aria-label="Menu">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M3 12h18M3 18h18"></path>
                </svg>
            </button>
            <button type="button" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 p-2 hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70" aria-label="Crear">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
            </button>
        </div>
    </header>

    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($ofertas as $oferta): ?>
            <?php
            $nombre = htmlspecialchars((string)($oferta['nombre'] ?? 'Producto sin nombre'), ENT_QUOTES, 'UTF-8');
            $origen = htmlspecialchars((string)($oferta['origen'] ?? 'Origen desconocido'), ENT_QUOTES, 'UTF-8');
            $distancia = htmlspecialchars((string)($oferta['distancia'] ?? 'S/D'), ENT_QUOTES, 'UTF-8');
            ?>
            <article class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                <div class="mb-4 grid h-32 place-items-center rounded-xl border-2 border-dashed border-slate-200 bg-gradient-to-br from-slate-50 to-white text-slate-400 transition group-hover:from-emerald-50 group-hover:text-emerald-500">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h3l2-3h8l2 3h3a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                </div>

                <h2 class="text-center text-base font-semibold text-slate-900">
                    <?php echo $nombre; ?>
                </h2>

                <p class="mt-2 text-center text-sm text-slate-500">
                    <?php echo $origen; ?>
                    <span class="font-semibold text-slate-700">&middot; <?php echo $distancia; ?></span>
                </p>

                <div class="mt-5 flex-1"></div>

                <button type="button" class="mt-4 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-500/30 transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                    Solicitar
                </button>
            </article>
        <?php endforeach; ?>
    </div>
</section>
