<div class="min-h-screen bg-slate-100 py-12 px-4">
  <div class="max-w-4xl mx-auto">
    
    <!-- Header -->
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold text-slate-900 mb-4">Contacto</h1>
      <p class="text-lg text-slate-600">¿Tenés dudas o consultas? Estamos para ayudarte</p>
    </div>

    <!-- Información de contacto -->
    <div class="grid gap-6 md:grid-cols-3 mb-12">
      
      <!-- Teléfono -->
      <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-xl text-center hover:shadow-2xl transition">
        <div class="w-16 h-16 bg-brand/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900 mb-2">Teléfono</h3>
        <a href="tel:+543624123456" class="text-brand hover:underline font-medium">
          +54 (362) 412-3456
        </a>
        <p class="text-sm text-slate-500 mt-2">Lun a Vie: 8:00 - 18:00hs</p>
      </div>

      <!-- Email -->
      <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-xl text-center hover:shadow-2xl transition">
        <div class="w-16 h-16 bg-brand/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900 mb-2">Email</h3>
        <a href="mailto:donappettit@ien.com" class="text-brand hover:underline font-medium break-all">
          donappettit@ien.com
        </a>
        <p class="text-sm text-slate-500 mt-2">Respondemos en 24-48hs</p>
      </div>

      <!-- Ubicación -->
      <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-xl text-center hover:shadow-2xl transition">
        <div class="w-16 h-16 bg-brand/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900 mb-2">Ubicación</h3>
        <p class="text-slate-700 font-medium">Mitre 280</p>
        <p class="text-sm text-slate-500 mt-1">Resistencia, Chaco</p>
        <p class="text-sm text-slate-500">Argentina</p>
      </div>

    </div>

    <!-- Formulario de contacto -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-xl">
      <h2 class="text-2xl font-semibold text-slate-900 mb-6">Envianos tu mensaje</h2>
      
      <form class="space-y-6">
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label for="nombre" class="block text-sm font-medium text-slate-700 mb-2">Nombre completo</label>
            <input type="text" id="nombre" name="nombre" 
                   class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand focus:ring focus:ring-brand/20 transition" 
                   placeholder="Juan Pérez" required>
          </div>
          <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email</label>
            <input type="email" id="email" name="email" 
                   class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand focus:ring focus:ring-brand/20 transition" 
                   placeholder="juan@ejemplo.com" required>
          </div>
        </div>

        <div>
          <label for="asunto" class="block text-sm font-medium text-slate-700 mb-2">Asunto</label>
          <input type="text" id="asunto" name="asunto" 
                 class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand focus:ring focus:ring-brand/20 transition" 
                 placeholder="¿En qué podemos ayudarte?" required>
        </div>

        <div>
          <label for="mensaje" class="block text-sm font-medium text-slate-700 mb-2">Mensaje</label>
          <textarea id="mensaje" name="mensaje" rows="6" 
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand focus:ring focus:ring-brand/20 transition resize-none" 
                    placeholder="Escribí tu consulta aquí..." required></textarea>
        </div>

        <button type="submit" 
                class="w-full bg-brand hover:bg-brand/90 text-white font-semibold py-3 px-6 rounded-xl transition shadow-lg hover:shadow-xl">
          Enviar mensaje
        </button>
      </form>
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
  .bg-brand { background-color: #0F1629; }
  .text-brand { color: #0F1629; }
  .border-brand { border-color: #0F1629; }
  .focus\:border-brand:focus { border-color: #0F1629; }
  .focus\:ring-brand\/20:focus { --tw-ring-color: rgba(15, 22, 41, 0.2); }
  .bg-brand\/10 { background-color: rgba(15, 22, 41, 0.1); }
</style>