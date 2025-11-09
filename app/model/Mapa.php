<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

/**
 * Obtiene los datos necesarios para el mapa de donantes.
 */
class Mapa extends Model
{
    /**
     * @return array<int,array<string,mixed>>
     */
    public function obtenerDonantesActivos(): array
    {
        self::initDb();

        $sql = '
            SELECT
                u.id_usuario,
                COALESCE(don.nom_comercial, u.Nombre) AS nombre,
                dir.Latitud,
                dir.Longitud,
                dir.nom_calle,
                dir.num_calle,
                COALESCE(SUM(sp.cantidad), 0) AS total_productos,
                MAX(sp.update_at) AS ultima_actualizacion
            FROM usuarios u
            LEFT JOIN donante don ON don.id_usu_donante = u.id_usuario
            LEFT JOIN direcciones dir ON dir.id_usuario_direcc = u.id_usuario
            LEFT JOIN stock_productos sp ON sp.id_donante = u.id_usuario
            WHERE u.rol = :rol
              AND dir.Latitud IS NOT NULL
              AND dir.Longitud IS NOT NULL
            GROUP BY
                u.id_usuario,
                nombre,
                dir.Latitud,
                dir.Longitud,
                dir.nom_calle,
                dir.num_calle
            ORDER BY
                (MAX(sp.update_at) IS NULL) ASC,
                MAX(sp.update_at) DESC,
                nombre ASC
        ';

        $stmt = self::$db->prepare($sql);
        $stmt->execute([':rol' => 'donante']);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return array_map(static function (array $row): array {
            $direccion = trim((string)($row['nom_calle'] ?? ''));
            $numero = trim((string)($row['num_calle'] ?? ''));
            if ($numero !== '') {
                $direccion = trim($direccion . ' ' . $numero);
            }

            return [
                'id_usuario' => (int)($row['id_usuario'] ?? 0),
                'nombre' => trim((string)($row['nombre'] ?? 'Donante')),
                'latitud' => (float)($row['Latitud'] ?? 0),
                'longitud' => (float)($row['Longitud'] ?? 0),
                'direccion' => $direccion !== '' ? $direccion : 'Direccion sin definir',
                'total_productos' => (int)($row['total_productos'] ?? 0),
                'ultima_actualizacion' => $row['ultima_actualizacion'] ?? null,
            ];
        }, $rows);
    }
}
