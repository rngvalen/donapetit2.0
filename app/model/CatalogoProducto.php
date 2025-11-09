<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

/**
 * Modelo para acceder al catálogo base (tabla cargar_productos).
 */
class CatalogoProducto extends Model
{
    protected string $table = 'cargar_productos';
    protected string $pk = 'id_carga_producto';

    /**
     * Obtiene los productos del catálogo con su unidad y categoría asociada.
     *
     * @return array<int,array<string,mixed>>
     */
    public function activos(): array
    {
        self::initDb();

        $sql = "
            SELECT
                cp.id_carga_producto,
                cp.nom_producto,
                cp.id_unidades,
                cp.id_categorias,
                un.abreviatura AS unidad_abreviatura,
                un.nombre_unidad AS unidad_nombre,
                cat.nombre AS categoria_nombre
            FROM cargar_productos cp
            LEFT JOIN unidades un ON un.id_unidad = cp.id_unidades
            LEFT JOIN categorias cat ON cat.id_categoria = cp.id_categorias
            WHERE cp.estado = 1
            ORDER BY cp.nom_producto ASC
        ";

        $stmt = self::$db->query($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return array_map(
            static function (array $row): array {
                return [
                    'id' => (int)($row['id_carga_producto'] ?? 0),
                    'nombre' => (string)($row['nom_producto'] ?? ''),
                    'unidad_abreviatura' => (string)($row['unidad_abreviatura'] ?? ''),
                    'unidad_nombre' => (string)($row['unidad_nombre'] ?? ''),
                    'categoria_nombre' => (string)($row['categoria_nombre'] ?? ''),
                ];
            },
            $rows
        );
    }

    /**
     * Busca un item del catálogo por nombre (case insensitive).
     *
     * @return array<string,mixed>|null
     */
    public function buscarPorNombre(string $nombre): ?array
    {
        self::initDb();

        $sql = "
            SELECT
                cp.id_carga_producto,
                cp.nom_producto,
                cp.id_unidades,
                cp.id_categorias,
                un.abreviatura AS unidad_abreviatura,
                un.nombre_unidad AS unidad_nombre,
                cat.nombre AS categoria_nombre
            FROM cargar_productos cp
            LEFT JOIN unidades un ON un.id_unidad = cp.id_unidades
            LEFT JOIN categorias cat ON cat.id_categoria = cp.id_categorias
            WHERE cp.estado = 1
              AND LOWER(cp.nom_producto) = LOWER(:nombre)
            LIMIT 1
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([':nombre' => $nombre]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return [
            'id' => (int)$row['id_carga_producto'],
            'nombre' => (string)$row['nom_producto'],
            'unidad_abreviatura' => (string)($row['unidad_abreviatura'] ?? ''),
            'unidad_nombre' => (string)($row['unidad_nombre'] ?? ''),
            'categoria_nombre' => (string)($row['categoria_nombre'] ?? ''),
        ];
    }
}
