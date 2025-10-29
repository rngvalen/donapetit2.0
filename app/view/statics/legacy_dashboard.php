<?php
declare(strict_types=1);

/**
 * @var array<string,string> $resumen
 * @var array<int,string> $labelsProductos
 * @var array<int,int|float> $valoresProductos
 * @var array<int,string> $labelsFrecuencia
 * @var array<int,int|float> $valoresFrecuencia
 */
$resumen = $resumen ?? [
    'totalDonaciones' => '1,248',
    'alimentosSalvados' => '3,520 kg',
];

$labelsProductos = $labelsProductos ?? ['Pan', 'Frutas', 'Verduras', 'Lacteos', 'Cereales'];
$valoresProductos = $valoresProductos ?? [25, 40, 30, 20, 15];
$labelsFrecuencia = $labelsFrecuencia ?? ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago'];
$valoresFrecuencia = $valoresFrecuencia ?? [20, 25, 18, 30, 28, 26, 24, 35];

$productosLabelsJson = htmlspecialchars(json_encode(array_values($labelsProductos), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
$productosValoresJson = htmlspecialchars(json_encode(array_values($valoresProductos), JSON_NUMERIC_CHECK), ENT_QUOTES, 'UTF-8');
$frecuenciaLabelsJson = htmlspecialchars(json_encode(array_values($labelsFrecuencia), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
$frecuenciaValoresJson = htmlspecialchars(json_encode(array_values($valoresFrecuencia), JSON_NUMERIC_CHECK), ENT_QUOTES, 'UTF-8');
?>

<section class="mx-auto w-full max-w-5xl py-8" id="legacy-dashboard"
    data-productos-labels="<?php echo $productosLabelsJson; ?>"
    data-productos-valores="<?php echo $productosValoresJson; ?>"
    data-frecuencia-labels="<?php echo $frecuenciaLabelsJson; ?>"
    data-frecuencia-valores="<?php echo $frecuenciaValoresJson; ?>">
    <header class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-600">Panel de administracion</p>
        <h1 class="mt-2 text-3xl font-semibold text-slate-900">Vision general</h1>
        <p class="mt-2 max-w-2xl text-sm text-slate-500">
            Analiza el desempeno de tus donaciones y detecta tendencias de productos y frecuencia mensual.
        </p>
    </header>

    <div class="grid gap-6 sm:grid-cols-2">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Total Donaciones</h2>
            <p class="mt-3 text-3xl font-semibold text-slate-900">
                <?php echo htmlspecialchars($resumen['totalDonaciones'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>
            </p>
        </article>
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Alimentos salvados</h2>
            <p class="mt-3 text-3xl font-semibold text-slate-900">
                <?php echo htmlspecialchars($resumen['alimentosSalvados'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>
            </p>
        </article>
    </div>

    <div class="mt-10 grid gap-6 lg:grid-cols-2">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
            <h2 class="text-sm font-semibold text-slate-700">Productos mas frecuentes</h2>
            <canvas id="productosChart" class="mt-6 h-72 w-full"></canvas>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
            <h2 class="text-sm font-semibold text-slate-700">Frecuencia mensual</h2>
            <canvas id="frecuenciaChart" class="mt-6 h-72 w-full"></canvas>
        </article>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script defer src="/donapetit2/public/assets/js/legacy_dashboard.js"></script>
