<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../core/auth_session.php';
require_once __DIR__ . '/../services/ProfileService.php';

class ProfileController extends Controller
{
    private ProfileService $profiles;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->profiles = new ProfileService();
    }

    public function completarPerfil(): void
    {
        requireLogin();

        $user = current_user() ?? [];
        $userId = (int)($user['id'] ?? 0);
        $role = strtolower((string)($user['rol'] ?? 'donante'));
        $userName = $user['name'] ?? 'Usuario';

        $needsProfile = $this->profiles->needsCompletion($userId, $role);

        $errorMessage = $_SESSION['error'] ?? null;
        $successMessage = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);

        [$extraHead, $extraScripts] = $this->leafletAssets();

        $view = $role === 'receptor'
            ? 'profile.completar_receptor'
            : 'profile.completar_donante';

        $this->render(
            $view,
            [
                'userName' => $userName,
                'needsProfile' => $needsProfile,
                'errorMessage' => $errorMessage,
                'successMessage' => $successMessage,
                'extraHeadHtml' => $extraHead,
                'extraBodyScripts' => $extraScripts,
            ]
        );
    }

    public function guardarDonante(): void
    {
        requireLogin();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('?controller=Profile&action=completarPerfil');
        }

        $user = current_user() ?? [];
        $userId = (int)($user['id'] ?? 0);

        $nombreComercial = trim($_POST['nombre_comercial'] ?? '');
        $cuit = trim($_POST['cuit'] ?? '');
        $nombreCalle = trim($_POST['nombre_calle'] ?? '');
        $numCalle = trim($_POST['num_calle'] ?? '');
        $latitud = $this->sanitizeCoordinate($_POST['latitud'] ?? null);
        $longitud = $this->sanitizeCoordinate($_POST['longitud'] ?? null);

        $errores = $this->validarCamposObligatorios([
            'nombre comercial' => $nombreComercial,
            'CUIT' => $cuit,
            'nombre de la calle' => $nombreCalle,
            'nmero de la calle' => $numCalle,
        ]);

        if ($numCalle !== '' && !ctype_digit($numCalle)) {
            $errores[] = 'El nmero de la calle debe ser numrico.';
        }

        if (!empty($errores)) {
            $_SESSION['error'] = implode(' ', $errores);
            $this->redirect('?controller=Profile&action=completarPerfil');
        }

        try {
            $this->profiles->saveDonante($userId, [
                'nombre_comercial' => $nombreComercial,
                'cuit' => $cuit,
                'nombre_calle' => $nombreCalle,
                'num_calle' => (int)$numCalle,
                'latitud' => $latitud,
                'longitud' => $longitud,
            ]);

            unset($_SESSION['profile_pending']);
            $_SESSION['success'] = 'Perfecto! Guardamos los datos de tu perfil de donante.';
            $this->redirect('index.php?controller=Home&action=index');
        } catch (\Throwable $exception) {
            error_log('Error al guardar perfil de donante: ' . $exception->getMessage());
            $_SESSION['error'] = 'No pudimos guardar el perfil. Intent nuevamente.';
            $this->redirect('?controller=Profile&action=completarPerfil');
        }
    }

    public function guardarReceptor(): void
    {
        requireLogin();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('?controller=Profile&action=completarPerfil');
        }

        $user = current_user() ?? [];
        $userId = (int)($user['id'] ?? 0);

        $numRenacom = trim($_POST['num_renacom'] ?? '');
        $nombreInstitucion = trim($_POST['nom_institucion'] ?? '');
        $responsable = trim($_POST['responsable'] ?? '');
        $nombreCalle = trim($_POST['nombre_calle'] ?? '');
        $numCalle = trim($_POST['num_calle'] ?? '');
        $latitud = $this->sanitizeCoordinate($_POST['latitud'] ?? null);
        $longitud = $this->sanitizeCoordinate($_POST['longitud'] ?? null);

        $errores = $this->validarCamposObligatorios([
            'nmero RENACOM' => $numRenacom,
            'nombre de la institucin' => $nombreInstitucion,
            'responsable' => $responsable,
            'nombre de la calle' => $nombreCalle,
            'nmero de la calle' => $numCalle,
        ]);

        if ($numCalle !== '' && !ctype_digit($numCalle)) {
            $errores[] = 'El nmero de la calle debe ser numrico.';
        }

        if (!empty($errores)) {
            $_SESSION['error'] = implode(' ', $errores);
            $this->redirect('?controller=Profile&action=completarPerfil');
        }

        try {
            $this->profiles->saveReceptor($userId, [
                'num_renacom' => $numRenacom,
                'nom_institucion' => $nombreInstitucion,
                'responsable' => $responsable,
                'nombre_calle' => $nombreCalle,
                'num_calle' => (int)$numCalle,
                'latitud' => $latitud,
                'longitud' => $longitud,
            ]);

            unset($_SESSION['profile_pending']);
            $_SESSION['success'] = 'Guardamos los datos de tu institucin.';
            $this->redirect('index.php?controller=Home&action=index');
        } catch (\Throwable $exception) {
            error_log('Error al guardar perfil de receptor: ' . $exception->getMessage());
            $_SESSION['error'] = 'No pudimos guardar el perfil. Intent nuevamente.';
            $this->redirect('?controller=Profile&action=completarPerfil');
        }
    }

    private function validarCamposObligatorios(array $campos): array
    {
        $errores = [];
        foreach ($campos as $nombre => $valor) {
            if ($valor === '') {
                $errores[] = "El campo {$nombre} es obligatorio.";
            }
        }

        return $errores;
    }

    private function sanitizeCoordinate($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $number = filter_var($value, FILTER_VALIDATE_FLOAT);
        return $number === false ? null : (float)$number;
    }

    private function leafletAssets(): array
    {
        $head = <<<HTML
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-sA+4J1rLQia+1sTB0trJQq4V0X1LyoSgS2Fk8CMmY3A="
      crossorigin="" />
HTML;

        $body = <<<HTML
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-o9N1j7kGPrL4GsI6Y3mxFNaR8HGNpPe0uhZ5gGLbYwY="
        crossorigin=""></script>
<script>
(function () {
  const mapElement = document.getElementById('map');
  if (!mapElement) return;

  const latInput = document.getElementById('latitud');
  const lngInput = document.getElementById('longitud');

  const initialLat = latInput && latInput.value ? parseFloat(latInput.value) : -27.4692;
  const initialLng = lngInput && lngInput.value ? parseFloat(lngInput.value) : -58.8306;

  const map = L.map(mapElement).setView([initialLat, initialLng], 14);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  const marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

  function updateInputs(lat, lng) {
    if (latInput) latInput.value = lat.toFixed(6);
    if (lngInput) lngInput.value = lng.toFixed(6);
  }

  updateInputs(initialLat, initialLng);

  marker.on('dragend', function () {
    const pos = marker.getLatLng();
    updateInputs(pos.lat, pos.lng);
  });

  map.on('click', function (event) {
    marker.setLatLng(event.latlng);
    updateInputs(event.latlng.lat, event.latlng.lng);
  });

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      function (position) {
        const { latitude, longitude } = position.coords;
        map.setView([latitude, longitude], 15);
        marker.setLatLng([latitude, longitude]);
        updateInputs(latitude, longitude);
      },
      function () { /* Ignorar errores de geolocalizacin */ },
      { enableHighAccuracy: true, timeout: 5000 }
    );
  }
})();
</script>
HTML;

        return [$head, $body];
    }
}
