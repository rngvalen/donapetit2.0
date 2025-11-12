<?php
$user = htmlspecialchars($userName ?? 'Usuario', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Completar Perfil - Donante</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { brand: '#0F1629' }
        }
      }
    };
  </script>
</head>
<body class="bg-slate-100">

<section class="py-10">
  <div class="mx-auto max-w-3xl">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl">
      
      <div class="text-center mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900">Completa tu perfil de Donante</h1>
        <p class="mt-2 text-sm text-slate-600">Ingresa los datos de tu negocio y ubicación</p>
      </div>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
          <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <form method="post" action="?controller=Profile&action=guardarDonante" class="space-y-6" id="formDonante">
        
        <!-- Datos del negocio -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="nombre_comercial" class="text-sm font-medium text-slate-700">Nombre Comercial *</label>
            <input 
              type="text" 
              id="nombre_comercial" 
              name="nombre_comercial" 
              required 
              placeholder="Ej: Panadería La Esquina"
              class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none">
          </div>

          <div>
            <label for="cuit" class="text-sm font-medium text-slate-700">CUIT *</label>
            <input 
              type="text" 
              id="cuit" 
              name="cuit" 
              required 
              placeholder="Ej: 20-12345678-9"
              maxlength="13"
              class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none">
          </div>
        </div>

        <!-- Dirección -->
        <div class="border-t pt-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">Dirección del negocio</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="nombre_calle" class="text-sm font-medium text-slate-700">Nombre de la calle *</label>
              <input 
                type="text" 
                id="nombre_calle" 
                name="nombre_calle" 
                required 
                placeholder="Ej: Av. Rivadavia"
                class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none">
            </div>

            <div>
              <label for="num_calle" class="text-sm font-medium text-slate-700">Número *</label>
              <input 
                type="text" 
                id="num_calle" 
                name="num_calle" 
                required 
                placeholder="Ej: 1234"
                class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none">
            </div>
          </div>
        </div>

        <!-- Mapa -->
        <div class="border-t pt-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-2">Ubicación exacta</h2>
          <p class="text-sm text-slate-600 mb-4">Arrastra el marcador para ajustar tu ubicación precisa</p>
          
          <div id="map" class="w-full h-64 rounded-xl border border-slate-200"></div>
          
          <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
              <label class="text-xs font-medium text-slate-600">Latitud</label>
              <input 
                type="text" 
                id="latitud" 
                name="latitud" 
                readonly
                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-slate-50">
            </div>
            <div>
              <label class="text-xs font-medium text-slate-600">Longitud</label>
              <input 
                type="text" 
                id="longitud" 
                name="longitud" 
                readonly
                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-slate-50">
            </div>
          </div>
        </div>

        <button 
          type="submit"
          class="w-full rounded-full bg-brand px-5 py-3 text-sm font-semibold text-white hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-brand/50">
          Guardar y continuar
        </button>

      </form>

    </div>
  </div>
</section>

<script>
// Inicializar mapa
let map, marker;
let defaultLat = -27.4692; // Corrientes, Argentina
let defaultLng = -58.8306;

// Obtener ubicación del usuario
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(
    (position) => {
      defaultLat = position.coords.latitude;
      defaultLng = position.coords.longitude;
      initMap(defaultLat, defaultLng);
    },
    () => {
      initMap(defaultLat, defaultLng);
    }
  );
} else {
  initMap(defaultLat, defaultLng);
}

function initMap(lat, lng) {
  map = L.map('map').setView([lat, lng], 15);
  
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);
  
  // Marcador arrastrable
  marker = L.marker([lat, lng], { draggable: true }).addTo(map);
  
  // Actualizar inputs
  updateCoordinates(lat, lng);
  
  // Evento al arrastrar marcador
  marker.on('dragend', function(e) {
    const pos = marker.getLatLng();
    updateCoordinates(pos.lat, pos.lng);
  });
  
  // Evento al hacer clic en el mapa
  map.on('click', function(e) {
    marker.setLatLng(e.latlng);
    updateCoordinates(e.latlng.lat, e.latlng.lng);
  });
}

function updateCoordinates(lat, lng) {
  document.getElementById('latitud').value = lat.toFixed(6);
  document.getElementById('longitud').value = lng.toFixed(6);
}
</script>

</body>
</html>