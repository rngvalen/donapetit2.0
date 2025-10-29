<?php
declare(strict_types=1);

/**
 * @var array<int,array<string,string>> $usuarios
 * @var array<int,array<string,string>> $resumenCatalogo
 */
$usuarios = $usuarios ?? [
    ['nombre' => 'Maria Gonzalez', 'rol' => 'Receptor', 'comercial' => 'La Buena Mesa', 'entidad' => 'Comedor Comunitario'],
    ['nombre' => 'Carlos Rodriguez', 'rol' => 'Receptor', 'comercial' => 'Escuela 24', 'entidad' => 'Comedor Escolar'],
    ['nombre' => 'Ana Martinez', 'rol' => 'Donante', 'comercial' => 'Mayorista Central', 'entidad' => 'Supermercado'],
];

$resumenCatalogo = $resumenCatalogo ?? [
    ['categoria' => 'Frutas y verduras', 'cantidad' => '32 productos'],
    ['categoria' => 'Lacteos', 'cantidad' => '18 productos'],
];
?>

<section class="mx-auto w-full max-w-6xl space-y-10 py-8">
    <header class="rounded-3xl bg-gradient-to-r from-emerald-600 to-emerald-400 px-8 py-10 text-white shadow-xl shadow-emerald-600/30">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-white/70">DonAppetit</p>
                <h1 class="mt-2 text-3xl font-semibold sm:text-4xl">Panel de administracion</h1>
            </div>
            <button type="button" class="inline-flex items-center justify-center rounded-2xl bg-white/15 px-4 py-2 text-sm font-semibold uppercase tracking-wide hover:bg-white/25">
                Menu
            </button>
        </div>
    </header>

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-900/5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Gestion de usuarios</h2>
                <p class="mt-1 text-sm text-slate-500">Busca perfiles y gestiona roles dentro de la plataforma.</p>
            </div>
            <div class="flex gap-3">
                <input type="search" placeholder="Buscar usuarios" class="w-60 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                    Buscar
                </button>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nombre</th>
                        <th class="px-5 py-3">Rol</th>
                        <th class="px-5 py-3">Nombre comercial</th>
                        <th class="px-5 py-3">Entidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-medium text-slate-900"><?php echo htmlspecialchars($usuario['nombre'] ?? 'Sin nombre', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-5 py-3"><?php echo htmlspecialchars($usuario['rol'] ?? 'Sin rol', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-5 py-3"><?php echo htmlspecialchars($usuario['comercial'] ?? 'Sin dato', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-5 py-3"><?php echo htmlspecialchars($usuario['entidad'] ?? 'Sin dato', ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-xl shadow-slate-900/5">
        <h2 class="text-2xl font-semibold text-slate-900">Catalogo de productos</h2>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-4">
            <button type="button" class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-500/30 transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                Anadir
            </button>
            <button type="button" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                Filtrar
            </button>
        </div>

        <ul class="mt-6 inline-flex flex-col gap-2 text-left text-sm text-slate-600">
            <?php foreach ($resumenCatalogo as $item): ?>
                <li>
                    <span class="font-semibold text-slate-900"><?php echo htmlspecialchars($item['categoria'] ?? 'Categoria', ENT_QUOTES, 'UTF-8'); ?></span>
                    &middot;
                    <span><?php echo htmlspecialchars($item['cantidad'] ?? '0 productos', ENT_QUOTES, 'UTF-8'); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</section>
