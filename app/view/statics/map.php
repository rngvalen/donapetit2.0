<?php
declare(strict_types=1);

/**
 * @var array<string,mixed> $mapConfig
 */
$mapConfig = $mapConfig ?? [
    'center' => ['lat' => -34.6037, 'lng' => -58.3816],
    'zoom' => 14,
    'radius' => 2.5,
    'radiusMin' => 0.5,
    'radiusMax' => 5,
    'radiusStep' => 0.5,
    'businesses' => [
        ['nombre' => 'Supermercado El Molino', 'distancia' => '820 m', 'detalle' => '5 productos disponibles', 'estado' => 'online'],
        ['nombre' => 'Supermercado La Esquina', 'distancia' => '950 m', 'detalle' => '10 productos disponibles', 'estado' => 'online'],
        ['nombre' => 'Supermercado Don Pedro', 'distancia' => '1.4 km', 'detalle' => 'Sin stock', 'estado' => 'offline'],
    ],
];

$mapDataJson = htmlspecialchars(json_encode($mapConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
?>

<section class="mx-auto flex w-full max-w-5xl flex-col gap-8 py-8" id="donapp-map"
    data-map-config="<?php echo $mapDataJson; ?>">
    <header>
        <h1 class="text-3xl font-semibold text-slate-900">Mapa de donantes</h1>
        <p class="mt-2 max-w-2xl text-sm text-slate-500">
            Encuentra negocios con productos disponibles cerca de tu ubicacion y ajusta el radio de busqueda segun tus necesidades.
        </p>
    </header>

    <div class="grid gap-8 lg:grid-cols-[2fr,1fr]">
        <div class="space-y-6">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
                <p class="text-sm font-semibold text-slate-700">Negocios cercanos</p>
                <div id="map" class="mt-4 h-96 w-full rounded-2xl bg-slate-100"></div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
                <h2 class="text-lg font-semibold text-slate-900">Filtros rapidos</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    <label class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100">
                        <input type="checkbox" name="filtro_stock" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                        Con stock disponible
                    </label>
                    <label class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100">
                        <input type="checkbox" name="filtro_abierto" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                        Abierto ahora
                    </label>
                    <label class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100">
                        <input type="checkbox" name="filtro_favoritos" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                        Favoritos
                    </label>
                </div>
            </section>
        </div>

        <aside class="flex flex-col gap-6">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
                <h2 class="text-lg font-semibold text-slate-900">Radio de busqueda</h2>
                <p class="mt-2 text-sm text-slate-500">Ajusta el alcance en kilometros para refinar los resultados.</p>

                <div class="mt-4 space-y-4">
                    <input type="range" id="radius" name="radius" class="h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-200 accent-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
                        <span id="radius-value"></span>
                        <button type="button" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-md shadow-indigo-500/30 transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                            Buscar
                        </button>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Negocios</h2>
                    <span class="text-xs font-semibold uppercase text-emerald-600">Actualizado</span>
                </div>

                <ul class="mt-4 space-y-4" id="business-list">
                    <!-- Items generados por JS -->
                </ul>
            </section>
        </aside>
    </div>
</section>

<script defer src="/donapetit2/public/assets/js/map_explorer.js"></script>
