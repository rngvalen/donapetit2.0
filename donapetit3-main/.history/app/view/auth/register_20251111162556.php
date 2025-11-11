<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$projectBase = $scriptDir;
$posApp = strpos($projectBase, '/app/');
if ($posApp !== false) {
    $projectBase = substr($projectBase, 0, $posApp);
}
if ($projectBase === '') {
    $projectBase = '/';
}

$tryPublic = rtrim($projectBase, '/') . '/public/assets/img/logo-don.png';
$tryAssets = rtrim($projectBase, '/') . '/assets/img/logo-don.png';

$logoPath = $tryPublic;
if (!is_file(($_SERVER['DOCUMENT_ROOT'] ?? '') . $logoPath)) {
    $logoPath = $tryAssets;
}

$loginUrl = '?controller=Auth&action=mostrarLogin';
$errorMessage = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Registrarse - DonAppetit</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: { brand: '#0F1629' },
        fontFamily: { sans: ['Montserrat','Inter','system-ui','sans-serif'] },
        boxShadow: { soft: '0 20px 60px rgba(15,22,41,.12)' }
      }
    }
  };
</script>
<style>*{transition:all .15s ease-in-out}</style>
</head>

<body class="bg-slate-100 text-slate-900 min-h-screen flex items-center justify-center font-sans">
<main class="w-full max-w-6xl px-4">
  <section class="flex items-center justify-center py-10">
    <div class="w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-xl shadow-slate-900/5">

      <div class="mx-auto mb-4 h-20 w-20 overflow-hidden rounded-2xl shadow-md bg-white">
        <img src="<?= htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8') ?>" alt="Logo DonAppetit" class="h-full w-full object-contain">
      </div>

      <h1 class="text-2xl font-semibold text-brand">DonAppetit</h1>
      <p class="mt-1 text-sm text-slate-500">Crear cuenta</p>

      <?php if ($errorMessage): ?>
        <div class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
          <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form method="post" action="?controller=Auth&action=registrar" class="mt-6 space-y-4 text-left">

        <div>
          <label class="text-sm font-medium text-slate-700" for="nombre">Nombre</label>
          <input id="nombre" name="nombre" required placeholder="Juan Perez"
            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-brand/60 focus:outline-none">
        </div>

        <div>
          <label class="text-sm font-medium text-slate-700" for="email">Email</label>
          <input type="email" name="email" id="email" required placeholder="usuario@email.com"
            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-brand/60 focus:outline-none">
          <p id="emailHelp" class="text-xs text-red-500 mt-1 hidden">
            Formato de email invalido
          </p>
        </div>

        <!-- Contraseña con ojito -->
        <div>
          <label class="text-sm font-medium text-slate-700" for="contrasena">Contraseña</label>
          <div class="relative mt-2">
            <input 
              type="password" 
              name="contrasena" 
              id="contrasena" 
              required 
              placeholder="Minimo 8 caracteres"
              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 pr-12 text-sm focus:ring-brand/60 focus:outline-none">
            
            <!-- Botón del ojito para contraseña -->
            <button 
              type="button"
              onclick="togglePasswordField('contrasena')"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors"
              aria-label="Mostrar contraseña">
              <svg id="eye-closed-contrasena" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
              </svg>
              <svg id="eye-open-contrasena" class="w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </div>
          <p id="passHelp" class="text-xs text-red-500 mt-1 hidden">
            Debe tener al menos 8 caracteres
          </p>
        </div>

        <!-- Confirmar contraseña con ojito -->
        <div>
          <label class="text-sm font-medium text-slate-700" for="confirmar">Confirmar contraseña</label>
          <div class="relative mt-2">
            <input 
              type="password" 
              id="confirmar" 
              required 
              placeholder="Repetir contrasena"
              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 pr-12 text-sm focus:ring-brand/60 focus:outline-none">
            
            <!-- Botón del ojito para confirmar contraseña -->
            <button 
              type="button"
              onclick="togglePasswordField('confirmar')"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors"
              aria-label="Mostrar contraseña">
              <svg id="eye-closed-confirmar" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
              </svg>
              <svg id="eye-open-confirmar" class="w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </div>
          <p id="confirmHelp" class="text-xs text-red-500 mt-1 hidden">
            Las contrasenas no coinciden
          </p>
        </div>

        <div>
          <label class="text-sm font-medium text-slate-700" for="telefono">Telefono</label>
          <input id="telefono" name="telefono" placeholder="Ej: 3794123456"
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
        Ya tenes cuenta?
        <a href="<?= $loginUrl ?>" class="font-semibold text-brand hover:text-brand/80">
          Iniciar sesion
        </a>
      </p>

    </div>
  </section>
</main>

<script>
// Función para mostrar/ocultar contraseña
function togglePasswordField(fieldId) {
  const passwordInput = document.getElementById(fieldId);
  const eyeClosed = document.getElementById('eye-closed-' + fieldId);
  const eyeOpen = document.getElementById('eye-open-' + fieldId);
  
  if (passwordInput.type === 'password') {
    // Mostrar contraseña
    passwordInput.type = 'text';
    eyeClosed.classList.add('hidden');
    eyeOpen.classList.remove('hidden');
  } else {
    // Ocultar contraseña
    passwordInput.type = 'password';
    eyeClosed.classList.remove('hidden');
    eyeOpen.classList.add('hidden');
  }
}

// Validaciones originales
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

</body>
</html>