<?php
$unidades = $unidades ?? [];
$nombresDisponibles = $nombresDisponibles ?? [];
$categorias = $categorias ?? [];
?>
<section class="max-w-3xl mx-auto px-4 py-8">
  
  <!-- Header con navegación -->
  <div class="mb-6 flex items-center justify-between">
    <a href="?controller=Producto&action=index" 
       class="inline-flex items-center gap-2 text-brand hover:underline text-sm font-medium transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      Volver al listado
    </a>
  </div>

  <!-- Título -->
  <header class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900">Registrar disponibilidad</h1>
    <p class="mt-2 text-slate-600">Seleccioná un producto del catálogo y completá la información de stock.</p>
  </header>

  <!-- Formulario -->
  <div class="bg-white border border-slate-200 rounded-3xl shadow-xl p-8">
    
    <!-- Mensajes de error (si existen en sesión) -->
    <?php if (isset($_SESSION['error'])): ?>
      <div class="mb-6 rounded-xl border border-red-300 bg-red-50 text-red-800 text-sm p-4">
        <p class="font-semibold">❌ Error</p>
        <p><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <!-- Mensajes de éxito -->
    <?php if (isset($_SESSION['success'])): ?>
      <div class="mb-6 rounded-xl border border-green-300 bg-green-50 text-green-800 text-sm p-4">
        <p class="font-semibold">✅ Éxito</p>
        <p><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>

    <form action="?controller=Producto&action=store" method="post" class="space-y-6">

      <!-- Producto -->
      <div>
        <label for="nombre" class="block text-sm font-semibold text-slate-900 mb-2">
          Producto <span class="text-red-600">*</span>
        </label>
        <select id="nombre" name="nombre" required
                class="w-full rounded-xl border-slate-300 focus:border-brand focus:ring focus:ring-brand/20 transition">
          <option value="">Seleccionar producto</option>
          <?php foreach ($nombresDisponibles as $nombre): ?>
            <option value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (empty($nombresDisponibles)): ?>
          <p class="mt-2 text-xs text-amber-600">
            ⚠️ Aún no hay productos en el catálogo. Contactá al administrador.
          </p>
        <?php endif; ?>
      </div>

      <!-- Categoría -->
      <div>
        <label for="categoria" class="block text-sm font-semibold text-slate-900 mb-2">
          Categoría <span class="text-red-600">*</span>
        </label>
        <select id="categoria" name="categoria" required
                class="w-full rounded-xl border-slate-300 focus:border-brand focus:ring focus:ring-brand/20 transition">
          <option value="">Seleccionar categoría</option>
          <?php foreach ($categorias as $id => $nombreCat): ?>
            <option value="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($nombreCat, ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (empty($categorias)): ?>
          <p class="mt-2 text-xs text-amber-600">
            ⚠️ No hay categorías disponibles.
          </p>
        <?php endif; ?>
      </div>

      <!-- Unidad -->
      <div>
        <label for="unidad" class="block text-sm font-semibold text-slate-900 mb-2">
          Unidad o presentación <span class="text-red-600">*</span>
        </label>
        <select id="unidad" name="unidad" required
                class="w-full rounded-xl border-slate-300 focus:border-brand focus:ring focus:ring-brand/20 transition">
          <option value="">Seleccionar unidad</option>
          <?php foreach ($unidades as $u): ?>
            <option value="<?= htmlspecialchars($u['abreviatura'], ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($u['nombre_unidad'] . ' (' . $u['abreviatura'] . ')', ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Cantidad -->
      <div>
        <label for="cantidad" class="block text-sm font-semibold text-slate-900 mb-2">
          Cantidad disponible <span class="text-red-600">*</span>
        </label>
        <input id="cantidad" name="cantidad" type="number" min="1" step="1" required
               class="w-full rounded-xl border-slate-300 focus:border-brand focus:ring focus:ring-brand/20 transition"
               placeholder="Ej: 10" />
        <p class="text-xs text-slate-500 mt-1">Solo números enteros positivos.</p>
      </div>

      <!-- Fecha de vencimiento -->
      <div>
        <label for="vencimiento" class="block text-sm font-semibold text-slate-900 mb-2">
          Fecha de vencimiento <span class="text-red-600">*</span>
        </label>
        <input id="vencimiento" name="vencimiento" type="date" required
               min="<?= date('Y-m-d') ?>"
               class="w-full rounded-xl border-slate-300 focus:border-brand focus:ring focus:ring-brand/20 transition" />
        <p class="text-xs text-slate-500 mt-1">Debe ser una fecha futura.</p>
      </div>

      <!-- Comentarios -->
      <div>
        <label for="comentarios" class="block text-sm font-semibold text-slate-900 mb-2">
          Comentarios (opcional)
        </label>
        <textarea id="comentarios" name="comentarios" rows="4"
                  class="w-full rounded-xl border-slate-300 focus:border-brand focus:ring focus:ring-brand/20 transition resize-none"
                  placeholder="Información adicional, por ejemplo marca o empaque"></textarea>
      </div>

      <!-- Botones -->
      <div class="pt-4 flex flex-col sm:flex-row gap-3">
        <button type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-brand hover:bg-brand/90 text-white px-6 py-3 font-semibold transition shadow-lg hover:shadow-xl">
          Guardar producto
        </button>

        <a href="?controller=Producto&action=index"
           class="inline-flex items-center justify-center rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 px-6 py-3 font-semibold transition">
          Cancelar
        </a>
      </div>

    </form>
  </div>

</section>

<style>
  .bg-brand { background-color: #0F1629; }
  .text-brand { color: #0F1629; }
  .border-brand { border-color: #0F1629; }
  .focus\:border-brand:focus { border-color: #0F1629; }
  .focus\:ring-brand\/20:focus { --tw-ring-color: rgba(15, 22, 41, 0.2); }
  .hover\:bg-brand\/90:hover { background-color: rgba(15, 22, 41, 0.9); }
</style>