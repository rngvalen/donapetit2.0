<?php
declare(strict_types=1);

/**
 * @var array<int,array<string,mixed>> $productos
 * @var string|null $titulo
 */
$productos = $productos ?? [];
$titulo = $titulo ?? 'Mis productos';
?>

<section class="py-6 text-slate-800">
    <header class="flex items-center justify-between gap-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-400 px-4 py-3 text-white shadow-lg shadow-emerald-700/20">
        <a href="index.php?controller=Producto&action=index" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 p-2 hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70" aria-label="Volver">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 18l-6-6 6-6"></path>
            </svg>
        </a>

        <h1 class="text-lg font-semibold tracking-tight sm:text-xl">
            <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>
        </h1>

        <div class="flex items-center gap-2">
            <a href="index.php?controller=Solicitud&action=misSolicitudes" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 p-2 hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70" aria-label="Solicitudes" title="Ver solicitudes pendientes">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </a>
            <button type="button" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 p-2 hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70" aria-label="Menu">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M3 12h18M3 18h18"></path>
                </svg>
            </button>
        </div>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            <?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($productos)): ?>
        <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($productos as $producto): ?>
                <?php
                $nombre = htmlspecialchars((string)($producto['nombre'] ?? 'Producto sin nombre'), ENT_QUOTES, 'UTF-8');
                $cantidad = $producto['cantidad'] ?? null;
                $cantidadLabel = $cantidad === null ? 'N/D' : (string)$cantidad;
                $vence = htmlspecialchars((string)($producto['vence'] ?? 'Sin fecha'), ENT_QUOTES, 'UTF-8');
                $links = $producto['links'] ?? null;
                $showUrl = is_array($links) && isset($links['show']) ? htmlspecialchars((string)$links['show'], ENT_QUOTES, 'UTF-8') : null;
                $editUrl = is_array($links) && isset($links['edit']) ? htmlspecialchars((string)$links['edit'], ENT_QUOTES, 'UTF-8') : null;
                $destroyUrl = is_array($links) && isset($links['destroy']) ? htmlspecialchars((string)$links['destroy'], ENT_QUOTES, 'UTF-8') : null;
                
                // Determinar color según stock
                $stockBajo = $cantidad !== null && $cantidad <= 5;
                $stockColor = $stockBajo ? 'bg-red-100 text-red-700' : 'bg-slate-50 text-slate-900';
                ?>
                <article class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 grid h-36 place-items-center rounded-xl border-2 border-dashed border-slate-200 bg-gradient-to-br from-slate-50 to-white text-slate-400 transition group-hover:from-emerald-50 group-hover:text-emerald-500">
                        <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h3l2-3h8l2 3h3a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </div>

                    <h2 class="text-center text-base font-semibold text-slate-900">
                        <?php echo $nombre; ?>
                    </h2>

                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="flex flex-col rounded-xl <?php echo $stockColor; ?> px-3 py-2 text-center">
                            <dt class="text-xs font-medium uppercase tracking-wide">Cantidad</dt>
                            <dd class="text-base font-semibold"><?php echo htmlspecialchars($cantidadLabel, ENT_QUOTES, 'UTF-8'); ?></dd>
                            <?php if ($stockBajo): ?>
                                <dd class="text-xs mt-0.5">¡Stock bajo!</dd>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col rounded-xl bg-slate-50 px-3 py-2 text-center">
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Vence</dt>
                            <dd class="text-base font-semibold text-slate-900"><?php echo $vence; ?></dd>
                        </div>
                    </dl>

                    <div class="mt-5 flex items-center justify-center gap-3">
                        <?php if ($showUrl): ?>
                        <a href="<?php echo $showUrl; ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Ver
                        </a>
                        <?php endif; ?>
                        <?php if ($editUrl): ?>
                        <a href="<?php echo $editUrl; ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                            </svg>
                            Editar
                        </a>
                        <?php endif; ?>
                        <?php if ($destroyUrl): ?>
                        <a href="<?php echo $destroyUrl; ?>" class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500" onclick="return confirm('¿Eliminar producto?');">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                <path d="M10 11v6M14 11v6"></path>
                                <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            Eliminar
                        </a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-sm text-slate-500">
            Aún no cargaste productos. Usa el botón flotante para agregar el primero.
        </div>
    <?php endif; ?>

    <a href="index.php?controller=Producto&action=create" class="fixed bottom-6 right-6 flex h-14 w-14 items-center justify-center rounded-full bg-indigo-600 text-white shadow-2xl shadow-indigo-500/40 transition hover:scale-110 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-300" aria-label="Agregar producto">
        <svg class="h-6 w-6" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2">
            <path d="M12 5v14M5 12h14"></path>
        </svg>
    </a>
</section>