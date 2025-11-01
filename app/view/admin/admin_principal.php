<?php
declare(strict_types=1);

/** @var array<int,array<string,mixed>> $metrics */
/** @var array<int,array<string,mixed>> $statusSummary */
/** @var array<int,array<string,mixed>> $lowStockProducts */
/** @var array<int,array<string,mixed>> $expiringProducts */
/** @var array<int,array<string,mixed>> $recentActivity */
/** @var array<int,array<string,mixed>> $alerts */
$metrics = $metrics ?? [];
$statusSummary = $statusSummary ?? [];
$lowStockProducts = $lowStockProducts ?? [];
$expiringProducts = $expiringProducts ?? [];
$recentActivity = $recentActivity ?? [];
$alerts = $alerts ?? [];

$totalProducts = 0;
foreach ($metrics as $metric) {
    if (($metric['key'] ?? '') === 'total') {
        $totalProducts = (int)($metric['value'] ?? 0);
        break;
    }
}

$metricToneClasses = [
    'neutral' => 'border-slate-200 bg-white text-slate-900',
    'positive' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
    'warning' => 'border-amber-200 bg-amber-50 text-amber-700',
    'alert' => 'border-sky-200 bg-sky-50 text-sky-700',
    'danger' => 'border-red-200 bg-red-50 text-red-700',
];

$statusToneBackground = [
    'positive' => 'bg-emerald-100',
    'warning' => 'bg-amber-100',
    'alert' => 'bg-sky-100',
    'neutral' => 'bg-slate-100',
];

$alertToneClasses = [
    'danger' => 'border-red-200 bg-red-50 text-red-700',
    'alert' => 'border-sky-200 bg-sky-50 text-sky-700',
    'warning' => 'border-amber-200 bg-amber-50 text-amber-700',
];

$alertDotClasses = [
    'danger' => 'bg-red-500',
    'alert' => 'bg-sky-500',
    'warning' => 'bg-amber-500',
];

$hasProducts = $totalProducts > 0;
?>
<section class="py-8" id="admin-dashboard" data-total-products="<?php echo $totalProducts; ?>">
  <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
      <p class="text-xs uppercase tracking-wide text-slate-500">Panel administrativo</p>
      <h1 class="text-3xl font-bold text-slate-900">Resumen general</h1>
      <p class="text-sm text-slate-600">Monitorea stock, vencimientos y actividad reciente desde un unico lugar.</p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="index.php?controller=Producto&action=catalogo"
         class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:border-slate-400 hover:bg-slate-50">
        Ver catalogo
      </a>
      <a href="index.php?controller=Producto&action=create"
         class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-emerald-700">
        Nuevo producto
      </a>
    </div>
  </header>

  <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
    <?php foreach ($metrics as $metric):
      $tone = $metric['tone'] ?? 'neutral';
      $classes = $metricToneClasses[$tone] ?? $metricToneClasses['neutral'];
      $metricKey = (string)($metric['key'] ?? '');
      ?>
      <article class="rounded-2xl border <?php echo htmlspecialchars($classes, ENT_QUOTES, 'UTF-8'); ?> px-5 py-4 shadow-sm"
               data-metric="<?php echo htmlspecialchars($metricKey, ENT_QUOTES, 'UTF-8'); ?>">
        <p class="text-xs uppercase tracking-wide opacity-70">
          <?php echo htmlspecialchars((string)($metric['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <p class="mt-2 text-3xl font-semibold">
          <?php echo htmlspecialchars((string)($metric['value'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <p class="mt-1 text-xs opacity-80">
          <?php echo htmlspecialchars((string)($metric['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
        </p>
      </article>
    <?php endforeach; ?>

    <?php if (empty($metrics)): ?>
      <p class="sm:col-span-2 xl:col-span-5 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-600">
        No hay estadisticas para mostrar. Carga nuevos productos para ver datos en esta seccion.
      </p>
    <?php endif; ?>
  </div>

  <section class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h2 class="text-lg font-semibold text-slate-900">Alertas rapidas</h2>
        <p class="text-sm text-slate-600">Acciones sugeridas segun el estado actual del inventario.</p>
      </div>
      <div class="flex flex-wrap gap-2" role="group" aria-label="Filtro de alertas">
        <button type="button" data-alert-filter="all" data-alert-default="true"
                class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-600 hover:border-slate-300 hover:text-slate-900">
          Todas
        </button>
        <button type="button" data-alert-filter="stock"
                class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-600 hover:border-slate-300 hover:text-slate-900">
          Stock
        </button>
        <button type="button" data-alert-filter="vencimiento"
                class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-600 hover:border-slate-300 hover:text-slate-900">
          Vencimiento
        </button>
      </div>
    </div>

    <div class="mt-4">
      <?php if (!empty($alerts)): ?>
        <ul class="grid gap-3" id="alertList">
          <?php foreach ($alerts as $alert):
            $level = $alert['level'] ?? 'warning';
            $type = $alert['type'] ?? 'stock';
            $alertClass = $alertToneClasses[$level] ?? $alertToneClasses['warning'];
            $dotClass = $alertDotClasses[$level] ?? $alertDotClasses['warning'];
            ?>
            <li class="flex items-center justify-between rounded-xl border <?php echo htmlspecialchars($alertClass, ENT_QUOTES, 'UTF-8'); ?> px-4 py-3 text-sm"
                data-alert-item
                data-alert-type="<?php echo htmlspecialchars((string)$type, ENT_QUOTES, 'UTF-8'); ?>"
                data-alert-level="<?php echo htmlspecialchars((string)$level, ENT_QUOTES, 'UTF-8'); ?>">
              <div class="flex items-center gap-3 text-left text-slate-700">
                <span class="h-2.5 w-2.5 rounded-full <?php echo htmlspecialchars($dotClass, ENT_QUOTES, 'UTF-8'); ?>"></span>
                <p><?php echo htmlspecialchars((string)($alert['message'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
              </div>
              <?php if (!empty($alert['url'])): ?>
                <a href="<?php echo htmlspecialchars((string)$alert['url'], ENT_QUOTES, 'UTF-8'); ?>"
                   class="ml-4 inline-flex items-center text-xs font-semibold uppercase tracking-wide text-slate-700 hover:text-slate-900">
                  Ver detalle
                </a>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <p data-alert-empty
         class="<?php echo empty($alerts) ? '' : 'hidden'; ?> mt-3 text-sm text-slate-500">
        No se encontraron alertas para el filtro seleccionado.
      </p>
    </div>
  </section>

  <section class="mb-8 grid gap-6 lg:grid-cols-2">
    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h2 class="text-lg font-semibold text-slate-900">Stock bajo</h2>
          <p class="text-sm text-slate-600">Productos con cinco unidades o menos disponibles.</p>
        </div>
        <?php if (!empty($lowStockProducts)): ?>
          <label class="relative w-full lg:w-64">
            <span class="sr-only">Buscar en stock bajo</span>
            <input id="lowStockSearch" type="search" placeholder="Buscar producto"
                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500" />
          </label>
        <?php endif; ?>
      </div>

      <?php if (!empty($lowStockProducts)): ?>
        <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
          <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
              <tr>
                <th class="px-4 py-3 text-left">Producto</th>
                <th class="px-4 py-3 text-left">Categoria</th>
                <th class="px-4 py-3 text-right">Cantidad</th>
                <th class="px-4 py-3 text-left">Estado</th>
                <th class="px-4 py-3 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="lowStockTable">
              <?php foreach ($lowStockProducts as $producto):
                $haystack = strtolower(trim(
                    (string)($producto['nombre'] ?? '') . ' ' .
                    (string)($producto['categoria'] ?? '') . ' ' .
                    (string)($producto['estado'] ?? '') . ' ' .
                    (string)($producto['comentarios'] ?? '')
                ));
                ?>
                <tr data-low-stock-row
                    data-search-haystack="<?php echo htmlspecialchars($haystack, ENT_QUOTES, 'UTF-8'); ?>"
                    data-status-key="<?php echo htmlspecialchars((string)($producto['status_key'] ?? 'otros'), ENT_QUOTES, 'UTF-8'); ?>"
                    class="bg-white">
                  <td class="px-4 py-3 font-medium text-slate-900">
                    <?php echo htmlspecialchars((string)($producto['nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                    <?php if (!empty($producto['comentarios'])): ?>
                      <div class="text-xs text-slate-500">
                        <?php echo htmlspecialchars((string)$producto['comentarios'], ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3 text-slate-600">
                    <?php echo htmlspecialchars((string)($producto['categoria'] ?? 'Sin categoria'), ENT_QUOTES, 'UTF-8'); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-semibold text-amber-700">
                    <?php echo htmlspecialchars((string)($producto['cantidad_label'] ?? 'N/D'), ENT_QUOTES, 'UTF-8'); ?>
                    <span class="text-xs font-normal text-slate-500">
                      <?php echo htmlspecialchars((string)($producto['unidad'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?php echo htmlspecialchars((string)($producto['estado_css'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo htmlspecialchars((string)($producto['estado'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-right text-xs">
                    <a href="<?php echo htmlspecialchars((string)($producto['url_show'] ?? '#'), ENT_QUOTES, 'UTF-8'); ?>"
                       class="mr-2 text-sky-600 hover:underline">Ver</a>
                    <a href="<?php echo htmlspecialchars((string)($producto['url_edit'] ?? '#'), ENT_QUOTES, 'UTF-8'); ?>"
                       class="text-emerald-600 hover:underline">Actualizar</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <p id="lowStockEmpty" class="mt-4 hidden text-sm text-slate-500">
          No hay coincidencias con el filtro aplicado.
        </p>
      <?php else: ?>
        <p class="mt-4 rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-sm text-slate-500">
          No hay productos con stock bajo en este momento.
        </p>
      <?php endif; ?>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h2 class="text-lg font-semibold text-slate-900">Por vencer</h2>
          <p class="text-sm text-slate-600">Productos cuya fecha de vencimiento esta proxima.</p>
        </div>
        <?php if (!empty($expiringProducts)): ?>
          <label class="relative w-full lg:w-64">
            <span class="sr-only">Buscar en proximos a vencer</span>
            <input id="expiringSearch" type="search" placeholder="Buscar producto"
                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500" />
          </label>
        <?php endif; ?>
      </div>

      <?php if (!empty($expiringProducts)): ?>
        <div class="mt-4 space-y-3" id="expiringList">
          <?php foreach ($expiringProducts as $producto):
            $haystack = strtolower(trim(
                (string)($producto['nombre'] ?? '') . ' ' .
                (string)($producto['categoria'] ?? '') . ' ' .
                (string)($producto['estado'] ?? '') . ' ' .
                (string)($producto['comentarios'] ?? '')
            ));
            ?>
            <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 md:flex-row md:items-center md:justify-between"
                 data-expiring-row
                 data-search-haystack="<?php echo htmlspecialchars($haystack, ENT_QUOTES, 'UTF-8'); ?>"
                 data-status-key="<?php echo htmlspecialchars((string)($producto['status_key'] ?? 'otros'), ENT_QUOTES, 'UTF-8'); ?>">
              <div>
                <p class="font-semibold text-slate-900">
                  <?php echo htmlspecialchars((string)($producto['nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <p class="text-xs text-slate-500">
                  Categoria: <?php echo htmlspecialchars((string)($producto['categoria'] ?? 'Sin categoria'), ENT_QUOTES, 'UTF-8'); ?>
                </p>
              </div>
              <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full bg-sky-100 px-3 py-1 text-xs font-medium text-sky-700">
                  Vence: <?php echo htmlspecialchars((string)($producto['fecha_vencimiento'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium <?php echo htmlspecialchars((string)($producto['estado_css'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                  <?php echo htmlspecialchars((string)($producto['estado'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <div class="flex items-center gap-2 text-xs">
                  <a href="<?php echo htmlspecialchars((string)($producto['url_show'] ?? '#'), ENT_QUOTES, 'UTF-8'); ?>"
                     class="text-sky-600 hover:underline">Ver</a>
                  <a href="<?php echo htmlspecialchars((string)($producto['url_edit'] ?? '#'), ENT_QUOTES, 'UTF-8'); ?>"
                     class="text-emerald-600 hover:underline">Actualizar</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <p id="expiringEmpty" class="mt-4 hidden text-sm text-slate-500">
          Ningun producto coincide con el filtro.
        </p>
      <?php else: ?>
        <p class="mt-4 rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-sm text-slate-500">
          No hay productos por vencer en los proximos siete dias.
        </p>
      <?php endif; ?>
    </article>
  </section>

  <section class="mb-8 grid gap-6 lg:grid-cols-2">
    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="text-lg font-semibold text-slate-900">Distribucion por estado</h2>
      <p class="mt-1 text-sm text-slate-600">Progreso estimado segun la cantidad de productos en cada estado.</p>

      <?php if (!empty($statusSummary)): ?>
        <ul class="mt-4 space-y-3">
          <?php foreach ($statusSummary as $item):
            $tone = $item['tone'] ?? 'neutral';
            $barClass = $statusToneBackground[$tone] ?? $statusToneBackground['neutral'];
            $percentage = max(0, min(100, (int)($item['percentage'] ?? 0)));
            ?>
            <li>
              <div class="flex items-center justify-between text-sm">
                <span class="font-medium text-slate-800">
                  <?php echo htmlspecialchars((string)($item['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <span class="text-xs text-slate-500">
                  <?php echo htmlspecialchars((string)($item['count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>
                  (<?php echo $percentage; ?>%)
                </span>
              </div>
              <div class="mt-2 h-2 rounded-full bg-slate-100">
                <span class="block h-full rounded-full <?php echo htmlspecialchars($barClass, ENT_QUOTES, 'UTF-8'); ?>"
                      style="width: <?php echo $percentage; ?>%"></span>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="mt-4 text-sm text-slate-500">
          No hay datos suficientes para calcular la distribucion por estado.
        </p>
      <?php endif; ?>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-lg font-semibold text-slate-900">Actividad reciente</h2>
          <p class="text-sm text-slate-600">Ultimos productos cargados o actualizados en el sistema.</p>
        </div>
        <?php if (!empty($recentActivity)): ?>
          <label class="text-xs font-medium text-slate-600">
            Estado
            <select id="activityFilter"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500">
              <option value="all">Todos</option>
              <option value="disponible">Disponibles</option>
              <option value="agotado">Agotados</option>
              <option value="revision">En revision</option>
              <option value="otros">Otros</option>
            </select>
          </label>
        <?php endif; ?>
      </div>

      <?php if (!empty($recentActivity)): ?>
        <ul class="mt-4 space-y-4" id="activityTimeline">
          <?php foreach ($recentActivity as $evento):
            $statusKey = $evento['status_key'] ?? 'otros';
            ?>
            <li class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700"
                data-activity-item
                data-activity-status="<?php echo htmlspecialchars((string)$statusKey, ENT_QUOTES, 'UTF-8'); ?>">
              <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                  <p class="font-semibold text-slate-900">
                    <?php echo htmlspecialchars((string)($evento['title'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                  </p>
                  <p class="text-xs text-slate-500">
                    Categoria: <?php echo htmlspecialchars((string)($evento['categoria'] ?? 'Sin categoria'), ENT_QUOTES, 'UTF-8'); ?>
                  </p>
                </div>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium <?php echo htmlspecialchars((string)($evento['estado_css'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                  <?php echo htmlspecialchars((string)($evento['estado'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                </span>
              </div>
              <div class="mt-2 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                <span><?php echo htmlspecialchars((string)($evento['meta'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                <a href="<?php echo htmlspecialchars((string)($evento['url'] ?? '#'), ENT_QUOTES, 'UTF-8'); ?>"
                   class="text-sky-600 hover:underline">Ver detalle</a>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="mt-4 text-sm text-slate-500">
          Aun no hay actividad reciente registrada.
        </p>
      <?php endif; ?>
      <p id="activityEmpty" class="mt-4 hidden text-sm text-slate-500">
        No hay actividad que coincida con el filtro seleccionado.
      </p>
    </article>
  </section>
</section>

<script defer src="/donapetit2/public/assets/js/admin_principal.js"></script>

