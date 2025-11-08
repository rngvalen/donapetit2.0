<?php
declare(strict_types=1);

/** @var string $logoPath */
/** @var string $email */
/** @var string|null $errorMessage */
/** @var string|null $successMessage */

$email = $email ?? '';
?>

<section class="flex items-center justify-center py-10">
  <div class="w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-xl shadow-slate-900/5">
    <div class="mx-auto mb-4 h-20 w-20 overflow-hidden rounded-2xl shadow-md bg-white">
      <img src="<?php echo htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8'); ?>"
           alt="Logo DonAppetit"
           class="h-full w-full object-contain" />
    </div>

    <h1 class="text-2xl font-semibold text-brand">Verificar codigo</h1>
    <p class="mt-1 text-sm text-slate-500">Revisa tu email e ingresa el codigo</p>
    <p class="mt-1 text-xs text-slate-400">
      <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>
    </p>

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
          action="?controller=Auth&action=verificarCodigo"
          class="mt-6 space-y-4 text-left">
      <div>
        <label for="codigo" class="text-sm font-medium text-slate-700">Codigo de verificacion</label>
        <input id="codigo"
               name="codigo"
               type="text"
               required
               placeholder="123456"
               maxlength="6"
               pattern="[0-9]{6}"
               autocomplete="off"
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-center text-2xl font-bold tracking-[0.5em] focus:border-brand focus:ring-brand/60" />
        <p class="mt-2 text-xs text-slate-500 text-center">
          Ingresar el codigo de 6 digitos que recibiste por email
        </p>
      </div>

      <button type="submit"
              class="mt-4 w-full rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand/90">
        Verificar codigo
      </button>
    </form>

    <div class="mt-6 space-y-2 text-sm">
      <p class="text-slate-500">
        No recibiste el codigo?
        <a href="?controller=Auth&action=mostrarRecuperacion"
           class="font-semibold text-brand hover:text-brand/80">
          Reenviar
        </a>
      </p>
      <p class="text-slate-500">
        <a href="?controller=Auth&action=mostrarLogin"
           class="font-semibold text-brand hover:text-brand/80">
          Volver al login
        </a>
      </p>
    </div>
  </div>
</section>

<script>
  const codigoInput = document.getElementById('codigo');
  codigoInput?.focus();
  codigoInput?.addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
  });
</script>
