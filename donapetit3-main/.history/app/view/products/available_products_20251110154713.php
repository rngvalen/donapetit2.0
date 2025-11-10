<?php
$productos = $productos ?? [];
$titulo = $titulo ?? 'Productos disponibles';
?>
<section class="max-w-7xl mx-auto px-4 py-8">
  
  <header class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900"><?= htmlspecialchars($titulo) ?></h1>
    <p class="mt-2 text-slate-600">Productos disponibles para solicitar de los donantes cercanos</p>
  </header>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 rounded-xl border border-green-300 bg-green-50 text-green-800 text-sm p-4">
      <p class="font-semibold">✅ <?= htmlspecialchars($_SESSION['success']) ?></p>
      <?php unset($_SESSION['success']); ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="mb-6 rounded-xl border border-red-300 bg-red-50 text-red-800 text-sm p-4">
      <p class="font-semibold">❌ <?= htmlspecialchars($_SESSION['error']) ?></p>
      <?php unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>

  <?php if (empty($productos)): ?>
    <div class="text-center py-16 bg-slate-50 rounded-2xl">
      <p class="text-slate-500 text-lg">No hay productos disponibles en este momento.</p>
      <p class="text-slate-400 text-sm mt-2">Volvé a consultar más tarde.</p>
    </div>
  <?php else: ?>
    
    <!-- Agrupar por categoría -->
    <?php 
    $porCategoria = [];
    foreach ($productos as $p) {
      $cat = $p['categoria'] ?? 'Sin categoría';
      if (!isset($porCategoria[$cat])) {
        $porCategoria[$cat] = [];
      }
      $porCategoria[$cat][] = $p;
    }
    ?>

    <?php foreach ($porCategoria as $categoria => $items): ?>
      <div class="mb-8">
        <h2 class="text-xl font-bold text-slate-800 mb-4 pb-2 border-b-2 border-blue-500">
          <?= htmlspecialchars($categoria) ?>
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($items as $p): ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-xl transition">
              
              <div class="flex items-start justify-between mb-4">
                <div>
                  <h3 class="font-bold text-slate-900 text-lg"><?= htmlspecialchars($p['nom_producto']) ?></h3>
                  <p class="text-sm text-slate-500 mt-1">
                    🏪 <?= htmlspecialchars($p['nombre_donante']) ?>
                  </p>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                  Disponible
                </span>
              </div>
              
              <div class="space-y-2 text-sm text-slate-600 mb-4">
                <p class="flex items-center gap-2">
                  <span class="font-semibold text-slate-900">📦 Cantidad:</span>
                  <span class="text-lg font-bold text-blue-600">
                    <?= $p['cantidad_disponible'] ?> <?= htmlspecialchars($p['unidad']) ?>
                  </span>
                </p>
              </div>

              <div class="pt-4 border-t border-slate-200">
                <button 
                  onclick="solicitarProducto('<?= htmlspecialchars($p['nom_producto']) ?>', '<?= htmlspecialchars($p['nombre_donante']) ?>')"
                  class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition text-sm">
                  Solicitar producto
                </button>
              </div>

            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

  <?php endif; ?>

</section>

<script>
function solicitarProducto(producto, donante) {
  alert('Funcionalidad en desarrollo:\nProducto: ' + producto + '\nDonante: ' + donante);
  // TODO: Implementar sistema de solicitudes
}
</script>