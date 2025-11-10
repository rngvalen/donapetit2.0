<?php
$user = htmlspecialchars($userName ?? 'Usuario', ENT_QUOTES, 'UTF-8');

$cards = [
    [
        'title' => 'Encargar productos',
        'description' => 'Solicita donaciones disponibles de productos que necesites.',
        'href' => 'index.php?controller=Producto&action=productosDisponibles', // ✅ Corregido
        'cta' => 'Ver disponibles',
    ],
    [
        'title' => 'Mis solicitudes',
        'description' => 'Consulta el estado de tus pedidos y gestiona reservas.',
        'href' => 'index.php?controller=Solicitud&action=missolicitudes', // ✅ Corregido
        'cta' => 'Ver solicitudes',
    ],
    [
        'title' => 'Mapa de donantes',
        'description' => 'Explora negocios cercanos con donaciones disponibles en un mapa interactivo.',
        'href' => 'index.php?controller=Map&action=index',
        'cta' => 'Abrir mapa',
    ],
];
?>
<section class="py-10 text-center">
  <h1 class="text-3xl font-extrabold text-slate-900">
    Bienvenido <span class="italic text-brand/90"><?php echo $user; ?></span>
  </h1>
  <p class="mt-3 text-slate-600">Encuentra y solicita donaciones de alimentos disponibles.</p>
</section>

<section class="mx-auto grid max-w-5xl gap-6 py-8 sm:grid-cols-2 lg:grid-cols-3">
  <?php foreach ($cards as $card): ?>
    <?php
    $title = htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8');
    $href = htmlspecialchars($card['href'], ENT_QUOTES, 'UTF-8');
    $cta = htmlspecialchars($card['cta'], ENT_QUOTES, 'UTF-8');
    ?>
    <article class="flex h-full flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 text-left shadow-sm transition hover:shadow-md">
      <div>
        <h2 class="text-lg font-semibold text-slate-900"><?php echo $title; ?></h2>
        <p class="mt-2 text-sm text-slate-600"><?php echo $description; ?></p>
      </div>
      <a href="<?php echo $href; ?>"
         class="mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-brand px-3 py-2 text-sm font-medium text-white hover:bg-brand/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand/80">
        <?php echo $cta; ?>
      </a>
    </article>
  <?php endforeach; ?>
</section>