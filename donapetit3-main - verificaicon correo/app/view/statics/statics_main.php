<?php
declare(strict_types=1);

/** @var int $totalDonaciones */
/** @var int $alimentosSalvados */
/** @var int $totalCantidadDonada */
/** @var array{labels:array<int,string>,values:array<int,int>} $topProductos */
/** @var array{labels:array<int,string>,values:array<int,int>} $frecuenciaMensual */

$totalDonaciones = $totalDonaciones ?? 0;
$alimentosSalvados = $alimentosSalvados ?? 0;
$totalCantidadDonada = $totalCantidadDonada ?? 0;
$topProductos = $topProductos ?? ['labels' => ['Sin datos'], 'values' => [0]];
$frecuenciaMensual = $frecuenciaMensual ?? ['labels' => ['Sin datos'], 'values' => [0]];

$topProductos['labels'] = array_values($topProductos['labels']);
$topProductos['values'] = array_map('intval', array_values($topProductos['values']));
$frecuenciaMensual['labels'] = array_values($frecuenciaMensual['labels']);
$frecuenciaMensual['values'] = array_map('intval', array_values($frecuenciaMensual['values']));

$totalDonacionesLabel = number_format((float) $totalDonaciones, 0, ',', '.');
$alimentosSalvadosLabel = number_format((float) $alimentosSalvados, 0, ',', '.');

$productosLabelsJson = json_encode($topProductos['labels'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$productosValoresJson = json_encode($topProductos['values'], JSON_NUMERIC_CHECK);
$frecuenciaLabelsJson = json_encode($frecuenciaMensual['labels'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$frecuenciaValoresJson = json_encode($frecuenciaMensual['values'], JSON_NUMERIC_CHECK);

$productosLabelsJson = $productosLabelsJson !== false ? $productosLabelsJson : '[]';
$productosValoresJson = $productosValoresJson !== false ? $productosValoresJson : '[]';
$frecuenciaLabelsJson = $frecuenciaLabelsJson !== false ? $frecuenciaLabelsJson : '[]';
$frecuenciaValoresJson = $frecuenciaValoresJson !== false ? $frecuenciaValoresJson : '[]';
?>

<section aria-labelledby="titulo-panel" class="mx-auto w-full max-w-5xl py-8 font-sans">
  <h1 id="titulo-panel" class="mb-8 text-center text-2xl font-semibold text-[#2b473a] sm:text-3xl">
    Panel de Administracion
  </h1>

  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    <article class="rounded-2xl bg-white p-6 shadow transition-transform hover:-translate-y-0.5 hover:shadow-lg">
      <h2 class="m-0 text-sm font-semibold text-[#416d56]">Total Donaciones</h2>
      <p class="mt-2 text-2xl font-semibold text-slate-900">
        <?php echo htmlspecialchars($totalDonacionesLabel, ENT_QUOTES, 'UTF-8'); ?>
      </p>
    </article>

    <article class="rounded-2xl bg-white p-6 shadow transition-transform hover:-translate-y-0.5 hover:shadow-lg">
      <h2 class="m-0 text-sm font-semibold text-[#416d56]">Alimentos Salvados</h2>
      <p class="mt-2 text-2xl font-semibold text-slate-900">
        <?php echo htmlspecialchars($alimentosSalvadosLabel, ENT_QUOTES, 'UTF-8'); ?> unidades
      </p>
    </article>
  </div>

  <div class="mt-12 flex flex-col items-center gap-8">
    <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow">
      <h2 class="mb-4 text-base font-semibold text-[#2b473a]">Productos mas frecuentes</h2>
      <canvas id="productosChart" class="h-64 w-full"></canvas>
    </div>

    <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow">
      <h2 class="mb-4 text-base font-semibold text-[#2b473a]">Frecuencia mensual</h2>
      <canvas id="frecuenciaChart" class="h-64 w-full"></canvas>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var productosCanvas = document.getElementById('productosChart');
    var frecuenciaCanvas = document.getElementById('frecuenciaChart');

    if (typeof Chart === 'undefined' || !productosCanvas || !frecuenciaCanvas) {
      return;
    }

    var productosLabels = <?php echo $productosLabelsJson; ?>;
    var productosData = <?php echo $productosValoresJson; ?>;
    var frecuenciaLabels = <?php echo $frecuenciaLabelsJson; ?>;
    var frecuenciaData = <?php echo $frecuenciaValoresJson; ?>;

    if (!Array.isArray(productosLabels) || productosLabels.length === 0) {
      productosLabels = ['Sin datos'];
      productosData = [0];
    }

    if (!Array.isArray(productosData) || productosData.length !== productosLabels.length) {
      productosData = productosLabels.map(function () { return 0; });
    }

    var baseColors = ['#3d538f', '#212E50', '#161D30', '#2F3953', '#1B2B55'];
    var productosColors = productosLabels.map(function (_, index) {
      return baseColors[index % baseColors.length];
    });

    new Chart(productosCanvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: productosLabels,
        datasets: [{
          label: 'Cantidad',
          data: productosData,
          backgroundColor: productosColors,
          borderRadius: 6,
          barThickness: 25
        }]
      },
      options: {
        indexAxis: 'y',
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: {
            beginAtZero: true,
            grid: { display: false }
          },
          y: {
            grid: { display: false }
          }
        }
      }
    });

    if (!Array.isArray(frecuenciaLabels) || frecuenciaLabels.length === 0) {
      frecuenciaLabels = ['Sin datos'];
      frecuenciaData = [0];
    }

    if (!Array.isArray(frecuenciaData) || frecuenciaData.length !== frecuenciaLabels.length) {
      frecuenciaData = frecuenciaLabels.map(function () { return 0; });
    }

    new Chart(frecuenciaCanvas.getContext('2d'), {
      type: 'line',
      data: {
        labels: frecuenciaLabels,
        datasets: [{
          label: 'Donaciones',
          data: frecuenciaData,
          borderColor: '#3d538f',
          backgroundColor: 'rgba(65, 109, 86, 0.12)',
          tension: 0.4,
          fill: true,
          pointRadius: 3
        }]
      },
      options: {
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#e2e8f0' }
          },
          x: {
            grid: { display: false }
          }
        }
      }
    });
  });
</script>
