<?php
declare(strict_types=1);

/** @var string $logoPath */
/** @var string|null $loginUrl */
/** @var string|null $errorMessage */

$loginUrl = $loginUrl ?? '?controller=Auth&action=mostrarLogin';
?>

<section class="flex items-center justify-center py-10">
  <div class="w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-xl shadow-slate-900/5">
    <div class="mx-auto mb-4 h-20 w-20 overflow-hidden rounded-2xl shadow-md bg-white">
      <img src="<?php echo htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8'); ?>"
           alt="Logo DonAppetit"
           class="h-full w-full object-contain" />
    </div>

    <h1 class="text-2xl font-semibold text-brand">DonAppetit</h1>
    <p class="mt-1 text-sm text-slate-500">Crear cuenta</p>

    <?php if (!empty($errorMessage)): ?>
      <div class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
        <?php echo htmlspecialchars((string)$errorMessage, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <form method="post"
          action="?controller=Auth&action=registrar"
          class="mt-6 space-y-4 text-left">

      <div>
        <label class="text-sm font-medium text-slate-700" for="nombre">Nombre</label>
        <input id="nombre"
               name="nombre"
               required
               placeholder="Juan Perez"
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-brand/60 focus:outline-none" />
      </div>

      <div>
        <label class="text-sm font-medium text-slate-700" for="email">Email</label>
        <input type="email"
               name="email"
               id="email"
               required
               placeholder="usuario@email.com"
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-brand/60 focus:outline-none" />
        <p id="emailHelp" class="mt-1 text-xs text-red-500 hidden">
          Formato de email invalido
        </p>
      </div>

      <div>
        <label class="text-sm font-medium text-slate-700" for="contrasena">Contrasena</label>
        <input type="password"
               name="contrasena"
               id="contrasena"
               required
               placeholder="Minimo 8 caracteres"
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-brand/60 focus:outline-none" />
        <p id="passHelp" class="mt-1 text-xs text-red-500 hidden">
          Debe tener al menos 8 caracteres
        </p>
      </div>

      <div>
        <label class="text-sm font-medium text-slate-700" for="confirmar">Confirmar contrasena</label>
        <input type="password"
               id="confirmar"
               required
               placeholder="Repetir contrasena"
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-brand/60 focus:outline-none" />
        <p id="confirmHelp" class="mt-1 text-xs text-red-500 hidden">
          Las contrasenas no coinciden
        </p>
      </div>

      <div>
        <label class="text-sm font-medium text-slate-700" for="telefono">Telefono (opcional)</label>
        <input id="telefono"
               name="telefono"
               placeholder="Ej: 3794123456"
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-brand/60 focus:outline-none" />
      </div>

      <input type="hidden" name="latitud" id="latitud" />
      <input type="hidden" name="longitud" id="longitud" />

      <div>
        <span class="text-sm font-medium text-slate-700">Rol</span>
        <div class="mt-2 flex gap-4 text-sm">
          <label class="flex items-center gap-2">
            <input type="radio" name="rol" value="donante" required />
            Donante
          </label>
          <label class="flex items-center gap-2">
            <input type="radio" name="rol" value="receptor" />
            Receptor
          </label>
        </div>
      </div>

      <button type="submit"
              class="w-full rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand/90">
        Registrarse
      </button>
    </form>

    <p class="mt-6 text-sm text-slate-500">
      Ya tenes cuenta?
      <a href="<?php echo htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8'); ?>"
         class="font-semibold text-brand hover:text-brand/80">
        Iniciar sesion
      </a>
    </p>
  </div>
</section>

<script>
const emailInput = document.getElementById('email');
const passInput = document.getElementById('contrasena');
const confirmInput = document.getElementById('confirmar');
const emailHelp = document.getElementById('emailHelp');
const passHelp = document.getElementById('passHelp');
const confirmHelp = document.getElementById('confirmHelp');
const latitud = document.getElementById('latitud');
const longitud = document.getElementById('longitud');

function validateEmail(value) {
  return /^[\w.!#$%&'*+\/=?^`{|}~-]+@[\w-]+(\.[\w-]+)+$/.test(value);
}

if (emailInput) {
  emailInput.addEventListener('input', () => {
    emailHelp.classList.toggle('hidden', validateEmail(emailInput.value));
  });
}

if (passInput) {
  passInput.addEventListener('input', () => {
    passHelp.classList.toggle('hidden', passInput.value.length >= 8);
    if (confirmInput) {
      confirmHelp.classList.toggle('hidden', confirmInput.value === passInput.value);
    }
  });
}

if (confirmInput) {
  confirmInput.addEventListener('input', () => {
    confirmHelp.classList.toggle('hidden', confirmInput.value === passInput.value);
  });
}

if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition((pos) => {
    if (latitud) {
      latitud.value = pos.coords.latitude;
    }
    if (longitud) {
      longitud.value = pos.coords.longitude;
    }
  });
}
</script>
