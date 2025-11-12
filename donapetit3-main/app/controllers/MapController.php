<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../../config/bdconexion.php';

class Mapcontroller extends Controller
{
    /**
     * Muestra el mapa con donantes cercanos
     */
    public function index(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        $userName = $_SESSION['user']['name'];
        $userId = $_SESSION['user']['id'];
        $userRole = $_SESSION['user']['rol'] ?? 'donante';

        // Obtener ubicación del usuario actual
        $ubicacionUsuario = $this->obtenerUbicacionUsuario($userId);

        // Obtener donantes cercanos
        $donantes = $this->obtenerDonantes($userId, $userRole);

        // Debug temporal
        error_log("=== MAP DEBUG ===");
        error_log("User ID: " . $userId);
        error_log("User Role: " . $userRole);
        error_log("Ubicación Usuario: " . json_encode($ubicacionUsuario));
        error_log("Total Donantes: " . count($donantes));
        error_log("Donantes: " . json_encode($donantes));

        // Pasar variables correctamente a la vista
        $userLat = $ubicacionUsuario['lat'];
        $userLng = $ubicacionUsuario['lng'];
        $userDir = $ubicacionUsuario['direccion'];

        // Renderizar la vista directamente
        require __DIR__ . '/../view/map/map.php';
    }

    /**
     * Obtiene la ubicación del usuario desde la tabla direcciones
     */
    private function obtenerUbicacionUsuario(int $userId): array
    {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            $sql = "SELECT Latitud, Longitud, nom_calle, num_calle 
                    FROM direcciones 
                    WHERE id_usuario_direcc = :id";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $userId, \PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result && $result['Latitud'] && $result['Longitud']) {
                return [
                    'lat' => (float)$result['Latitud'],
                    'lng' => (float)$result['Longitud'],
                    'direccion' => trim(($result['nom_calle'] ?? '') . ' ' . ($result['num_calle'] ?? ''))
                ];
            }

            // Si no tiene dirección, usar coordenadas por defecto (Corrientes)
            return [
                'lat' => -27.4692,
                'lng' => -58.8306,
                'direccion' => 'Ubicación no configurada'
            ];

        } catch (\PDOException $e) {
            error_log("Error al obtener ubicación: " . $e->getMessage());
            return [
                'lat' => -27.4692,
                'lng' => -58.8306,
                'direccion' => 'Error al cargar ubicación'
            ];
        }
    }

    /**
     * Obtiene lista de donantes con sus ubicaciones (VERSIÓN SIMPLE)
     */
    private function obtenerDonantes(int $userId, string $userRole): array
    {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Query SIMPLE sin contar productos
            $sql = "SELECT 
                        u.id_usuario,
                        u.Nombre,
                        d.nom_comercial,
                        dir.Latitud,
                        dir.Longitud,
                        dir.nom_calle,
                        dir.num_calle
                    FROM usuarios u
                    INNER JOIN donante d ON u.id_usuario = d.id_usu_donante
                    INNER JOIN direcciones dir ON u.id_usuario = dir.id_usuario_direcc
                    WHERE u.rol = 'donante' 
                    AND dir.Latitud IS NOT NULL 
                    AND dir.Longitud IS NOT NULL
                    AND dir.Latitud != 0
                    AND dir.Longitud != 0";

            // Si es donante, excluir su propia ubicación
            if ($userRole === 'donante') {
                $sql .= " AND u.id_usuario != :userId";
            }

            $sql .= " ORDER BY u.Nombre";

            $stmt = $conn->prepare($sql);

            if ($userRole === 'donante') {
                $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
            }

            $stmt->execute();

            $donantes = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $donantes[] = [
                    'id' => (int)$row['id_usuario'],
                    'nombre' => $row['nom_comercial'] ?? $row['Nombre'],
                    'lat' => (float)$row['Latitud'],
                    'lng' => (float)$row['Longitud'],
                    'direccion' => trim(($row['nom_calle'] ?? '') . ' ' . ($row['num_calle'] ?? '')),
                    'productos' => 0, // Por ahora 0
                    'estado' => 'online' // Todos activos
                ];
            }

            error_log("SQL ejecutado: " . $sql);
            error_log("Donantes encontrados: " . count($donantes));

            return $donantes;

        } catch (\PDOException $e) {
            error_log("Error al obtener donantes: " . $e->getMessage());
            error_log("SQL que falló: " . ($sql ?? 'N/A'));
            return [];
        }
    }
}