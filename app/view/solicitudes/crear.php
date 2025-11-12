<?php
$productos = $productos ?? [];
$id_donante = $id_donante ?? 0;
$nombre_donante = $nombre_donante ?? 'Donante';
?>
<section class="max-w-4xl mx-auto px-4 py-8">
  
  <div class="mb-6">
    <a href="?controller=Producto&action=productosDisponibles" 
       class="inline-flex items-center gap-2 text-blue-600 hover:underline text-sm font-medium">
      ← Volver a productos disponibles
    </a>
  </div>

  <header class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900">Crear solicitud</h1>
    <p class="mt-2 text-slate-600">Solicitando productos a: <strong><?= htmlspecialchars($nombre_donante) ?></strong></p>
  </header>

  <div class="bg-white border border-slate-200 rounded-3xl shadow-xl p-8">
    
    <?php if (isset($_SESSION['error'])): ?>
      <div class="mb-6 rounded-xl border border-red-300 bg-red-50 text-red-800 text-sm p-4">
        <p class="font-semibold">❌ Error</p>
        <p><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <?php if (empty($productos)): ?>
      <div class="text-center py-12">
        <p class="text-slate-500">Este donante no tiene productos disponibles en este momento.</p>
        <a href="?controller=Producto&action=productosDisponibles" 
           class="mt-4 inline-block text-blue-600 hover:underline">
          Ver otros donantes
        </a>
      </div>
    <?php else: ?>

      <form action="?controller=Solicitud&action=store" method="post" class="space-y-6">
        
        <input type="hidden" name="id_donante" value="<?= $id_donante ?>">

        <!-- Lista de productos -->
        <div>
          <h3 class="text-lg font-bold text-slate-900 mb-4">Seleccionar productos</h3>
          
          <div class="space-y-3">
            <?php foreach ($productos as $p): ?>
              <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                
                <div class="flex-1">
                  <h4 class="font-semibold text-slate-900"><?= htmlspecialchars($p['nom_producto']) ?></h4>
                  <p class="text-sm text-slate-500">
                    📦 <?= htmlspecialchars($p['categoria']) ?> • 
                    Disponible: <?= $p['cantidad_disponible'] ?> <?= htmlspecialchars($p['abreviatura']) ?>
                  </p>
                </div>

                <div class="flex items-center gap-2">
                  <label class="text-sm text-slate-600">Cantidad:</label>
                  <input 
                    type="number" 
                    name="productos[<?= $p['id_producto_donante'] ?>]" 
                    min="0" 
                    max="<?= $p['cantidad_disponible'] ?>"
                    value="0"
                    class="w-24 rounded-lg border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition">
                  <span class="text-sm text-slate-500"><?= htmlspecialchars($p['abreviatura']) ?></span>
                </div>

              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Comentarios -->
        <div>
          <label for="comentarios" class="block text-sm font-semibold text-slate-900 mb-2">
            Comentarios u observaciones (opcional)
          </label>
          <textarea 
            id="comentarios" 
            name="comentarios" 
            rows="4"
            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition"
            placeholder="Ej: Necesito estos productos para el comedor del barrio..."></textarea>
        </div>

        <!-- Botones -->
        <div class="pt-4 flex gap-3">
          <button type="submit"
                  class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg">
            Enviar solicitud
          </button>

          <a href="?controller=Producto&action=productosDisponibles"
             class="px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold rounded-xl transition">
            Cancelar
          </a>
        </div>

      </form>

    <?php endif; ?>
  </div>

</section>