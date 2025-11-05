<?php
declare(strict_types=1);

/** @var string $logoPath */
/** @var string|null $registerUrl */
/** @var string|null $forgotUrl */
/** @var string|null $errorMessage */
/** @var string|null $successMessage */

$registerUrl = $registerUrl ?? '?controller=Auth&action=mostrarRegistro';
$forgotUrl = $forgotUrl ?? '#';
?>

<section class="flex items-center justify-center py-10">
  <div class="w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-xl shadow-slate-900/5">
    <div class="mx-auto mb-4 h-20 w-20 overflow-hidden rounded-2xl shadow-md bg-white">
      <img src="<?php echo htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8'); ?>"
           alt="Logo DonAppetit"
           class="h-full w-full object-contain" />
    </div>

    <h1 class="text-2xl font-semibold text-brand">DonAppetit</h1>
    <p class="mt-1 text-sm text-slate-500">Ingresar</p>

    <?php if (!empty($errorMessage)): ?>
      <div class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
        <?php echo htmlspecialchars((string)$errorMessage, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
      <div class="mt-4 rounded-lg bg-brand/10 p-3 text-sm text-brand">
        <?php echo htmlspecialchars((string)$successMessage, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <form method="post"
          action="?controller=Auth&action=login"
          class="mt-6 space-y-4 text-left">
      <div>
        <label for="email" class="text-sm font-medium text-slate-700">Email</label>
        <input id="email"
               name="email"
               type="email"
               required
               placeholder="Email"
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-brand/60" />
      </div>

      <div>
        <label for="password" class="text-sm font-medium text-slate-700">Contrasena</label>
        <input id="password"
               name="password"
               type="password"
               required
               placeholder="Contrasena"
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-brand/60" />
      </div>

      <div class="flex items-center justify-between pt-1">
        <a href="<?php echo htmlspecialchars($forgotUrl, ENT_QUOTES, 'UTF-8'); ?>"
           class="text-sm font-medium text-brand hover:text-brand/80">
          Olvidaste tu contrasena?
        </a>
      </div>

      <button type="submit"
              class="mt-2 w-full rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand/90">
        Iniciar sesion
      </button>
    </form>

    <p class="mt-6 text-sm text-slate-500">
      No tenes cuenta?
      <a href="<?php echo htmlspecialchars($registerUrl, ENT_QUOTES, 'UTF-8'); ?>"
         class="font-semibold text-brand hover:text-brand/80">
        Registrate
      </a>
    </p>
  </div>
</section>
