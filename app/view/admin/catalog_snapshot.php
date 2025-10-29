<?php
declare(strict_types=1);

/**
 * @var array<int,array<string,string>> $categorias
 */
$categorias = $categorias ?? [
    ['nombre' => 'Frutas y verduras', 'cantidad' => '32 productos', 'vence' => '20/10/2025'],
    ['nombre' => 'Lacteos', 'cantidad' => '18 productos', 'vence' => '12/11/2025'],
    ['nombre' => 'Panificados', 'cantidad' => '15 productos', 'vence' => '02/10/2025'],
    ['nombre' => 'Bebidas', 'cantidad' => '20 productos', 'vence' => '30/09/2025'],
];
?>

<section class="mx-auto w-full max-w-5xl space-y-8 py-8">
    <header class="flex flex-col gap-4 rounded-3xl bg-gradient-to-r from-emerald-600 to-emerald-400 px-8 py-10 text-white shadow-xl shadow-emerald-600/25 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-white/70">DonAppetit Panel</p>
            <h1 class="mt-2 text-3xl font-semibold sm:text-4xl">Catalogo de productos</h1>
        </div>
        <button type="button" class="inline-flex items-center justify-center rounded-2xl bg-white/15 px-4 py-2 text-sm font-semibold uppercase tracking-wide hover:bg-white/25">
            Menu
        </button>
    </header>

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-900/5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Buscar productos</h2>
            <div class="flex flex-wrap gap-3">
                <input type="search" placeholder="Buscar productos" class="w-64 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                <button type="button" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-500/30 transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">Anadir</button>
                <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">Filtrar</button>
            </div>
        </div>
    </section>

    <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($categorias as $categoria): ?>
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 transition hover:-translate-y-1 hover:shadow-2xl">
                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-700">
                    <?php echo htmlspecialchars($categoria['nombre'] ?? 'Categoria', ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <h2 class="mt-4 text-2xl font-semibold text-slate-900">
                    <?php echo htmlspecialchars($categoria['cantidad'] ?? '0 productos', ENT_QUOTES, 'UTF-8'); ?>
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Vencimiento mas proximo: <span class="font-semibold text-slate-700"><?php echo htmlspecialchars($categoria['vence'] ?? 'Sin dato', ENT_QUOTES, 'UTF-8'); ?></span>
                </p>
            </article>
        <?php endforeach; ?>
    </section>
</section>
