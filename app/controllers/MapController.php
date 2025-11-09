<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../core/auth_session.php';
require_once __DIR__ . '/../model/Mapa.php';
require_once __DIR__ . '/../../config/bdconexion.php';

/**
 * Controlador para el mapa interactivo de donantes.
 */
class MapController extends Controller
{
    private Mapa $mapa;

    public function __construct()
    {
        $this->mapa = new Mapa();
    }

    public function index(): void
    {
        requireLogin();

        $usuario = current_user() ?? [];
        $userId = (int)($usuario['id'] ?? 0);
        $userName = $usuario['name'] ?? 'Usuario';
        $userRole = normalize_role($usuario['rol'] ?? null) ?? ROLE_DONANTE;

        $ubicacion = $this->resolveUserLocation($userId);
        $donantes = $this->mapa->obtenerDonantesActivos();

        if ($userRole === ROLE_DONANTE) {
            $donantes = array_values(
                array_filter(
                    $donantes,
                    static fn(array $donante): bool => (int)($donante['id_usuario'] ?? 0) !== $userId
                )
            );
        }

        $this->render(
            'map.map',
            [
                'userName' => $userName,
                'userRole' => $userRole,
                'userDir' => $ubicacion['direccion'],
                'userLat' => $ubicacion['lat'],
                'userLng' => $ubicacion['lng'],
                'donantes' => $this->formatDonorsForView($donantes),
            ],
            'none'
        );
    }

    /**
     * Obtiene la direccion del usuario o una ubicacion por defecto.
     *
     * @return array{lat:float,lng:float,direccion:string}
     */
    private function resolveUserLocation(int $userId): array
    {
        $default = [
            'lat' => -27.4692,
            'lng' => -58.8306,
            'direccion' => 'Corrientes, Argentina',
        ];

        if ($userId <= 0) {
            return $default;
        }

        try {
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare(
                'SELECT Latitud, Longitud, nom_calle, num_calle
                 FROM direcciones
                 WHERE id_usuario_direcc = :id
                 LIMIT 1'
            );
            $stmt->execute([':id' => $userId]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                return $default;
            }

            $direccion = trim((string)($row['nom_calle'] ?? ''));
            $numero = trim((string)($row['num_calle'] ?? ''));
            if ($numero !== '') {
                $direccion = trim($direccion . ' ' . $numero);
            }

            return [
                'lat' => isset($row['Latitud']) ? (float)$row['Latitud'] : $default['lat'],
                'lng' => isset($row['Longitud']) ? (float)$row['Longitud'] : $default['lng'],
                'direccion' => $direccion !== '' ? $direccion : $default['direccion'],
            ];
        } catch (\Throwable $exception) {
            error_log('No se pudo leer la direccion del usuario: ' . $exception->getMessage());
            return $default;
        }
    }

    /**
     * Normaliza los donantes para el mapa Leaflet.
     *
     * @param array<int,array<string,mixed>> $donantes
     * @return array<int,array<string,mixed>>
     */
    private function formatDonorsForView(array $donantes): array
    {
        return array_map(
            static function (array $donante): array {
                $productos = (int)($donante['total_productos'] ?? 0);

                return [
                    'id' => (int)($donante['id_usuario'] ?? 0),
                    'nombre' => (string)($donante['nombre'] ?? 'Donante'),
                    'lat' => (float)($donante['latitud'] ?? 0),
                    'lng' => (float)($donante['longitud'] ?? 0),
                    'direccion' => (string)($donante['direccion'] ?? 'Sin direccion'),
                    'productos' => $productos,
                    'estado' => $productos > 0 ? 'online' : 'offline',
                ];
            },
            $donantes
        );
    }
}
