<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

/**
 * Modelo Unidad: consulta unidades activas desde la tabla `unidades`.
 */
class Unidad extends Model
{
    protected string $table = 'unidades';
    protected string $pk = 'id_unidad';

    /**
     * Obtiene las unidades activas.
     *
     * @return array<int,array{abreviatura:string,nombre_unidad:string}>
     */
    public function activas(): array
    {
        self::initDb();
        $sql = 'SELECT abreviatura, nombre_unidad FROM unidades WHERE estado = 1 ORDER BY nombre_unidad ASC';
        $stmt = self::$db->query($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (!is_array($rows)) return [];
        return array_values(array_map(static function ($r): array {
            return [
                'abreviatura' => (string)($r['abreviatura'] ?? ''),
                'nombre_unidad' => (string)($r['nombre_unidad'] ?? ''),
            ];
        }, $rows));
    }
}

