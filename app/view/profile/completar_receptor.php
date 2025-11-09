<?php
declare(strict_types=1);

/** @var string $userName */
/** @var bool $needsProfile */
/** @var string|null $errorMessage */
/** @var string|null $successMessage */

$needsProfile = $needsProfile ?? false;
?>

<section class="py-10">
  <div class="mx-auto max-w-3xl">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-900/5">
      <header class="mb-6 text-center">
        <p class="text-sm uppercase tracking-wide text-brand/80">Hola <?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></p>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-900">Complet tu perfil de receptor</h1>
        <p class="mt-2 text-sm text-slate-500">
          Necesitamos los datos de tu organizacin para coordinar las entregas de manera segura.
        </p>
      </header>

      <?php if ($needsProfile): ?>
        <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
          An no registramos los datos de tu institucin. Complet el formulario para continuar.
        </div>
      <?php endif; ?>

      <?php if (!empty($errorMessage)): ?>
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($successMessage)): ?>
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
          <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form method="post" action="?controller=Profile&action=guardarReceptor" class="space-y-8">
        <section>
          <h2 class="text-lg font-semibold text-slate-900">Datos de la institucin</h2>
          <div class="mt-4 space-y-4">
            <div>
              <label for="nom_institucion" class="text-sm font-medium text-slate-700">Nombre de la institucin *</label>
              <input
                id="nom_institucion"
                name="nom_institucion"
                type="text"
                required
                placeholder="Ej: Comedor Comunitario Esperanza"
                class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-brand/20" />
            </div>
            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label for="num_renacom" class="text-sm font-medium text-slate-700">Nmero RENACOM *</label>
                <input
                  id="num_renacom"
                  name="num_renacom"
                  type="text"
                  required
                  placeholder="Ej: 123456"
                  class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-brand/20" />
              </div>
              <div>
                <label for="responsable" class="text-sm font-medium text-slate-700">Responsable *</label>
                <input
                  id="responsable"
                  name="responsable"
                  type="text"
                  required
                  placeholder="Ej: Mara Gonzlez"
                  class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-brand/20" />
              </div>
            </div>
          </div>
        </section>

        <section class="border-t border-slate-100 pt-6">
          <h2 class="text-lg font-semibold text-slate-900">Direccin de la institucin</h2>
          <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
              <label for="nombre_calle" class="text-sm font-medium text-slate-700">Calle *</label>
              <input
                id="nombre_calle"
                name="nombre_calle"
                type="text"
                required
                placeholder="Ej: San Martn"
                class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-brand/20" />
            </div>
            <div>
              <label for="num_calle" class="text-sm font-medium text-slate-700">Nmero *</label>
              <input
                id="num_calle"
                name="num_calle"
                type="text"
                required
                placeholder="Ej: 456"
                class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-brand/20" />
            </div>
          </div>

          <p class="mt-6 text-sm text-slate-500">Indic la ubicacin exacta en el mapa (pods arrastrar el marcador o hacer clic para moverlo).</p>
          <div id="map" class="mt-3 h-64 w-full rounded-2xl border border-slate-200"></div>

          <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
              <label for="latitud" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Latitud</label>
              <input
                id="latitud"
                name="latitud"
                readonly
                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm" />
            </div>
            <div>
              <label for="longitud" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Longitud</label>
              <input
                id="longitud"
                name="longitud"
                readonly
                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm" />
            </div>
          </div>
        </section>

        <button
          type="submit"
          class="w-full rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand/60">
          Guardar y continuar
        </button>
      </form>
    </div>
  </div>
</section>
