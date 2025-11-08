<?php
declare(strict_types=1);

/** @var string $logoPath */
/** @var string|null $errorMessage */
/** @var string|null $successMessage */
?>

<section class="flex items-center justify-center py-10">
  <div class="w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-xl shadow-slate-900/5">
    <div class="mx-auto mb-4 h-20 w-20 overflow-hidden rounded-2xl shadow-md bg-white">
      <img src="<?php echo htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8'); ?>"
           alt="Logo DonAppetit"
           class="h-full w-full object-contain" />
    </div>

    <h1 class="text-2xl font-semibold text-brand">Nueva contrasena</h1>
    <p class="mt-1 text-sm text-slate-500">Ingresa tu nueva contrasena</p>

    <?php if (!empty($errorMessage)): ?>
      <div class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
        <?php echo htmlspecialchars((string)$errorMessage, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
      <div class="mt-4 rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-700">
        <?php echo htmlspecialchars((string)$successMessage, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <form method="post"
          action="?controller=Auth&action=cambiarContrasena"
          class="mt-6 space-y-4 text-left"
          id="resetForm">
      <div>
        <label for="password" class="text-sm font-medium text-slate-700">Nueva contrasena</label>
        <input id="password"
               name="password"
               type="password"
               required
               placeholder="Minimo 8 caracteres"
               minlength="8"
               autocomplete="new-password"
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-brand/60" />
        <p class="mt-1 text-xs text-slate-500">Debe tener al menos 8 caracteres</p>
      </div>

      <div>
        <label for="confirm_password" class="text-sm font-medium text-slate-700">Confirmar contrasena</label>
        <input id="confirm_password"
               name="confirm_password"
               type="password"
               required
               minlength="8"
               autocomplete="new-password"
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-brand/60" />
        <p id="matchError" class="mt-1 text-xs text-red-500 hidden">Las contrasenas no coinciden</p>
      </div>

      <button type="submit"
              class="mt-4 w-full rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand/90">
        Cambiar contrasena
      </button>
    </form>
  </div>
</section>

<script>
  const passwordInput = document.getElementById('password');
  const confirmInput = document.getElementById('confirm_password');
  const matchError = document.getElementById('matchError');
  const form = document.getElementById('resetForm');

  passwordInput?.focus();

  function validatePasswords() {
    if (!passwordInput || !confirmInput || !matchError) {
      return true;
    }

    if (confirmInput.value !== '' && passwordInput.value !== confirmInput.value) {
      matchError.classList.remove('hidden');
      confirmInput.classList.add('border-red-300');
      return false;
    }

    matchError.classList.add('hidden');
    confirmInput.classList.remove('border-red-300');
    return true;
  }

  confirmInput?.addEventListener('input', validatePasswords);
  passwordInput?.addEventListener('input', validatePasswords);

  form?.addEventListener('submit', function (event) {
    if (!validatePasswords()) {
      event.preventDefault();
    }
  });
</script>
