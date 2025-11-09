<?php

declare(strict_types=1);



/**

 * @var array<int,array<string,string>> $ofertas

 * @var string|null $titulo

 */

$ofertas = $ofertas ?? [];

$titulo = $titulo ?? 'Productos disponibles';

?>



<section class="py-6 text-slate-800">

    <header class="flex items-center justify-between gap-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-400 px-4 py-3 text-white shadow-lg shadow-emerald-700/20">

        <a href="index.php?controller=Home&action=index" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 p-2 hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70" aria-label="Volver">

            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                <path d="M15 18l-6-6 6-6"></path>

            </svg>

        </a>



        <h1 class="text-lg font-semibold tracking-tight sm:text-xl">

            <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>

        </h1>



        <button type="button" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 p-2 hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70" aria-label="Menu">

            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                <path d="M3 6h18M3 12h18M3 18h18"></path>

            </svg>

        </button>

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



    <?php if (!empty($ofertas)): ?>

        <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

            <?php foreach ($ofertas as $oferta): ?>

                <?php

                $idStock = (int)($oferta['id_stock'] ?? 0);

                $nombre = htmlspecialchars((string)($oferta['nombre'] ?? 'Producto sin nombre'), ENT_QUOTES, 'UTF-8');

                $origen = htmlspecialchars((string)($oferta['origen'] ?? 'Origen desconocido'), ENT_QUOTES, 'UTF-8');

                $distancia = htmlspecialchars((string)($oferta['distancia'] ?? 'S/D'), ENT_QUOTES, 'UTF-8');

                $cantidadDisponible = (int)($oferta['cantidad_disponible'] ?? 0);

                $unidad = htmlspecialchars((string)($oferta['unidad'] ?? ''), ENT_QUOTES, 'UTF-8');

                $fechaVenc = $oferta['fecha_venc'] ?? null;

                

                $fechaVencLabel = 'Sin fecha';

                if ($fechaVenc) {

                    $fecha = \DateTime::createFromFormat('Y-m-d', $fechaVenc);

                    if ($fecha) {

                        $fechaVencLabel = 'Vence: ' . $fecha->format('d/m/Y');

                    }

                }

                ?>

                <article class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">

                    <div class="mb-4 grid h-32 place-items-center rounded-xl border-2 border-dashed border-slate-200 bg-gradient-to-br from-slate-50 to-white text-slate-400 transition group-hover:from-emerald-50 group-hover:text-emerald-500">

                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h3l2-3h8l2 3h3a2 2 0 0 1 2 2z"></path>

                            <circle cx="12" cy="13" r="4"></circle>

                        </svg>

                    </div>



                    <h2 class="text-center text-base font-semibold text-slate-900">

                        <?php echo $nombre; ?>

                    </h2>



                    <p class="mt-2 text-center text-sm text-slate-500">

                        <?php echo $origen; ?>

                        <span class="font-semibold text-slate-700">&middot; <?php echo $distancia; ?></span>

                    </p>



                    <div class="mt-3 flex items-center justify-center gap-2 text-sm">

                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-700 font-medium">

                            Disponible: <?php echo $cantidadDisponible; ?> <?php echo $unidad; ?>

                        </span>

                    </div>



                    <?php if ($fechaVenc): ?>

                        <p class="mt-2 text-center text-xs text-slate-400">

                            <?php echo $fechaVencLabel; ?>

                        </p>

                    <?php endif; ?>



                    <div class="mt-5 flex-1"></div>



                    <button 

                        type="button" 

                        onclick="abrirModalSolicitud(<?php echo $idStock; ?>, '<?php echo addslashes($nombre); ?>', <?php echo $cantidadDisponible; ?>, '<?php echo addslashes($unidad); ?>')"

                        class="mt-4 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-500/30 transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400"

                    >

                        Solicitar

                    </button>

                </article>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-sm text-slate-500">

            No hay ofertas disponibles por el momento. Vuelve m%s tarde para descubrir nuevos productos.

        </div>

    <?php endif; ?>

</section>



<!-- Modal para solicitar producto -->

<div id="modalSolicitud" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">

    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">

        <div class="flex items-center justify-between border-b border-slate-200 pb-4">

            <h3 class="text-lg font-semibold text-slate-900">Solicitar producto</h3>

            <button type="button" onclick="cerrarModalSolicitud()" class="text-slate-400 hover:text-slate-600">

                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                    <path d="M18 6L6 18M6 6l12 12"></path>

                </svg>

            </button>

        </div>



        <form action="index.php?controller=Solicitud&action=solicitar" method="POST" class="mt-4 space-y-4">

            <input type="hidden" id="modal_id_stock" name="id_stock" value="">



            <div>

                <label class="block text-sm font-medium text-slate-700">Producto</label>

                <p id="modal_nombre_producto" class="mt-1 text-base font-semibold text-slate-900"></p>

            </div>



            <div>

                <label class="block text-sm font-medium text-slate-700">Disponible</label>

                <p id="modal_cantidad_disponible" class="mt-1 text-base text-slate-600"></p>

            </div>



            <div>

                <label for="cantidad" class="block text-sm font-medium text-slate-700">Cantidad a solicitar *</label>

                <input 

                    type="number" 

                    id="cantidad" 

                    name="cantidad" 

                    min="1" 

                    required

                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"

                >

            </div>



            <div>

                <label for="fecha_programada" class="block text-sm font-medium text-slate-700">Fecha programada para retiro</label>

                <input 

                    type="date" 

                    id="fecha_programada" 

                    name="fecha_programada"

                    min="<?php echo date('Y-m-d'); ?>"

                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"

                >

            </div>



            <div class="flex gap-3 pt-4">

                <button 

                    type="button" 

                    onclick="cerrarModalSolicitud()"

                    class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"

                >

                    Cancelar

                </button>

                <button 

                    type="submit"

                    class="flex-1 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"

                >

                    Confirmar solicitud

                </button>

            </div>

        </form>

    </div>

</div>



<script>

function abrirModalSolicitud(idStock, nombreProducto, cantidadDisponible, unidad) {

    document.getElementById('modal_id_stock').value = idStock;

    document.getElementById('modal_nombre_producto').textContent = nombreProducto;

    document.getElementById('modal_cantidad_disponible').textContent = cantidadDisponible + ' ' + unidad + ' disponibles';

    document.getElementById('cantidad').max = cantidadDisponible;

    document.getElementById('cantidad').value = '';

    document.getElementById('fecha_programada').value = '';

    

    const modal = document.getElementById('modalSolicitud');

    modal.classList.remove('hidden');

    modal.classList.add('flex');

}



function cerrarModalSolicitud() {

    const modal = document.getElementById('modalSolicitud');

    modal.classList.add('hidden');

    modal.classList.remove('flex');

}



// Cerrar modal al hacer clic fuera

document.getElementById('modalSolicitud').addEventListener('click', function(e) {

    if (e.target === this) {

        cerrarModalSolicitud();

    }

});

</script>

