<?php
// Obtiene el nombre de usuario desde la variable $userName, o desde la sesion si no esta definida
$userName = $userName ?? ($_SESSION['user']['name'] ?? 'Usuario');

// Obtiene el avatar del usuario si esta disponible
$userAvatar = $userAvatar ?? ($_SESSION['user']['avatar'] ?? null);

// Define los items del menu agrupados por secciones (usuario/admin/cuenta).
$menuSections = $menuSections ?? null;
if ($menuSections === null) {
    if (isset($menuItems) && is_array($menuItems)) {
        $menuSections = ['Menu' => $menuItems];
    } else {
        $menuSections = [
            'Usuario' => [
                ['label' => 'Inicio', 'url' => 'index.php?controller=Home&action=index'],
                ['label' => 'Mis productos', 'url' => 'index.php?controller=Producto&action=index'],
                ['label' => 'Registrar disponibilidad', 'url' => 'index.php?controller=Producto&action=create'],
            ],
            'Admin' => [
                ['label' => 'Panel principal', 'url' => 'index.php?controller=Admin&action=principal'],
                ['label' => 'Catalogo de productos', 'url' => 'index.php?controller=Producto&action=catalogo'],
                ['label' => 'Estadisticas', 'url' => 'index.php?controller=Home&action=statics'],
            ],
            'Cuenta' => [
                ['label' => 'Cerrar sesion', 'url' => 'index.php?controller=Auth&action=logout'],
            ],
        ];
    }
}

// Obtiene la inicial del nombre de usuario para mostrar en el avatar si no hay imagen
$initial = strtoupper(mb_substr($userName, 0, 1, 'UTF-8'));
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DonAppetit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: { brand: { DEFAULT: '#0f1629' } },
                    boxShadow: { soft: '0 8px 30px rgba(0,0,0,.08)' }
                }
            }
        }
    </script>
    <style>
        * { transition: all .15s ease-in-out }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen flex flex-col">
    <!-- Encabezado principal con barra de navegacion -->
    <header class="relative z-30 w-full bg-brand text-white shadow">
        <nav class="relative mx-auto flex w-full max-w-6xl items-center justify-between gap-4 px-4 py-3">
            <!-- IZQUIERDA: Logo y nombre -->
            <a href="index.php?controller=Home&action=index" class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-white/20 shadow-soft">
                    <span class="text-xl font-semibold">DA</span>
                </div>
                <span class="text-lg font-semibold tracking-tight">DonAppetit</span>
            </a>

            <!-- DERECHA: Menu y usuario -->
            <div class="flex items-center gap-4">
                <!-- Menu desplegable para escritorio -->
                <div class="relative hidden md:block">
                    <button id="navDropdownToggle"
                        class="inline-flex items-center justify-center rounded-lg bg-white/10 p-2 hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                        aria-label="Abrir menu desplegable" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                        </svg>
                        <span class="sr-only">Abrir menu</span>
                    </button>
                    <div id="navDropdownMenu"
                        class="absolute right-0 top-full mt-2 w-60 overflow-hidden rounded-lg bg-white text-slate-800 shadow-xl ring-1 ring-black/5 hidden z-50">
                        <?php $sectionIndex = 0; $sectionCount = count($menuSections); ?>
                        <?php foreach ($menuSections as $sectionLabel => $items): ?>
                            <div class="<?php echo $sectionIndex + 1 < $sectionCount ? 'border-b border-slate-100' : ''; ?>">
                                <p class="px-4 pt-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    <?php echo htmlspecialchars($sectionLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <?php foreach ($items as $item): ?>
                                    <a href="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>"
                                        class="block px-4 py-2 text-sm hover:bg-slate-100">
                                        <?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <?php $sectionIndex++; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Boton menu movil -->
                <button id="btnMenu"
                    class="inline-flex items-center justify-center rounded-lg bg-white/10 px-3 py-2 text-sm font-medium hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60 md:hidden"
                    aria-label="Abrir menu movil" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                </button>

                <!-- Usuario -->
                <div class="flex items-center gap-3">
                    <div class="hidden text-right leading-tight sm:block">
                        <span class="block text-xs opacity-70">Bienvenido</span>
                        <span class="block text-sm font-semibold">
                            <?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-white/20 text-base font-semibold">
                        <?php if ($userAvatar): ?>
                            <img src="<?php echo htmlspecialchars($userAvatar, ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar de <?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>" class="h-full w-full object-cover" />
                        <?php else: ?>
                            <span><?php echo htmlspecialchars($initial, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Menu movil (solo visible en pantallas pequenas) -->
        <div id="mobileMenu" class="absolute left-0 top-full hidden w-full border-t border-white/20 bg-brand/95 shadow-2xl z-40 md:hidden">
            <?php foreach ($menuSections as $sectionLabel => $items): ?>
                <div class="px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-white/70">
                        <?php echo htmlspecialchars($sectionLabel, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                </div>
                <?php foreach ($items as $item): ?>
                    <a href="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>"
                        class="block px-4 py-2 text-sm text-white hover:bg-white/15">
                        <?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </header>

    <!-- Contenedor principal para el contenido de la pagina -->
    <main class="mx-auto w-full max-w-6xl flex-1 px-4">
