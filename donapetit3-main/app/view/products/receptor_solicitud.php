<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Solicitar Productos - DonAppetit</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { brand: '#3D538F' },
          fontFamily: { sans: ['Montserrat', 'Inter', 'system-ui', 'sans-serif'] },
          boxShadow: { soft: '0 20px 60px rgba(15,22,41,.12)' }
        }
      }
    };
  </script>
  <style>
    * { transition: all .15s ease-in-out; }
  </style>
</head>

<body class="bg-slate-100 text-slate-900 min-h-screen font-sans py-8">
  <main class="mx-auto w-full max-w-6xl px-4">
    <header class="mb-8">
      <h1 class="text-3xl font-semibold text-slate-900">Solicitar Productos</h1>
      <p class="mt-2 max-w-2xl text-sm text-slate-500">
        Seleccioná los productos que necesitás y solicitá tu pedido a los negocios disponibles.
      </p>
    </header>

    <!-- Grid de productos -->
    <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      
      <!-- Producto 1 - Con imagen y botón -->
      <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 hover:shadow-2xl">
        <div class="mb-4 overflow-hidden rounded-2xl bg-slate-100">
          <img src="https://via.placeholder.com/300x200/3D538F/ffffff?text=Producto" 
               alt="Producto" 
               class="h-48 w-full object-cover" />
        </div>
        <h3 class="text-lg font-semibold text-slate-900">Nombre del Producto</h3>
        <p class="mt-2 text-sm text-slate-500">Descripción breve del producto disponible.</p>
        <div class="mt-4 flex items-center justify-between text-sm">
          <span class="font-semibold text-brand">Disponible</span>
          <span class="text-slate-600">2.5 km</span>
        </div>
        <button class="mt-4 w-full rounded-full bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand/90 shadow-lg shadow-brand/20">
          Solicitar
        </button>
      </article>

      <!-- Producto 2 - Vacío -->
      <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 hover:shadow-2xl">
        <div class="mb-4 flex h-48 items-center justify-center rounded-2xl bg-slate-100">
          <svg class="h-16 w-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900">Nombre del Producto</h3>
        <p class="mt-2 text-sm text-slate-500">Descripción breve del producto disponible.</p>
        <div class="mt-4 flex items-center justify-between text-sm">
          <span class="font-semibold text-brand">Disponible</span>
          <span class="text-slate-600">1.8 km</span>
        </div>
        <button class="mt-4 w-full rounded-full bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand/90 shadow-lg shadow-brand/20">
          Solicitar
        </button>
      </article>

      <!-- Producto 3 - Vacío -->
      <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 hover:shadow-2xl">
        <div class="mb-4 flex h-48 items-center justify-center rounded-2xl bg-slate-100">
          <svg class="h-16 w-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900">Nombre del Producto</h3>
        <p class="mt-2 text-sm text-slate-500">Descripción breve del producto disponible.</p>
        <div class="mt-4 flex items-center justify-between text-sm">
          <span class="font-semibold text-brand">Disponible</span>
          <span class="text-slate-600">3.2 km</span>
        </div>
        <button class="mt-4 w-full rounded-full bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand/90 shadow-lg shadow-brand/20">
          Solicitar
        </button>
      </article>

      <!-- Producto 4 - Vacío -->
      <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 hover:shadow-2xl">
        <div class="mb-4 flex h-48 items-center justify-center rounded-2xl bg-slate-100">
          <svg class="h-16 w-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900">Nombre del Producto</h3>
        <p class="mt-2 text-sm text-slate-500">Descripción breve del producto disponible.</p>
        <div class="mt-4 flex items-center justify-between text-sm">
          <span class="font-semibold text-slate-400">Sin stock</span>
          <span class="text-slate-600">4.1 km</span>
        </div>
        <button disabled class="mt-4 w-full rounded-full bg-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-500 cursor-not-allowed">
          No disponible
        </button>
      </article>

      <!-- Producto 5 - Vacío -->
      <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 hover:shadow-2xl">
        <div class="mb-4 flex h-48 items-center justify-center rounded-2xl bg-slate-100">
          <svg class="h-16 w-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900">Nombre del Producto</h3>
        <p class="mt-2 text-sm text-slate-500">Descripción breve del producto disponible.</p>
        <div class="mt-4 flex items-center justify-between text-sm">
          <span class="font-semibold text-brand">Disponible</span>
          <span class="text-slate-600">1.5 km</span>
        </div>
        <button class="mt-4 w-full rounded-full bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand/90 shadow-lg shadow-brand/20">
          Solicitar
        </button>
      </article>

      <!-- Producto 6 - Vacío -->
      <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 hover:shadow-2xl">
        <div class="mb-4 flex h-48 items-center justify-center rounded-2xl bg-slate-100">
          <svg class="h-16 w-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900">Nombre del Producto</h3>
        <p class="mt-2 text-sm text-slate-500">Descripción breve del producto disponible.</p>
        <div class="mt-4 flex items-center justify-between text-sm">
          <span class="font-semibold text-brand">Disponible</span>
          <span class="text-slate-600">2.9 km</span>
        </div>
        <button class="mt-4 w-full rounded-full bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand/90 shadow-lg shadow-brand/20">
          Solicitar
        </button>
      </article>

    </section>
  </main>

  <script>
    // Funcionalidad para los botones
    const buttons = document.querySelectorAll('button:not([disabled])');
    buttons.forEach(btn => {
      btn.addEventListener('click', (e) => {
        const card = e.target.closest('article');
        const productName = card.querySelector('h3').textContent;
        alert(`Solicitando: ${productName}`);
      });
    });
  </script>
</body>
</html>