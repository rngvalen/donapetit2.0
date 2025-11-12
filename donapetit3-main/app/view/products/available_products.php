<?php
$productos = $productos ?? [];
$titulo = $titulo ?? 'Productos disponibles';

// Agrupar productos por donante
$porDonante = [];
foreach ($productos as $p) {
    $donante = $p['nombre_donante'] ?? 'Donante desconocido';
    $idDonante = $p['id_donante'] ?? 0;
    
    if (!isset($porDonante[$idDonante])) {
        $porDonante[$idDonante] = [
            'nombre' => $donante,
            'productos' => []
        ];
    }
    $porDonante[$idDonante]['productos'][] = $p;
}
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

  <?php if (isset($_SESSION['info'])): ?>
    <div class="mb-6 rounded-xl border border-blue-300 bg-blue-50 text-blue-800 text-sm p-4">
      <p class="font-semibold">ℹ️ <?= htmlspecialchars($_SESSION['info']) ?></p>
      <?php unset($_SESSION['info']); ?>
    </div>
  <?php endif; ?>

  <?php if (empty($productos)): ?>
    <div class="text-center py-16 bg-slate-50 rounded-2xl">
      <p class="text-slate-500 text-lg">No hay productos disponibles en este momento.</p>
      <p class="text-slate-400 text-sm mt-2">Volvé a consultar más tarde.</p>
    </div>
  <?php else: ?>
    
    <!-- Agrupar por donante -->
    <?php foreach ($porDonante as $idDonante => $dataDonante): ?>
      <div class="mb-10 bg-white rounded-3xl shadow-lg border border-slate-200 p-6">
        
        <!-- Header del donante -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-blue-500">
          <div>
            <h2 class="text-2xl font-bold text-slate-800">
              🏪 <?= htmlspecialchars($dataDonante['nombre']) ?>
            </h2>
            <p class="text-sm text-slate-500 mt-1">
              <?= count($dataDonante['productos']) ?> producto(s) disponible(s)
            </p>
          </div>
          
          <!-- Botón para solicitar productos de este donante -->
          <a href="?controller=Solicitud&action=crear&id_donante=<?= $idDonante ?>"
             class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg">
            📋 Crear solicitud
          </a>
        </div>

        <!-- Grid de productos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($dataDonante['productos'] as $p): ?>
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 hover:shadow-lg transition">
              
              <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                  <h3 class="font-bold text-slate-900 text-lg">
                    <?= htmlspecialchars($p['nom_producto']) ?>
                  </h3>
                  <p class="text-sm text-slate-500 mt-1">
                    📦 <?= htmlspecialchars($p['categoria']) ?>
                  </p>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full whitespace-nowrap ml-2">
                  Disponible
                </span>
              </div>
              
              <div class="mt-4 pt-4 border-t border-slate-300">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-slate-600 font-medium">Cantidad:</span>
                  <span class="text-xl font-bold text-blue-600">
                    <?= $p['cantidad_disponible'] ?> <?= htmlspecialchars($p['unidad']) ?>
                  </span>
                </div>
              </div>

            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

  <?php endif; ?>

  <!-- Botón flotante para ver mis solicitudes -->
<div class="fixed bottom-8 right-8">
  <a href="?controller=Solicitud&action=missolicitudes"
     class="flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-full shadow-2xl transition">
    📋 Mis solicitudes
  </a>
</div>

</section>