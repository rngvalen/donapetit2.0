<div class="min-h-screen bg-slate-100 py-12 px-4">
  <div class="max-w-4xl mx-auto">
    
    <!-- Header -->
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold text-slate-900 mb-4">Política de Privacidad</h1>
      <p class="text-lg text-slate-600">Última actualización: <?= date('d/m/Y') ?></p>
    </div>

    <!-- Contenido -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 md:p-12 shadow-xl space-y-8">
      
      <!-- Introducción -->
      <section>
        <h2 class="text-2xl font-semibold text-slate-900 mb-4">1. Introducción</h2>
        <p class="text-slate-700 leading-relaxed">
          En DonAppétit nos comprometemos a proteger tu privacidad y tus datos personales. Esta política describe cómo recopilamos, 
          usamos y protegemos la información que nos proporcionás al utilizar nuestra plataforma de donación de alimentos.
        </p>
      </section>

      <!-- Información que recopilamos -->
      <section>
        <h2 class="text-2xl font-semibold text-slate-900 mb-4">2. Información que Recopilamos</h2>
        <p class="text-slate-700 leading-relaxed mb-3">Recopilamos la siguiente información:</p>
        <ul class="list-disc list-inside space-y-2 text-slate-700 ml-4">
          <li>Información de registro: nombre, email, dirección y ubicación</li>
          <li>Información de perfil: tipo de usuario (donante o receptor), datos de contacto</li>
          <li>Información de donaciones: productos donados, cantidades, fechas</li>
          <li>Información de ubicación: coordenadas para mostrar donantes cercanos</li>
        </ul>
      </section>

      <!-- Uso de la información -->
      <section>
        <h2 class="text-2xl font-semibold text-slate-900 mb-4">3. Uso de la Información</h2>
        <p class="text-slate-700 leading-relaxed mb-3">Utilizamos tu información para:</p>
        <ul class="list-disc list-inside space-y-2 text-slate-700 ml-4">
          <li>Facilitar la conexión entre donantes y receptores</li>
          <li>Mostrar donantes cercanos en el mapa</li>
          <li>Gestionar y procesar donaciones</li>
          <li>Mejorar nuestros servicios y experiencia de usuario</li>
          <li>Enviar notificaciones importantes relacionadas con tu cuenta</li>
        </ul>
      </section>

      <!-- Protección de datos -->
      <section>
        <h2 class="text-2xl font-semibold text-slate-900 mb-4">4. Protección de Datos</h2>
        <p class="text-slate-700 leading-relaxed">
          Implementamos medidas de seguridad técnicas y organizativas para proteger tus datos personales contra acceso no autorizado, 
          pérdida o alteración. Tus contraseñas están encriptadas y nunca compartimos tu información con terceros sin tu consentimiento.
        </p>
      </section>

      <!-- Cookies -->
      <section>
        <h2 class="text-2xl font-semibold text-slate-900 mb-4">5. Cookies y Tecnologías Similares</h2>
        <p class="text-slate-700 leading-relaxed">
          Utilizamos cookies para mantener tu sesión activa y mejorar tu experiencia en la plataforma. 
          Podés configurar tu navegador para rechazar cookies, aunque esto puede afectar algunas funcionalidades del sitio.
        </p>
      </section>

      <!-- Derechos del usuario -->
      <section>
        <h2 class="text-2xl font-semibold text-slate-900 mb-4">6. Tus Derechos</h2>
        <p class="text-slate-700 leading-relaxed mb-3">Tenés derecho a:</p>
        <ul class="list-disc list-inside space-y-2 text-slate-700 ml-4">
          <li>Acceder a tus datos personales</li>
          <li>Rectificar información incorrecta</li>
          <li>Solicitar la eliminación de tu cuenta y datos</li>
          <li>Oponerte al procesamiento de tus datos</li>
          <li>Retirar tu consentimiento en cualquier momento</li>
        </ul>
      </section>

      <!-- Contacto -->
      <section>
        <h2 class="text-2xl font-semibold text-slate-900 mb-4">7. Contacto</h2>
        <p class="text-slate-700 leading-relaxed">
          Si tenés preguntas sobre esta política de privacidad o querés ejercer tus derechos, contactanos en 
          <a href="mailto:donappettit@ien.com" class="text-brand hover:underline font-medium">donappettit@ien.com</a>
        </p>
      </section>

    </div>

    <!-- Botón volver -->
    <div class="mt-8 text-center">
      <a href="?controller=Home&action=index" class="inline-flex items-center text-brand hover:underline font-medium">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Volver al inicio
      </a>
    </div>

  </div>
</div>

<style>
  .text-brand { color: #0F1629; }
</style>