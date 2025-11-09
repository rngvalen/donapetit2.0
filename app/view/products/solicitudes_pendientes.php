<?php

declare(strict_types=1);



/**

 * @var array<int,array<string,mixed>> $solicitudes

 * @var string|null $titulo

 */

$solicitudes = $solicitudes ?? [];

$titulo = $titulo ?? 'Solicitudes pendientes';

?>



<section class="py-6 text-slate-800">

    <header class="flex items-center justify-between gap-3 rounded-2xl bg-gradient-to-r from-amber-600 to-amber-400 px-4 py-3 text-white shadow-lg shadow-amber-700/20">

        <a href="index.php?controller=Producto&action=misProductos" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 p-2 hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70" aria-label="Volver">

            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                <path d="M15 18l-6-6 6-6"></path>

            </svg>

        </a>



        <h1 class="text-lg font-semibold tracking-tight sm:text-xl">

            <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>

        </h1>



        <div class="w-10"></div>

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



    <?php if (!empty($solicitudes)): ?>

        <div class="mt-6 space-y-4">

            <?php foreach ($solicitudes as $solicitud): ?>

                <?php

                $idDetalle = (int)($solicitud['id_solicitud_detalle'] ?? 0);

                $nombreProducto = htmlspecialchars((string)($solicitud['nombre_producto'] ?? 'Producto'), ENT_QUOTES, 'UTF-8');

                $cantidad = (int)($solicitud['cantidad'] ?? 0);

                $unidad = htmlspecialchars((string)($solicitud['unidad'] ?? ''), ENT_QUOTES, 'UTF-8');

                $institucion = htmlspecialchars((string)($solicitud['institucion_receptor'] ?? 'Instituci%%n desconocida'), ENT_QUOTES, 'UTF-8');

                $responsable = htmlspecialchars((string)($solicitud['responsable_receptor'] ?? ''), ENT_QUOTES, 'UTF-8');

                $fechaSolicitud = $solicitud['fecha_solicitud'] ?? null;

                $fechaProgramada = $solicitud['fecha_programada'] ?? null;

                

                $fechaSolicitudLabel = 'Fecha desconocida';

                if ($fechaSolicitud) {

                    $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $fechaSolicitud);

                    if ($fecha) {

                        $fechaSolicitudLabel = $fecha->format('d/m/Y H:i');

                    }

                }

                

                $fechaProgramadaLabel = null;

                if ($fechaProgramada) {

                    $fecha = \DateTime::createFromFormat('Y-m-d', $fechaProgramada);

                    if ($fecha) {

                        $fechaProgramadaLabel = $fecha->format('d/m/Y');

                    }

                }

                ?>

                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <div class="flex items-start justify-between gap-4">

                        <div class="flex-1">

                            <h3 class="text-lg font-semibold text-slate-900"><?php echo $nombreProducto; ?></h3>

                            <p class="mt-1 text-sm text-slate-500">Solicitado el <?php echo $fechaSolicitudLabel; ?></p>

                        </div>

                        <div class="rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-700">

                            Pendiente

                        </div>

                    </div>



                    <div class="mt-4 grid gap-3 sm:grid-cols-2">

                        <div class="rounded-lg bg-slate-50 px-4 py-3">

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Cantidad solicitada</p>

                            <p class="mt-1 text-lg font-semibold text-slate-900"><?php echo $cantidad; ?> <?php echo $unidad; ?></p>

                        </div>

                        <div class="rounded-lg bg-slate-50 px-4 py-3">

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Instituci%%n</p>

                            <p class="mt-1 text-base font-semibold text-slate-900"><?php echo $institucion; ?></p>

                        </div>

                    </div>



                    <?php if ($responsable): ?>

                        <div class="mt-3 rounded-lg bg-slate-50 px-4 py-3">

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Responsable</p>

                            <p class="mt-1 text-base text-slate-700"><?php echo $responsable; ?></p>

                        </div>

                    <?php endif; ?>



                    <?php if ($fechaProgramadaLabel): ?>

                        <div class="mt-3 rounded-lg bg-blue-50 px-4 py-3">

                            <p class="text-xs font-medium uppercase tracking-wide text-blue-600">Fecha programada para retiro</p>

                            <p class="mt-1 text-base font-semibold text-blue-900"><?php echo $fechaProgramadaLabel; ?></p>

                        </div>

                    <?php endif; ?>



                    <div class="mt-5 flex gap-3">

                        <form action="index.php?controller=Solicitud&action=confirmar" method="POST" class="flex-1" onsubmit="return confirm(',%%Confirmar esta solicitud? Se restar% el stock autom%ticamente.');">

                            <input type="hidden" name="id_solicitud_detalle" value="<?php echo $idDetalle; ?>">

                            <button 

                                type="submit"

                                class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-500/30 hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400"

                            >

                                 Confirmar

                            </button>

                        </form>

                        <form action="index.php?controller=Solicitud&action=rechazar" method="POST" class="flex-1" onsubmit="return confirm(',%%Rechazar esta solicitud?');">

                            <input type="hidden" name="id_solicitud_detalle" value="<?php echo $idDetalle; ?>">

                            <button 

                                type="submit"

                                class="w-full rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400"

                            >

                                 Rechazar

                            </button>

                        </form>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">

            <svg class="mx-auto h-12 w-12 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>

            </svg>

            <p class="mt-4 text-sm text-slate-500">No tienes solicitudes pendientes por el momento.</p>

        </div>

    <?php endif; ?>

</section>

