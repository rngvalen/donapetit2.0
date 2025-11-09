<?php
declare(strict_types=1);

$user = htmlspecialchars($userName ?? 'Usuario', ENT_QUOTES, 'UTF-8');

$cards = [
    [
        'title' => 'Buscar donaciones',
        'description' => 'Consulta los productos disponibles y genera una solicitud.',
        'href' => '?controller=Solicitud&action=productosDisponibles',
        'cta' => 'Ver productos',
    ],
    [
        'title' => 'Mapa interactivo',
        'description' => 'Explora donantes cercanos y filtralos por radio.',
        'href' => '?controller=Map&action=index',
        'cta' => 'Abrir mapa',
    ],
    [
        'title' => 'Mi perfil',
        'description' => 'Actualiza responsable, direccion o ubicacion de retiro.',
        'href' => '?controller=Profile&action=completarPerfil',
        'cta' => 'Editar perfil',
    ],
];
?>

<section class="py-10 text-center">
  <p class="text-sm font-semibold uppercase tracking-widest text-brand">Panel del receptor</p>
  <h1 class="mt-2 text-3xl font-extrabold text-slate-900">
    Hola <span class="text-brand/80"><?php echo $user; ?></span>
  </h1>
  <p class="mt-2 text-base text-slate-600">
    Estas son las acciones recomendadas para comenzar a solicitar donaciones.
  </p>
</section>

<section class="mx-auto grid max-w-5xl gap-6 pb-8 sm:grid-cols-2 lg:grid-cols-3">
  <?php foreach ($cards as $card): ?>
    <?php
      $title = htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8');
      $description = htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8');
      $href = htmlspecialchars($card['href'], ENT_QUOTES, 'UTF-8');
      $cta = htmlspecialchars($card['cta'], ENT_QUOTES, 'UTF-8');
    ?>
    <article class="flex h-full flex-col justify-between rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <div>
        <h2 class="text-lg font-semibold text-slate-900"><?php echo $title; ?></h2>
        <p class="mt-2 text-sm text-slate-600"><?php echo $description; ?></p>
      </div>
      <a href="<?php echo $href; ?>"
         class="mt-6 inline-flex items-center justify-center gap-2 rounded-2xl bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand/90">
        <?php echo $cta; ?>
      </a>
    </article>
  <?php endforeach; ?>
</section>
