<?php
// app/view/products/product_load.php
// Espera opcionalmente $unidades, $nombresDisponibles y $categorias
$unidades = $unidades ?? [];
$nombresDisponibles = $nombresDisponibles ?? [];
$categorias = $categorias ?? [];
?>
<section class="max-w-3xl mx-auto px-4 py-6">
  <div class="mb-4 flex items-center justify-between">
    <a href="index.php?controller=Producto&action=index" class="inline-flex items-center gap-2 text-brand hover:underline text-sm">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7" />
      </svg>
      Volver al listado
    </a>
    <a href="index.php?controller=Admin&action=principal"
       class="text-xs font-semibold uppercase tracking-wide text-slate-500 hover:text-brand">
      Gestionar catalogo
    </a>
  </div>

  <header class="mb-4">
    <h1 class="text-xl font-semibold text-slate-900">Registrar disponibilidad</h1>
    <p class="text-sm text-slate-600">Selecciona un producto del catalogo existente. Los nuevos productos se crean desde el panel de administracion.</p>
  </header>

  <div class="bg-white border rounded-xl shadow-sm p-4 md:p-6">
    <div id="errors" class="hidden mb-4 rounded-lg border border-red-300 bg-red-50 text-red-800 text-sm p-3"></div>

    <form id="loadProductForm"
          action="/donapetit2/public/index.php?controller=Producto&action=store"
          method="post"
          novalidate
          class="space-y-5">

      <div>
        <label for="nombre" class="block text-sm font-medium mb-1">Producto <span class="text-red-600">*</span></label>
        <select id="nombre"
                name="nombre"
                class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500">
          <option value="">Seleccionar producto</option>
          <?php foreach ($nombresDisponibles as $nombre): ?>
            <option value="<?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>">
              <?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (empty($nombresDisponibles)): ?>
          <p class="mt-2 text-xs text-amber-600">Aun no hay productos en el catalogo. Agrega nuevos desde el panel de administracion.</p>
        <?php endif; ?>
      </div>

      <div>
        <label for="categoria" class="block text-sm font-medium mb-1">Categoria <span class="text-red-600">*</span></label>
        <select id="categoria" name="categoria"
                class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500">
          <option value="">Seleccionar categoria</option>
          <?php foreach ($categorias as $id => $nombre): ?>
            <option value="<?php echo htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>">
              <?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (empty($categorias)): ?>
          <p class="mt-2 text-xs text-amber-600">No hay categorias disponibles. Agrega nuevas desde administracion.</p>
        <?php endif; ?>
      </div>

      <div>
        <label for="unidad" class="block text-sm font-medium mb-1">
          Unidad o presentacion <span class="text-red-600">*</span>
        </label>
        <select id="unidad" name="unidad"
                class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500">
          <option value="">Seleccionar unidad</option>
          <?php foreach ($unidades as $u): ?>
            <option value="<?php echo htmlspecialchars($u['abreviatura'], ENT_QUOTES, 'UTF-8'); ?>">
              <?php echo htmlspecialchars($u['nombre_unidad'] . ' (' . $u['abreviatura'] . ')', ENT_QUOTES, 'UTF-8'); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label for="cantidad" class="block text-sm font-medium mb-1">
          Cantidad disponible <span class="text-red-600">*</span>
        </label>
        <input id="cantidad" name="cantidad" type="number" inputmode="numeric" min="1" step="1"
               class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500"
               placeholder="Ej.: 10" />
        <p class="text-xs text-zinc-500 mt-1">Solo enteros positivos.</p>
      </div>

      <div>
        <label for="vencimiento" class="block text-sm font-medium mb-1">
          Fecha de vencimiento <span class="text-red-600">*</span>
        </label>
        <input id="vencimiento" name="vencimiento" type="date"
               class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500" />
      </div>

      <div>
        <label for="comentarios" class="block text-sm font-medium mb-1">Comentarios (opcional)</label>
        <textarea id="comentarios" name="comentarios" rows="4"
                  class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500"
                  placeholder="Informacion adicional, por ejemplo marca o empaque"></textarea>
      </div>

      <div class="pt-2 flex flex-col sm:flex-row gap-3">
        <button id="guardarBtn" type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 font-medium">
          Guardar producto
        </button>

        <button type="button"
                onclick="history.back()"
                class="inline-flex items-center justify-center rounded-lg bg-zinc-200 hover:bg-zinc-300 text-zinc-800 px-4 py-2 font-medium">
          Cancelar
        </button>
      </div>
    </form>
  </div>
</section>

<script src="/donapetit2/public/assets/js/product_load.js"></script>
