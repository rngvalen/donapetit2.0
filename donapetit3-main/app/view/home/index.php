<?php
$user = htmlspecialchars($userName ?? 'Usuario', ENT_QUOTES, 'UTF-8');

// Obtener contador de solicitudes pendientes
$notificaciones = 0;
if (isset($_SESSION['user']) && $_SESSION['user']['rol'] === 'donante') {
    try {
        require_once __DIR__ . '/../../model/Solicitud.php';
        $solicitudModel = new Solicitud();
        $notificaciones = $solicitudModel->contarSolicitudesPendientes((int)$_SESSION['user']['id']);
    } catch (\Throwable $e) {
        error_log("Error al obtener notificaciones: " . $e->getMessage());
    }
}

$cards = [
    [
        'title' => 'Cargar producto',
        'description' => 'Agrega nuevas donaciones indicando stock, vencimiento y comentarios.',
        'href' => 'index.php?controller=Producto&action=create',
        'cta' => 'Ir al formulario',
        'icon' => '➕',
        'badge' => null,
        'highlight' => false,
    ],
    [
        'title' => 'Mis productos',
        'description' => 'Consulta el estado, actualiza datos y gestiona tu inventario.',
        'href' => 'index.php?controller=Producto&action=index',
        'cta' => 'Ver listado',
        'icon' => '📦',
        'badge' => null,
        'highlight' => false,
    ],
    [
        'title' => 'Solicitudes recibidas',
        'description' => 'Revisa y aprueba las solicitudes de receptores que necesitan tus productos.',
        'href' => 'index.php?controller=Solicitud&action=solicitudesrecibidas',
        'cta' => 'Ver solicitudes',
        'icon' => '📋',
        'badge' => $notificaciones > 0 ? $notificaciones : null,
        'highlight' => $notificaciones > 0,
    ],
    [
        'title' => 'Estadísticas',
        'description' => 'Consulta métricas sobre tus donaciones, productos más donados y frecuencia mensual.',
        'href' => 'index.php?controller=Home&action=statics',
        'cta' => 'Ver dashboard',
        'icon' => '📊',
        'badge' => null,
        'highlight' => false,
    ],
    [
        'title' => 'Mapa de donantes',
        'description' => 'Explora negocios cercanos con donaciones disponibles en un mapa interactivo.',
        'href' => 'index.php?controller=Map&action=index',
        'cta' => 'Abrir mapa',
        'icon' => '🗺️',
        'badge' => null,
        'highlight' => false,
    ],
];
?>
<section class="py-10 text-center">
  <h1 class="text-3xl font-extrabold text-slate-900">
    Bienvenido <span class="italic text-brand/90"><?php echo $user; ?></span>
  </h1>
  <p class="mt-3 text-slate-600">Gestiona tus donaciones y hace seguimiento de los productos disponibles.</p>
  
  <!-- Banner de notificaciones -->
  <?php if ($notificaciones > 0): ?>
    <div class="mt-6 mx-auto max-w-2xl">
      <div class="rounded-xl border-2 border-amber-400 bg-amber-50 p-4 shadow-lg">
        <div class="flex items-center justify-center gap-3">
          <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
          </div>
          <div class="flex-1 text-left">
            <p class="text-sm font-bold text-amber-900">
              <?= $notificaciones ?> solicitud<?= $notificaciones > 1 ? 'es' : '' ?> pendiente<?= $notificaciones > 1 ? 's' : '' ?>
            </p>
            <p class="text-xs text-amber-700">
              Tienes solicitudes esperando tu respuesta
            </p>
          </div>
          <a href="index.php?controller=Solicitud&action=solicitudesrecibidas"
             class="flex-shrink-0 rounded-lg bg-amber-600 hover:bg-amber-700 px-4 py-2 text-sm font-semibold text-white transition">
            Ver ahora
          </a>
        </div>
      </div>
    </div>
  <?php endif; ?>
</section>

<section class="mx-auto grid max-w-6xl gap-6 py-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
  <?php foreach ($cards as $card): ?>
    <?php
    $title = htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8');
    $href = htmlspecialchars($card['href'], ENT_QUOTES, 'UTF-8');
    $cta = htmlspecialchars($card['cta'], ENT_QUOTES, 'UTF-8');
    $icon = $card['icon'] ?? '';
    $badge = $card['badge'] ?? null;
    $highlight = $card['highlight'] ?? false;
    
    // Clases dinámicas según si hay highlight
    $cardClass = $highlight 
      ? 'border-2 border-amber-400 bg-amber-50 shadow-amber-200' 
      : 'border border-slate-200 bg-white';
    
    $buttonClass = $highlight
      ? 'bg-amber-600 hover:bg-amber-700 focus-visible:ring-amber-600'
      : 'bg-brand hover:bg-brand/90 focus-visible:ring-brand/80';
    ?>
    <article class="relative flex h-full flex-col justify-between rounded-2xl <?= $cardClass ?> p-6 text-left shadow-sm transition hover:shadow-md">
      
      <!-- Badge de notificaciones -->
      <?php if ($badge !== null): ?>
        <span class="absolute -top-3 -right-3 flex h-10 w-10">
          <span class="absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75 animate-ping"></span>
          <span class="relative inline-flex h-10 w-10 items-center justify-center rounded-full bg-red-600 text-sm font-bold text-white shadow-lg">
            <?= $badge > 9 ? '9+' : $badge ?>
          </span>
        </span>
      <?php endif; ?>

      <div>
        <!-- Icono + título -->
        <div class="flex items-center gap-2 mb-2">
          <?php if ($icon): ?>
            <span class="text-2xl"><?= $icon ?></span>
          <?php endif; ?>
          <h2 class="text-lg font-semibold text-slate-900"><?php echo $title; ?></h2>
        </div>
        <p class="mt-2 text-sm text-slate-600"><?php echo $description; ?></p>
      </div>
      
      <a href="<?php echo $href; ?>"
        class="mt-6 inline-flex items-center justify-center gap-2 rounded-lg <?= $buttonClass ?> px-3 py-2 text-sm font-medium text-white focus:outline-none focus-visible:ring-2">
        <?php echo $cta; ?>
      </a>
    </article>
  <?php endforeach; ?>
</section>

<style>
  @keyframes ping {
    75%, 100% {
      transform: scale(1.5);
      opacity: 0;
    }
  }
  .animate-ping {
    animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
  }
</style>