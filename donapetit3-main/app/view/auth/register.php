<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Resolver logo dinámico como en login
$scriptDir = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$projectBase = $scriptDir;
$posApp = strpos($projectBase, '/app/');
if ($posApp !== false) {
    $projectBase = substr($projectBase, 0, $posApp);
}
if ($projectBase === '') { $projectBase = '/'; }

$tryPublic = rtrim($projectBase, '/') . '/public/assets/img/logo-don.png';
$tryAssets = rtrim($projectBase, '/') . '/assets/img/logo-don.png';

$logoPath = $tryPublic;
if (!is_file($_SERVER['DOCUMENT_ROOT'] . $logoPath)) {
    $logoPath = $tryAssets;
}
$loginUrl = '?controller=Auth&action=mostrarLogin';
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Registrarse — DonAppétit</title>

<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: { brand: '#3D538F' },
        fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
        boxShadow: { soft: '0 20px 60px rgba(15,22,41,.12)' }
      }
    }
  };
</script>
<style>*{transition:all .15s ease-in-out}</style>
</head>

<body class="bg-slate-100 text-slate-900 min-h-screen flex items-center justify-center">
<main class="w-full max-w-6xl px-4">
  <section class="flex items-center justify-center py-10">
    <div class="w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-xl shadow-slate-900/5">

      <!-- Logo -->
      <div class="mx-auto mb-4 h-20 w-20 overflow-hidden rounded-2xl shadow-md bg-white">
        <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo DonAppétit" class="h-full w-full object-contain">
      </div>

      <h1 class="text-2xl font-semibold text-brand">DonAppétit</h1>
      <p class="mt-1 text-sm text-slate-500">Crear cuenta</p>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
          <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <!-- ✅ Acá está el cambio -->
      <form method="post" action="?controller=Auth&action=registrar" class="mt-6 space-y-4 text-left">

        <div>
          <label class="text-sm font-medium text-slate-700">Nombre</label>
          <input name="nombre" required placeholder="Juan Pérez"
            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-brand/60 focus:outline-none">
        </div>

        <div>
          <label class="text-sm font-medium text-slate-700">Email</label>
          <input type="email" name="email" id="email" required placeholder="usuario@email.com"
            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-brand/60 focus:outline-none">
          <p id="emailHelp" class="text-xs text-red-500 mt-1 hidden">
            Formato de email inválido
          </p>
        </div>

        <div>
          <label class="text-sm font-medium text-slate-700">Contraseña</label>
          <input type="password" name="contrasena" id="contrasena" required placeholder="Mínimo 8 caracteres"
            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-brand/60 focus:outline-none">
          <p id="passHelp" class="text-xs text-red-500 mt-1 hidden">
            Debe tener al menos 8 caracteres
          </p>
        </div>

        <div>
          <label class="text-sm font-medium text-slate-700">Confirmar contraseña</label>
          <input type="password" id="confirmar" required placeholder="Repetir contraseña"
            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-brand/60 focus:outline-none">
          <p id="confirmHelp" class="text-xs text-red-500 mt-1 hidden">
            Las contraseñas no coinciden
          </p>
        </div>

        <div>
          <label class="text-sm font-medium text-slate-700">Teléfono (opcional)</label>
          <input name="telefono" placeholder="Ej: 3794123456"
            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-brand/60 focus:outline-none">
        </div>

        <input type="hidden" name="latitud" id="latitud">
        <input type="hidden" name="longitud" id="longitud">

        <div>
          <span class="text-sm font-medium text-slate-700">Rol</span>
          <div class="mt-2 flex gap-4 text-sm">
            <label class="flex items-center gap-2"><input type="radio" name="rol" value="donante" required> Donante</label>
            <label class="flex items-center gap-2"><input type="radio" name="rol" value="receptor"> Receptor</label>
          </div>
        </div>

        <button type="submit"
          class="w-full rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand/90">
          Registrarse
        </button>
      </form>

      <p class="mt-6 text-sm text-slate-500">
        ¿Ya tenés cuenta?
        <a href="<?= $loginUrl ?>" class="font-semibold text-brand hover:text-brand/80">
          Iniciar sesión
        </a>
      </p>

    </div>
  </section>
</main>

<script>
// ✅ Validación visual solamente (ya no bloquea envío)
function validar() {
  const email = emailHelp.classList;
  const pass = passHelp.classList;
  const confirm = confirmHelp.classList;
}

navigator.geolocation.getCurrentPosition(pos => {
  latitud.value = pos.coords.latitude;
  longitud.value = pos.coords.longitude;
});
</script>

</body>
</html>