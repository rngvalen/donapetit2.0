<?php
$productos = $productos ?? [];
?>
<section class="max-w-3xl mx-auto px-4 py-8">
  
  <div class="mb-6">
    <a href="?controller=Producto&action=misProductos" 
       class="inline-flex items-center gap-2 text-blue-600 hover:underline text-sm font-medium">
      ← Volver a mi inventario
    </a>
  </div>

  <header class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900">Cargar producto</h1>
    <p class="mt-2 text-slate-600">Seleccioná un producto del catálogo y registrá tu stock disponible.</p>
  </header>

  <div class="bg-white border border-slate-200 rounded-3xl shadow-xl p-8">
    
    <?php if (isset($_SESSION['error'])): ?>
      <div class="mb-6 rounded-xl border border-red-300 bg-red-50 text-red-800 text-sm p-4">
        <p class="font-semibold">❌ Error</p>
        <p><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="mb-6 rounded-xl border border-green-300 bg-green-50 text-green-800 text-sm p-4">
        <p class="font-semibold">✅ Éxito</p>
        <p><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>

    <form action="?controller=Producto&action=store" method="post" class="space-y-6">

      <!-- Seleccionar producto -->
      <div>
        <label for="id_catalogo" class="block text-sm font-semibold text-slate-900 mb-2">
          Producto <span class="text-red-600">*</span>
        </label>
        <select id="id_catalogo" name="id_catalogo" required
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition">
          <option value="">Seleccionar producto</option>
          <?php 
          $categoriaActual = '';
          foreach ($productos as $p): 
            if ($categoriaActual !== $p['categoria']) {
              if ($categoriaActual !== '') echo '</optgroup>';
              echo '<optgroup label="' . htmlspecialchars($p['categoria']) . '">';
              $categoriaActual = $p['categoria'];
            }
          ?>
            <option value="<?= $p['id_catalogo'] ?>">
              <?= htmlspecialchars($p['nom_producto']) ?> (<?= htmlspecialchars($p['abreviatura']) ?>)
            </option>
          <?php endforeach; ?>
          <?php if ($categoriaActual !== '') echo '</optgroup>'; ?>
        </select>
      </div>

      <!-- Cantidad -->
      <div>
        <label for="cantidad" class="block text-sm font-semibold text-slate-900 mb-2">
          Cantidad disponible <span class="text-red-600">*</span>
        </label>
        <input id="cantidad" name="cantidad" type="number" min="1" step="1" required
               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition"
               placeholder="Ej: 10" />
      </div>

      <!-- Fecha de vencimiento (opcional) -->
      <div>
        <label for="fecha_vencimiento" class="block text-sm font-semibold text-slate-900 mb-2">
          Fecha de vencimiento (opcional)
        </label>
        <input id="fecha_vencimiento" name="fecha_vencimiento" type="date"
               min="<?= date('Y-m-d') ?>"
               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition" />
      </div>

      <!-- Botones -->
      <div class="pt-4 flex gap-3">
        <button type="submit"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg">
          Agregar a mi inventario
        </button>

        <a href="?controller=Producto&action=misProductos"
           class="px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold rounded-xl transition">
          Cancelar
        </a>
      </div>

    </form>
  </div>

</section>