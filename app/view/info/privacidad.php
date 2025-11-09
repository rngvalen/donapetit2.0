<?php
declare(strict_types=1);
?>
<div class="min-h-screen bg-slate-50 py-12 px-4">
  <article class="mx-auto max-w-4xl rounded-3xl bg-white p-8 shadow-xl shadow-slate-900/5">
    <header class="border-b border-slate-200 pb-6">
      <p class="text-xs font-semibold uppercase tracking-widest text-emerald-600">Privacidad</p>
      <h1 class="mt-2 text-3xl font-bold text-slate-900">Politica de tratamiento de datos</h1>
      <p class="mt-2 text-sm text-slate-600">
        Resumen claro de como almacenamos y protegemos la informacion de los usuarios de DonAppetit.
      </p>
    </header>

    <section class="mt-8 space-y-6 text-sm leading-relaxed text-slate-700">
      <div>
        <h2 class="text-lg font-semibold text-slate-900">Datos que recopilamos</h2>
        <ul class="mt-2 list-disc space-y-1 pl-5">
          <li>Datos de cuenta: nombre, email, telefono y rol (donante o receptor).</li>
          <li>Datos de perfil: nombre comercial, numero RENACOM, direccion y geolocalizacion.</li>
          <li>Registros operativos: productos cargados, solicitudes y confirmaciones.</li>
        </ul>
      </div>

      <div>
        <h2 class="text-lg font-semibold text-slate-900">Para que usamos la informacion</h2>
        <p class="mt-2">
          Utilizamos los datos para operar la plataforma, validar perfiles, mostrar ubicaciones en el mapa,
          facilitar las solicitudes entre usuarios y enviar notificaciones relevantes.
        </p>
      </div>

      <div>
        <h2 class="text-lg font-semibold text-slate-900">Compartir datos</h2>
        <p class="mt-2">
          Solo compartimos informacion entre usuarios cuando es necesario para concretar una donacion
          (por ejemplo el nombre y direccion del donante al receptor que solicitara el retiro). No vendemos
          datos a terceros ni usamos la informacion con fines publicitarios externos.
        </p>
      </div>

      <div>
        <h2 class="text-lg font-semibold text-slate-900">Tus derechos</h2>
        <p class="mt-2">
          Podes solicitar la actualizacion o eliminacion de tus datos escribiendo a
          <a class="font-medium text-indigo-600" href="mailto:privacidad@donappetit.com">privacidad@donappetit.com</a>.
          Eliminaremos la informacion salvo que exista una obligacion legal para conservarla.
        </p>
      </div>
    </section>

    <footer class="mt-8 rounded-2xl bg-slate-900/90 p-5 text-sm text-white">
      Ultima actualizacion: <?php echo date('d/m/Y'); ?>. Ante cualquier consulta adicional
      podes revisar el centro de ayuda o escribirnos desde la seccion de contacto.
    </footer>
  </article>
</div>
