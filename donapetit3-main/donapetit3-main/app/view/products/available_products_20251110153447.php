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
                    <?= $p['cantidad_disponible'] ?> <?= htmlspecialchars($p['abreviatura']) ?>
                  </span>
                </p>
              </div>

              <div class="pt-4 border-t border-slate-200">
                <button 
                  onclick="abrirModal(<?= $p['id_producto_donante'] ?>, '<?= htmlspecialchars($p['nom_producto'], ENT_QUOTES) ?>', <?= $p['cantidad_disponible'] ?>, '<?= htmlspecialchars($p['abreviatura'], ENT_QUOTES) ?>')"
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

<!-- Modal de solicitud -->
<div id="modalSolicitud" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
  <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
    <h3 class="text-2xl font-bold text-slate-900 mb-6">Solicitar producto</h3>
    
    <form action="?controller=Solicitud&action=solicitar" method="POST">
      <input type="hidden" name="id_producto_donante" id="modal_id_producto">
      
      <div class="mb-4">
        <label class="block text-sm font-semibold text-slate-700 mb-2">Producto</label>
        <p class="text-slate-900 font-medium" id="modal_nombre_producto"></p>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-semibold text-slate-700 mb-2">Disponible</label>
        <p class="text-blue-600 font-bold text-lg" id="modal_cantidad_disponible"></p>
      </div>
      
      <div class="mb-4">
        <label for="cantidad" class="block text-sm font-semibold text-slate-700 mb-2">
          Cantidad a solicitar <span class="text-red-600">*</span>
        </label>
        <input 
          type="number" 
          name="cantidad" 
          id="cantidad" 
          min="1" 
          required
          class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
          placeholder="Ej: 5">
      </div>
      
      <div class="mb-6">
        <label for="observacion" class="block text-sm font-semibold text-slate-700 mb-2">
          Observaciones (opcional)
        </label>
        <textarea 
          name="observacion" 
          id="observacion" 
          rows="3"
          class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
          placeholder="Información adicional..."></textarea>
      </div>
      
      <div class="flex gap-3">
        <button 
          type="submit"
          class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg">
          Confirmar solicitud
        </button>
        <button 
          type="button"
          onclick="cerrarModal()"
          class="px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl transition">
          Cancelar
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function abrirModal(idProducto, nombreProducto, cantidadDisponible, unidad) {
  document.getElementById('modal_id_producto').value = idProducto;
  document.getElementById('modal_nombre_producto').textContent = nombreProducto;
  document.getElementById('modal_cantidad_disponible').textContent = cantidadDisponible + ' ' + unidad;
  document.getElementById('cantidad').max = cantidadDisponible;
  
  const modal = document.getElementById('modalSolicitud');
  modal.style.display = 'flex';
  modal.classList.remove('hidden');
}

function cerrarModal() {
  const modal = document.getElementById('modalSolicitud');
  modal.style.display = 'none';
  modal.classList.add('hidden');
  
  document.getElementById('cantidad').value = '';
  document.getElementById('observacion').value = '';
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalSolicitud').addEventListener('click', function(e) {
  if (e.target === this) {
    cerrarModal();
  }
});

// Cerrar modal con tecla ESC
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    cerrarModal();
  }
});
</script>