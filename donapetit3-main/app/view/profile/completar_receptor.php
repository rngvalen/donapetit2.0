<?php
$user = htmlspecialchars($userName ?? 'Usuario', ENT_QUOTES, 'UTF-8');
?>
<section class="py-10">
  <div class="mx-auto max-w-2xl">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl">
      
      <div class="text-center mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900">Completa tu perfil</h1>
        <p class="mt-2 text-sm text-slate-600">Necesitamos algunos datos adicionales para tu perfil de receptor</p>
      </div>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
          <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <form method="post" action="?controller=Profile&action=guardarReceptor" class="space-y-6">
        
        <div>
          <label for="num_renacom" class="text-sm font-medium text-slate-700">Número RENACOM *</label>
          <input 
            type="text" 
            id="num_renacom" 
            name="num_renacom" 
            required 
            placeholder="Ej: 123456"
            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none">
        </div>

        <div>
          <label for="nom_institucion" class="text-sm font-medium text-slate-700">Nombre de la Institución *</label>
          <input 
            type="text" 
            id="nom_institucion" 
            name="nom_institucion" 
            required 
            placeholder="Ej: Comedor Comunitario Esperanza"
            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none">
        </div>

        <div>
          <label for="responsable" class="text-sm font-medium text-slate-700">Responsable *</label>
          <input 
            type="text" 
            id="responsable" 
            name="responsable" 
            required 
            placeholder="Ej: María González"
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
