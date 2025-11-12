<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../../config/bdconexion.php';

class Mapcontroller extends Controller
{
    public function index(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('?controller=Auth&action=mostrarLogin');
            return;
        }

        $userName = $_SESSION['user']['name'] ?? 'Usuario';
        $userId = (int)$_SESSION['user']['id'];
        $userRole = $_SESSION['user']['rol'] ?? 'receptor';

        // Obtener ubicación del usuario
        $ubicacionUsuario = $this->obtenerUbicacionUsuario($userId);
        
        // Obtener lista de donantes
        $donantes = $this->obtenerDonantes($userId, $userRole);

        // Renderizar vista
        $this->render('map.map', [
            'userName' => $userName,
            'userRole' => $userRole,
            'userLat' => $ubicacionUsuario['lat'],
            'userLng' => $ubicacionUsuario['lng'],
            'userDir' => $ubicacionUsuario['direccion'],
            'donantes' => $donantes
        ]);
    }

    private function obtenerUbicacionUsuario(int $userId): array
    {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            $sql = "SELECT Latitud, Longitud, nom_calle, num_calle 
                    FROM direcciones 
                    WHERE id_usuario_direcc = :id";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['Latitud'] && $result['Longitud']) {
                return [
                    'lat' => (float)$result['Latitud'],
                    'lng' => (float)$result['Longitud'],
                    'direccion' => trim(($result['nom_calle'] ?? '') . ' ' . ($result['num_calle'] ?? ''))
                ];
            }

            return [
                'lat' => -27.4692,
                'lng' => -58.8306,
                'direccion' => 'Ubicación no configurada'
            ];

        } catch (PDOException $e) {
            error_log("Error obtener ubicación: " . $e->getMessage());
            return [
                'lat' => -27.4692,
                'lng' => -58.8306,
                'direccion' => 'Error al cargar ubicación'
            ];
        }
    }

    private function obtenerDonantes(int $userId, string $userRole): array
    {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            if ($userRole === 'receptor') {
                $sql = "SELECT 
                            u.id_usuario,
                            d.id_usu_donante as id,
                            d.nom_comercial as nombre,
                            dir.Latitud as lat,
                            dir.Longitud as lng,
                            CONCAT(dir.nom_calle, ' ', dir.num_calle) as direccion,
                            COUNT(DISTINCT CASE 
                                WHEN pd.cantidad_disponible > 0 AND pd.estado = 'Activo' 
                                THEN pd.id_producto_donante 
                            END) as productos,
                            COUNT(DISTINCT CASE 
                                WHEN s.estado = 'Pendiente' AND s.id_receptor = :userId 
                                THEN s.id_solicitud 
                            END) as solicitudes_pendientes
                        FROM usuarios u
                        INNER JOIN donante d ON u.id_usuario = d.id_usu_donante
                        INNER JOIN direcciones dir ON u.id_usuario = dir.id_usuario_direcc
                        LEFT JOIN productos_donante pd ON d.id_usu_donante = pd.id_donante
                        LEFT JOIN solicitud s ON d.id_usu_donante = s.id_donante
                        WHERE u.rol = 'donante' 
                        AND dir.Latitud IS NOT NULL 
                        AND dir.Longitud IS NOT NULL
                        GROUP BY u.id_usuario, d.id_usu_donante, d.nom_comercial, 
                                 dir.Latitud, dir.Longitud, dir.nom_calle, dir.num_calle
                        HAVING productos > 0";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([':userId' => $userId]);
            } else {
                $sql = "SELECT 
                            u.id_usuario,
                            d.id_usu_donante as id,
                            d.nom_comercial as nombre,
                            dir.Latitud as lat,
                            dir.Longitud as lng,
                            CONCAT(dir.nom_calle, ' ', dir.num_calle) as direccion,
                            COUNT(DISTINCT CASE 
                                WHEN pd.cantidad_disponible > 0 AND pd.estado = 'Activo' 
                                THEN pd.id_producto_donante 
                            END) as productos,
                            0 as solicitudes_pendientes
                        FROM usuarios u
                        INNER JOIN donante d ON u.id_usuario = d.id_usu_donante
                        INNER JOIN direcciones dir ON u.id_usuario = dir.id_usuario_direcc
                        LEFT JOIN productos_donante pd ON d.id_usu_donante = pd.id_donante
                        WHERE u.rol = 'donante' 
                        AND u.id_usuario != :userId
                        AND dir.Latitud IS NOT NULL 
                        AND dir.Longitud IS NOT NULL
                        GROUP BY u.id_usuario, d.id_usu_donante, d.nom_comercial,
                                 dir.Latitud, dir.Longitud, dir.nom_calle, dir.num_calle
                        HAVING productos > 0";

                $stmt = $conn->prepare($sql);
                $stmt->execute([':userId' => $userId]);
            }

            $resultado = [];
            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $resultado[] = [
                    'id' => (int)$fila['id'],
                    'nombre' => $fila['nombre'] ?? 'Donante',
                    'lat' => (float)$fila['lat'],
                    'lng' => (float)$fila['lng'],
                    'direccion' => $fila['direccion'] ?? 'Sin dirección',
                    'productos' => (int)$fila['productos'],
                    'solicitudes_pendientes' => (int)$fila['solicitudes_pendientes'],
                    'estado' => 'online'
                ];
            }

            return $resultado;

        } catch (PDOException $e) {
            error_log("Error obtener donantes: " . $e->getMessage());
            return [];
        }
    }
}