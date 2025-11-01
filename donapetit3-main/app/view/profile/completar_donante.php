<?php
$user = htmlspecialchars($userName ?? 'Usuario', ENT_QUOTES, 'UTF-8');
?>
<section class="py-10">
  <div class="mx-auto max-w-2xl">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl">
      
      <div class="text-center mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900">Completa tu perfil</h1>
        <p class="mt-2 text-sm text-slate-600">Necesitamos algunos datos adicionales para tu perfil de donante</p>
      </div>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
          <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <form method="post" action="?controller=Profile&action=guardarDonante" class="space-y-6">
        
        <div>
          <label for="nombre_comercial" class="text-sm font-medium text-slate-700">Nombre Comercial *</label>
          <input 
            type="text" 
            id="nombre_comercial" 
            name="nombre_comercial" 
            required 
            placeholder="Ej: Panadería La Esquina"
            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none">
        </div>

        <div>
          <label for="cuit" class="text-sm font-medium text-slate-700">CUIT *</label>
          <input 
            type="text" 
            id="cuit" 
            name="cuit" 
            required 
            placeholder="Ej: 20-12345678-9"
            maxlength="13"
            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none">
        </div>

        <button 
          type="submit"
          class="w-full rounded-full bg-brand px-5 py-3 text-sm font-semibold text-white hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-brand/50">
          Guardar y continuar
        </button>

      </form>

    </div>
  </div>
</section>