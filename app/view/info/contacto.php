<?php
declare(strict_types=1);
?>
<div class="min-h-screen bg-slate-100 py-12 px-4">
  <div class="mx-auto grid max-w-5xl gap-8 md:grid-cols-[2fr,1fr]">
    <section class="rounded-3xl bg-white p-8 shadow-xl shadow-slate-900/5">
      <h1 class="text-3xl font-bold text-slate-900">Contactanos</h1>
      <p class="mt-2 text-sm text-slate-600">
        Completa el formulario y te respondemos dentro de las proximas 48 horas habiles.
        Tambien podes escribirnos directo al correo de soporte.
      </p>

      <form class="mt-6 space-y-5">
        <div>
          <label class="text-sm font-medium text-slate-700" for="contacto_nombre">Nombre completo</label>
          <input id="contacto_nombre" type="text" required
                 class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20"
                 placeholder="Tu nombre" />
        </div>

        <div>
          <label class="text-sm font-medium text-slate-700" for="contacto_email">Email</label>
          <input id="contacto_email" type="email" required
                 class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20"
                 placeholder="correo@ejemplo.com" />
        </div>

        <div>
          <label class="text-sm font-medium text-slate-700" for="contacto_mensaje">Mensaje</label>
          <textarea id="contacto_mensaje" rows="4" required
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20"
                    placeholder="Comentanos en que podemos ayudarte"></textarea>
        </div>

        <button type="submit"
                class="inline-flex items-center justify-center rounded-2xl bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand/20 hover:bg-brand/90">
          Enviar
        </button>
      </form>
    </section>

    <aside class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white/80 p-6 shadow-sm">
      <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Correo</p>
        <a class="mt-1 block text-base font-medium text-indigo-600" href="mailto:hola@donappetit.com">
          hola@donappetit.com
        </a>
      </div>
      <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Telefono</p>
        <p class="mt-1 text-base text-slate-700">+54 379 400 0000</p>
      </div>
      <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Direccion</p>
        <p class="mt-1 text-base text-slate-700">Av. Principal 1234, Corrientes</p>
      </div>
      <div class="rounded-2xl bg-slate-900/90 p-4 text-white">
        <p class="text-sm font-semibold">Necesitas ayuda rapida?</p>
        <p class="mt-1 text-xs text-white/80">Visita el centro de ayuda para ver las guias paso a paso.</p>
        <a href="?controller=Info&action=ayuda"
           class="mt-3 inline-flex items-center gap-2 rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold">
          Abrir centro de ayuda
        </a>
      </div>
    </aside>
  </div>
</div>
