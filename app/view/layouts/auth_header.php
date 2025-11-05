<?php
declare(strict_types=1);

require_once __DIR__ . '/../../core/auth_session.php';

$currentUser = current_user() ?? [];
$isLogged = is_logged();
$isAdmin = has_role(ROLE_ADMIN);

$authPageTitle = $authPageTitle ?? 'DonAppetit';
$authBodyClass = $authBodyClass
    ?? 'bg-slate-100 text-slate-900 min-h-screen flex items-center justify-center font-sans';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($authPageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { brand: '#3D538F' },
          fontFamily: { sans: ['Montserrat', 'Inter', 'system-ui', 'sans-serif'] },
          boxShadow: { soft: '0 20px 60px rgba(15,22,41,.12)' }
        }
      }
    };
  </script>
  <style>*{transition:all .15s ease-in-out}</style>
</head>
<body class="<?php echo htmlspecialchars($authBodyClass, ENT_QUOTES, 'UTF-8'); ?>">
  <nav class="absolute right-6 top-6 flex items-center gap-3 text-sm text-slate-600">
    <?php if ($isLogged): ?>
      <span>
        Hola,
        <?php echo htmlspecialchars((string)($currentUser['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
      </span>

      <?php if ($isAdmin): ?>
        <a href="?controller=Usuario&action=index"
           class="rounded-lg bg-brand px-3 py-1 text-white hover:bg-brand/90">
          Usuarios
        </a>
      <?php endif; ?>

      <a href="?controller=Auth&action=logout"
         class="rounded-lg border border-brand px-3 py-1 text-brand hover:bg-brand/10">
        Salir
      </a>
    <?php else: ?>
      <a href="?controller=Auth&action=mostrarLogin"
         class="rounded-lg border border-brand px-3 py-1 text-brand hover:bg-brand/10">
        Ingresar
      </a>
    <?php endif; ?>
  </nav>

  <main class="w-full max-w-6xl px-4">
