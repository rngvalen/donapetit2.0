<?php
declare(strict_types=1);
?>
<div class="min-h-screen bg-slate-100 py-12 px-4">
  <div class="mx-auto flex max-w-5xl flex-col gap-10">
    <header class="text-center">
      <p class="text-sm font-semibold uppercase tracking-widest text-brand">Centro de ayuda</p>
      <h1 class="mt-3 text-3xl font-bold text-slate-900">Preguntas frecuentes</h1>
      <p class="mt-2 text-base text-slate-600">
        Recopilamos las dudas que mas se repiten al empezar a usar DonAppetit.
        Si necesitas algo mas, podras escribirnos desde la seccion de contacto.
      </p>
    </header>

    <div class="space-y-4">
      <?php
      $faqs = [
        [
          'question' => 'Que es DonAppetit?',
          'answer' => 'Es una plataforma que conecta donantes de comida con instituciones receptoras para reducir el desperdicio de alimentos.'
        ],
        [
          'question' => 'Como me registro como donante?',
          'answer' => 'Elegis el rol Donante durante el registro, confirmas tu correo y completas el perfil con datos comerciales, direccion y ubicacion en el mapa.'
        ],
        [
          'question' => 'Que requisitos tiene un receptor?',
          'answer' => 'Necesita demostrar su actividad social (por ejemplo numero RENACOM) y detallar quien sera responsable del retiro.'
        ],
        [
          'question' => 'Puedo modificar mi perfil mas adelante?',
          'answer' => 'Si, desde el menu de usuario podes volver a editar los datos comerciales o la ubicacion en cualquier momento.'
        ],
        [
          'question' => 'Como funciona el mapa de donantes?',
          'answer' => 'Usamos tu ubicacion para mostrar negocios cercanos con stock disponible. Tambien podes filtrar por radio y estado.'
        ],
      ];
      ?>
      <?php foreach ($faqs as $faq): ?>
        <details class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md">
          <summary class="flex cursor-pointer items-center justify-between text-left text-lg font-semibold text-slate-900">
            <span><?php echo htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8'); ?></span>
            <span class="ml-4 text-slate-400 transition group-open:rotate-180">&#x25BE;</span>
          </summary>
          <p class="mt-3 text-sm leading-relaxed text-slate-600">
            <?php echo htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8'); ?>
          </p>
        </details>
      <?php endforeach; ?>
    </div>

    <div class="rounded-3xl border border-indigo-100 bg-white/80 p-6 shadow-inner">
      <h2 class="text-lg font-semibold text-slate-900">No encontraste la respuesta?</h2>
      <p class="mt-2 text-sm text-slate-600">
        Escribinos desde la seccion de contacto o envia un correo a
        <a class="font-medium text-indigo-600" href="mailto:ayuda@donappetit.com">ayuda@donappetit.com</a>.
      </p>
      <a href="?controller=Info&action=contacto"
         class="mt-4 inline-flex items-center gap-2 rounded-xl bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand/90">
        Abrir contacto
      </a>
    </div>
  </div>
</div>
