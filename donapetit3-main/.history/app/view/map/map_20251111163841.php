<?php
$user = htmlspecialchars($userName ?? 'Usuario', ENT_QUOTES, 'UTF-8');
$rolTexto = $userRole === 'receptor' ? 'donantes' : 'otros donantes';
?>

<!-- Estilos adicionales para Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  #map { z-index: 1; }
</style>

<!-- Contenido del mapa -->
<section id="donapp-map" class="mx-auto w-full max-w-6xl py-8 px-4 flex flex-col gap-8">
  <header>
    <h1 class="text-3xl font-semibold text-slate-900">Mapa de <?= $rolTexto ?></h1>
    <p class="mt-2 max-w-2xl text-sm text-slate-500">
      Encontrá <?= $rolTexto ?> con productos disponibles cerca de tu ubicación.
    </p>
    <p class="mt-1 text-xs text-slate-400">
      📍 Tu ubicación: <strong><?= htmlspecialchars($userDir) ?></strong>
    </p>
  </header>

  <div class="grid gap-8 lg:grid-cols-[2fr,1fr]">
    <!-- Columna principal -->
    <div class="space-y-6">
      <!-- Mapa -->
      <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
        <p class="text-sm font-semibold text-slate-700">Negocios cercanos</p>
        <div id="map" class="mt-4 h-96 w-full rounded-2xl bg-slate-100 overflow-hidden"></div>
      </section>

      <!-- Filtros -->
      <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
        <h2 class="text-lg font-semibold text-slate-900">Filtros rápidos</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-3">
          <label class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100 cursor-pointer">
            <input type="checkbox" id="filtro_stock" class="h-4 w-4 rounded border-slate-300 text-brand accent-brand" />
            Con stock disponible
          </label>
          <label class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100 cursor-pointer">
            <input type="checkbox" id="filtro_cercanos" class="h-4 w-4 rounded border-slate-300 text-brand accent-brand" checked />
            Solo cercanos
          </label>
          <label class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100 cursor-pointer">
            <input type="checkbox" id="filtro_ordenar" class="h-4 w-4 rounded border-slate-300 text-brand accent-brand" />
            Ordenar por distancia
          </label>
        </div>
      </section>
    </div>

    <!-- Columna lateral -->
    <aside class="flex flex-col gap-6">
      <!-- Radio -->
      <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
        <h2 class="text-lg font-semibold text-slate-900">Radio de búsqueda</h2>
        <p class="mt-2 text-sm text-slate-500">Ajustá el alcance en kilómetros</p>
        <div class="mt-4 space-y-4">
          <input type="range" id="radius" name="radius" min="0.5" max="10" step="0.5" value="2.5"
            class="h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-200 accent-brand" />
          <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
            <span id="radius-value">2.5 km</span>
            <button type="button" id="btn-search"
              class="rounded-full bg-brand px-4 py-2 text-xs font-semibold uppercase text-white hover:bg-brand/90">
              Buscar
            </button>
          </div>
        </div>
      </section>

      <!-- Lista de negocios -->
      <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-slate-900">Negocios</h2>
          <span class="text-xs font-semibold uppercase text-brand" id="total-donantes">0 encontrados</span>
        </div>
        <ul class="mt-4 space-y-4 max-h-96 overflow-y-auto" id="business-list">
          <li class="text-center text-sm text-slate-500 py-8">Cargando...</li>
        </ul>
      </section>
    </aside>
  </div>
</section>

<!-- Scripts -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
  let map, circle, markers = [];
  let allDonantes = <?= json_encode($donantes ?? []) ?>;
  const userLocation = { 
    lat: <?= $userLat ?>, 
    lng: <?= $userLng ?> 
  };
  const userRole = '<?= $userRole ?>';

  function initMap() {
    map = L.map('map').setView([userLocation.lat, userLocation.lng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Marcador usuario (verde)
    const userIcon = L.divIcon({
      html: '<div style="background: #10b981; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
      iconSize: [30, 30], 
      iconAnchor: [15, 15]
    });
    
    L.marker([userLocation.lat, userLocation.lng], { icon: userIcon })
      .addTo(map)
      .bindPopup('<strong>Tu ubicación</strong><br><?= htmlspecialchars($userDir) ?>');

    // Círculo de radio
    circle = L.circle([userLocation.lat, userLocation.lng], {
      color: '#0F1629', 
      fillColor: '#0F1629', 
      fillOpacity: 0.1, 
      radius: 2500
    }).addTo(map);

    updateMarkers(allDonantes);
    updateList(allDonantes);
  }

  function updateMarkers(donantes) {
    markers.forEach(m => map.removeLayer(m));
    markers = [];
    
    donantes.forEach((d, i) => {
      const color = d.estado === 'online' ? '#0F1629' : '#94a3b8';
      const icon = L.divIcon({
        html: `<div style="background: ${color}; width: 26px; height: 26px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center; color: white; font-size: 10px; font-weight: bold;">${i + 1}</div>`,
        iconSize: [26, 26], 
        iconAnchor: [13, 13]
      });
      
      // Popup mejorado con botones
      let popupContent = `
        <div style="min-width: 220px;">
          <strong style="font-size: 14px; color: #0F1629;">${d.nombre}</strong><br>
          <p style="margin: 8px 0; color: #64748b; font-size: 12px;">
            📍 ${d.direccion}<br>
            📦 ${d.productos} producto(s) disponible(s)<br>
            📏 ${d.distancia || 'Calculando...'} km
          </p>
      `;

      // Mostrar badge si hay solicitudes pendientes
      if (userRole === 'receptor' && d.solicitudes_pendientes > 0) {
        popupContent += `
          <p style="margin-top: 8px; padding: 6px; background: #fef3c7; border-radius: 6px; color: #92400e; font-size: 11px; font-weight: 600;">
            ⏳ Tienes ${d.solicitudes_pendientes} solicitud(es) pendiente(s)
          </p>
        `;
      }

      // Botones de acción (solo para receptores)
      if (userRole === 'receptor') {
        popupContent += `
          <div style="display: flex; gap: 8px; margin-top: 12px;">
            <a href="index.php?controller=Producto&action=productosDisponibles#donante-${d.id}" 
               style="flex: 1; background: #0F1629; color: white; padding: 6px 12px; border-radius: 8px; text-align: center; text-decoration: none; font-size: 12px; font-weight: 600;">
              Ver productos
            </a>
            <a href="index.php?controller=Solicitud&action=crear&id_donante=${d.id}" 
               style="flex: 1; background: #10b981; color: white; padding: 6px 12px; border-radius: 8px; text-align: center; text-decoration: none; font-size: 12px; font-weight: 600;">
              Solicitar
            </a>
          </div>
        `;
      }

      popupContent += `</div>`;
      
      const marker = L.marker([d.lat, d.lng], { icon: icon })
        .addTo(map)
        .bindPopup(popupContent);
      
      markers.push(marker);
    });
  }

  function updateList(donantes) {
    const list = document.getElementById('business-list');
    const total = document.getElementById('total-donantes');
    
    total.textContent = `${donantes.length} encontrado${donantes.length !== 1 ? 's' : ''}`;
    
    if (donantes.length === 0) {
      list.innerHTML = '<li class="text-center text-sm text-slate-500 py-8">No hay donantes en este radio</li>';
      return;
    }
    
    list.innerHTML = donantes.map((d, i) => {
      let itemHTML = `
        <li class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:bg-slate-100 transition" data-lat="${d.lat}" data-lng="${d.lng}">
          <div class="flex items-start gap-3 cursor-pointer" onclick="map.setView([${d.lat}, ${d.lng}], 16); markers[${i}].openPopup();">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl ${d.estado === 'online' ? 'bg-brand text-white' : 'bg-slate-300 text-slate-600'} text-sm font-semibold flex-shrink-0">
              ${i + 1}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-slate-900 truncate">${d.nombre}</p>
              <p class="mt-1 text-xs text-slate-500">${d.distancia || ''} km • ${d.productos} producto(s)</p>
      `;

      // Badge de solicitudes pendientes
      if (userRole === 'receptor' && d.solicitudes_pendientes > 0) {
        itemHTML += `
              <p class="mt-2 text-xs bg-amber-100 text-amber-800 px-2 py-1 rounded-lg inline-block">
                ⏳ ${d.solicitudes_pendientes} pendiente(s)
              </p>
        `;
      }

      itemHTML += `
            </div>
            <span class="text-xs font-semibold ${d.estado === 'online' ? 'text-brand' : 'text-slate-400'} flex-shrink-0">
              ${d.estado === 'online' ? '●' : '○'}
            </span>
          </div>
      `;

      // Botones de acción (solo para receptores)
      if (userRole === 'receptor') {
        itemHTML += `
          <div class="flex gap-2 mt-3">
            <a href="index.php?controller=Producto&action=productosDisponibles#donante-${d.id}" 
               class="flex-1 text-center px-3 py-2 bg-brand text-white text-xs font-semibold rounded-lg hover:bg-brand/90 transition">
              Ver productos
            </a>
            <a href="index.php?controller=Solicitud&action=crear&id_donante=${d.id}" 
               class="flex-1 text-center px-3 py-2 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition">
              Solicitar
            </a>
          </div>
        `;
      }

      itemHTML += `</li>`;
      return itemHTML;
    }).join('');
  }

  function calcularDistancia(lat1, lng1, lat2, lng2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) + 
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
              Math.sin(dLng/2) * Math.sin(dLng/2);
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  }

  // Calcular distancias
  allDonantes = allDonantes.map(d => {
    d.distancia = calcularDistancia(userLocation.lat, userLocation.lng, d.lat, d.lng).toFixed(2);
    return d;
  });

  // Slider de radio
  const radiusSlider = document.getElementById('radius');
  const radiusValue = document.getElementById('radius-value');
  
  radiusSlider.addEventListener('input', function() {
    radiusValue.textContent = this.value + ' km';
    if (circle) circle.setRadius(parseFloat(this.value) * 1000);
  });

  // Botón buscar
  document.getElementById('btn-search').addEventListener('click', function() {
    const radio = parseFloat(radiusSlider.value);
    const filtrados = allDonantes.filter(d => parseFloat(d.distancia) <= radio);
    updateMarkers(filtrados);
    updateList(filtrados);
  });

  // Filtros
  document.getElementById('filtro_stock').addEventListener('change', function() {
    let filtrados = this.checked ? allDonantes.filter(d => d.productos > 0) : allDonantes;
    updateMarkers(filtrados);
    updateList(filtrados);
  });

  document.getElementById('filtro_ordenar').addEventListener('change', function() {
    let filtrados = [...allDonantes];
    if (this.checked) filtrados.sort((a, b) => parseFloat(a.distancia) - parseFloat(b.distancia));
    updateList(filtrados);
  });

  // Inicializar mapa
  initMap();
})();
</script>