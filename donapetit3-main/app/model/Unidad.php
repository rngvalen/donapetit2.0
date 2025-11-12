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
        $sql = 'SELECT id_unidad, abreviatura, nombre_unidad FROM unidades WHERE estado = 1 ORDER BY nombre_unidad ASC';
        $stmt = self::$db->query($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (!is_array($rows)) return [];
        return array_values(array_map(static function ($r): array {
            return [
                'id' => isset($r['id_unidad']) ? (int)$r['id_unidad'] : null,
                'abreviatura' => (string)($r['abreviatura'] ?? ''),
                'nombre_unidad' => (string)($r['nombre_unidad'] ?? ''),
            ];
        }, $rows));
    }

    /**
     * Busca una unidad por su abreviatura.
     *
     * @return array<string,mixed>|null
     */
    public function buscarPorAbreviatura(string $abreviatura): ?array
    {
        self::initDb();
        $stmt = self::$db->prepare(
            'SELECT id_unidad, abreviatura, nombre_unidad
             FROM unidades
             WHERE estado = 1 AND abreviatura = :abreviatura
             LIMIT 1'
        );
        $stmt->execute([':abreviatura' => $abreviatura]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return [
            'id' => (int)$row['id_unidad'],
            'abreviatura' => (string)$row['abreviatura'],
            'nombre_unidad' => (string)$row['nombre_unidad'],
        ];
    }
}

