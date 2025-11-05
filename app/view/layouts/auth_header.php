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
  <link rel="stylesheet" href="assets/css/tailwind.css">
  <style>*{transition:all .15s ease-in-out}</style>
</head>
<body data-theme="auth" class="<?php echo htmlspecialchars($authBodyClass, ENT_QUOTES, 'UTF-8'); ?>">
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
