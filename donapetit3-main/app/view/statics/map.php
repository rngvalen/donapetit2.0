<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mapa de donantes - DonAppetit</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
    #map { z-index: 1; }
  </style>
</head>

<body class="bg-slate-100 text-slate-900 min-h-screen font-sans">
  <section
    id="donapp-map"
    class="mx-auto w-full max-w-5xl py-8 px-4 flex flex-col gap-8"
    data-map-config='{"center":{"lat":-34.6037,"lng":-58.3816},"zoom":14,"radius":2.5,"radiusMin":0.5,"radiusMax":5,"radiusStep":0.5,"businesses":[{"nombre":"Supermercado El Molino","distancia":"820 m","detalle":"5 productos disponibles","estado":"online"},{"nombre":"Supermercado La Esquina","distancia":"950 m","detalle":"10 productos disponibles","estado":"online"},{"nombre":"Supermercado Don Pedro","distancia":"1.4 km","detalle":"Sin stock","estado":"offline"}]}'
  >
    <header>
      <h1 class="text-3xl font-semibold text-slate-900">Mapa de donantes</h1>
      <p class="mt-2 max-w-2xl text-sm text-slate-500">
        Encontrá negocios con productos disponibles cerca de tu ubicación y ajustá el radio de búsqueda según tus necesidades.
      </p>
    </header>

    <div class="grid gap-8 lg:grid-cols-[2fr,1fr]">
      <!-- Columna principal -->
      <div class="space-y-6">
        <!-- Mapa -->
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
          <p class="text-sm font-semibold text-slate-700">Negocios cercanos</p>
          <div id="map" class="mt-4 h-96 w-full rounded-2xl bg-slate-100 overflow-hidden"></div>
        </section>

        <!-- Filtros -->
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
          <h2 class="text-lg font-semibold text-slate-900">Filtros rápidos</h2>
          <div class="mt-4 grid gap-3 sm:grid-cols-3">
            <label class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100 cursor-pointer">
              <input type="checkbox" name="filtro_stock" class="h-4 w-4 rounded border-slate-300 text-brand accent-brand focus:ring-brand" />
              Con stock disponible
            </label>
            <label class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100 cursor-pointer">
              <input type="checkbox" name="filtro_abierto" class="h-4 w-4 rounded border-slate-300 text-brand accent-brand focus:ring-brand" />
              Abierto ahora
            </label>
            <label class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100 cursor-pointer">
              <input type="checkbox" name="filtro_favoritos" class="h-4 w-4 rounded border-slate-300 text-brand accent-brand focus:ring-brand" />
              Favoritos
            </label>
          </div>
        </section>
      </div>

      <!-- Columna lateral -->
      <aside class="flex flex-col gap-6">
        <!-- Radio -->
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
          <h2 class="text-lg font-semibold text-slate-900">Radio de búsqueda</h2>
          <p class="mt-2 text-sm text-slate-500">Ajustá el alcance en kilómetros para refinar los resultados.</p>

          <div class="mt-4 space-y-4">
            <input
              type="range"
              id="radius"
              name="radius"
              class="h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-200 accent-brand focus:outline-none focus:ring-2 focus:ring-brand"
            />
            <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
              <span id="radius-value"></span>
              <button
                type="button"
                id="btn-search"
                class="rounded-full bg-brand px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-sm transition hover:bg-brand/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand"
              >
                Buscar
              </button>
            </div>
          </div>
        </section>

        <!-- Lista de negocios -->
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Negocios</h2>
            <span class="text-xs font-semibold uppercase text-brand">Actualizado</span>
          </div>

          <ul class="mt-4 space-y-4" id="business-list">
            <!-- Items generados por JS -->
          </ul>
        </section>
      </aside>
    </div>
  </section>

  <script>
    (function () {
      const root = document.getElementById('donapp-map');
      if (!root) return;
      
      let map, circle;
      
      try {
        const cfg = JSON.parse(root.dataset.mapConfig || '{}');
        
        // Configurar slider
        const radius = document.getElementById('radius');
        const out = document.getElementById('radius-value');
        if (radius && out) {
          radius.min = cfg.radiusMin ?? 0.5;
          radius.max = cfg.radiusMax ?? 5;
          radius.step = cfg.radiusStep ?? 0.5;
          radius.value = cfg.radius ?? 2.5;
          out.textContent = `${radius.value} km`;
          radius.addEventListener('input', () => {
            out.textContent = `${radius.value} km`;
            if (circle) {
              circle.setRadius(parseFloat(radius.value) * 1000);
            }
          });
        }
        
        // Inicializar mapa
        const center = cfg.center || { lat: -34.6037, lng: -58.3816 };
        map = L.map('map').setView([center.lat, center.lng], cfg.zoom || 14);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Marcador central
        L.marker([center.lat, center.lng])
          .addTo(map)
          .bindPopup('Tu ubicación');
        
        // Círculo de radio
        circle = L.circle([center.lat, center.lng], {
          color: '#3D538F',
          fillColor: '#3D538F',
          fillOpacity: 0.1,
          radius: (cfg.radius || 2.5) * 1000
        }).addTo(map);
        
        // Generar lista de negocios
        const businessList = document.getElementById('business-list');
        if (businessList && cfg.businesses) {
          businessList.innerHTML = cfg.businesses.map((b, i) => `
            <li class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:bg-slate-100 transition">
              <div class="flex h-10 w-10 items-center justify-center rounded-xl ${b.estado === 'online' ? 'bg-brand text-white' : 'bg-slate-300 text-slate-600'} text-sm font-semibold">
                ${i + 1}
              </div>
              <div class="flex-1">
                <p class="text-sm font-semibold text-slate-900">${b.nombre}</p>
                <p class="mt-1 text-xs text-slate-500">${b.distancia} • ${b.detalle}</p>
              </div>
              <span class="text-xs font-semibold ${b.estado === 'online' ? 'text-brand' : 'text-slate-400'}">${b.estado === 'online' ? '●' : '○'}</span>
            </li>
          `).join('');
        }
        
        // Botón buscar
        const btnSearch = document.getElementById('btn-search');
        if (btnSearch) {
          btnSearch.addEventListener('click', () => {
            alert('Buscando negocios en un radio de ' + radius.value + ' km');
          });
        }
        
      } catch (e) {
        console.error('Error al inicializar el mapa:', e);
      }
    })();
  </script>
</body>
</html>