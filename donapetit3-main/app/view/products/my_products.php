<?php
$productos = $productos ?? [];
$titulo = $titulo ?? 'Mi inventario';
?>
<section class="max-w-7xl mx-auto px-4 py-8">
  
  <header class="mb-8 flex items-center justify-between">
    <div>
      <h1 class="text-3xl font-bold text-slate-900"><?= htmlspecialchars($titulo) ?></h1>
      <p class="mt-2 text-slate-600">Gestión de productos disponibles para donación</p>
    </div>
    <a href="?controller=Producto&action=create"
       class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg">
      + Agregar producto
    </a>
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
    <div class="text-center py-16">
      <p class="text-slate-500 mb-4">No tienes productos en tu inventario.</p>
      <a href="?controller=Producto&action=create"
         class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition">
        Agregar mi primer producto
      </a>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($productos as $p): ?>
        <div class="bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-xl transition">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="font-bold text-slate-900"><?= htmlspecialchars($p['nom_producto']) ?></h3>
              <p class="text-sm text-slate-500"><?= htmlspecialchars($p['categoria']) ?></p>
            </div>
            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
              <?= $p['estado'] ?>
            </span>
          </div>
          
          <div class="space-y-2 text-sm text-slate-600 mb-4">
            <p><strong>Cantidad:</strong> <?= $p['cantidad_disponible'] ?> <?= htmlspecialchars($p['abreviatura']) ?></p>
            <?php if ($p['fecha_vencimiento']): ?>
              <p><strong>Vence:</strong> <?= date('d/m/Y', strtotime($p['fecha_vencimiento'])) ?></p>
            <?php endif; ?>
          </div>

          <div class="flex gap-2">
            <a href="?controller=Producto&action=destroy&id=<?= $p['id_producto_donante'] ?>"
               onclick="return confirm('¿Seguro que deseas eliminar este producto?')"
               class="flex-1 text-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-lg transition text-sm">
              Eliminar
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</section>