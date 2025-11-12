<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

class Catalogo extends Model
{
    protected string $table = 'catalogo_productos';
    protected string $pk = 'id_catalogo';

    /**
     * Obtiene todos los productos activos del catálogo
     */
    public function obtenerProductosActivos(): array
    {
        self::initDb();
        
        $sql = "SELECT 
                    c.id_catalogo,
                    c.nom_producto,
                    c.descripcion,
                    cat.nombre AS categoria,
                    u.nombre_unidad,
                    u.abreviatura
                FROM catalogo_productos c
                INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
                INNER JOIN unidades u ON c.id_unidad = u.id_unidad
                WHERE c.estado = 'Activo'
                ORDER BY cat.nombre, c.nom_producto";
        
        $stmt = self::$db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un producto por ID
     */
    public function obtenerPorId(int $id): ?array
    {
        self::initDb();

        $sql = "SELECT
                    c.*,
                    cat.nombre AS categoria,
                    u.nombre_unidad,
                    u.abreviatura
                FROM catalogo_productos c
                INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
                INNER JOIN unidades u ON c.id_unidad = u.id_unidad
                WHERE c.id_catalogo = :id";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Devuelve los items activos del catalogo junto a su categoria y unidad.
     *
     * @return array<int,array<string,mixed>>
     */
    public function activos(): array
    {
        self::initDb();

        $sql = "
            SELECT
                cp.id_catalogo,
                cp.nom_producto,
                cp.descripcion,
                cp.id_categoria,
                cp.id_unidad,
                un.abreviatura AS unidad_abreviatura,
                un.nombre_unidad AS unidad_nombre,
                cat.nombre AS categoria_nombre
            FROM catalogo_productos cp
            LEFT JOIN unidades un ON un.id_unidad = cp.id_unidad
            LEFT JOIN categorias cat ON cat.id_categoria = cp.id_categoria
            WHERE cp.estado = 'Activo'
            ORDER BY cp.nom_producto ASC
        ";

        $stmt = self::$db->query($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return array_map(
            static function (array $row): array {
                return [
                    'id' => (int)($row['id_catalogo'] ?? 0),
                    'nombre' => (string)($row['nom_producto'] ?? ''),
                    'descripcion' => (string)($row['descripcion'] ?? ''),
                    'categoria_id' => isset($row['id_categoria']) ? (int)$row['id_categoria'] : null,
                    'unidad_id' => isset($row['id_unidad']) ? (int)$row['id_unidad'] : null,
                    'unidad_abreviatura' => (string)($row['unidad_abreviatura'] ?? ''),
                    'unidad_nombre' => (string)($row['unidad_nombre'] ?? ''),
                    'categoria_nombre' => (string)($row['categoria_nombre'] ?? ''),
                ];
            },
            $rows
        );
    }

    /**
     * Busca un item activo por nombre (case insensitive).
     */
    public function buscarPorNombre(string $nombre): ?array
    {
        self::initDb();

        $sql = "
            SELECT
                cp.id_catalogo,
                cp.nom_producto,
                cp.descripcion,
                cp.id_categoria,
                cp.id_unidad,
                un.abreviatura AS unidad_abreviatura,
                un.nombre_unidad AS unidad_nombre,
                cat.nombre AS categoria_nombre
            FROM catalogo_productos cp
            LEFT JOIN unidades un ON un.id_unidad = cp.id_unidad
            LEFT JOIN categorias cat ON cat.id_categoria = cp.id_categoria
            WHERE cp.estado = 'Activo'
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
            'id' => (int)$row['id_catalogo'],
            'nombre' => (string)$row['nom_producto'],
            'descripcion' => (string)($row['descripcion'] ?? ''),
            'categoria_id' => isset($row['id_categoria']) ? (int)$row['id_categoria'] : null,
            'unidad_id' => isset($row['id_unidad']) ? (int)$row['id_unidad'] : null,
            'unidad_abreviatura' => (string)($row['unidad_abreviatura'] ?? ''),
            'unidad_nombre' => (string)($row['unidad_nombre'] ?? ''),
            'categoria_nombre' => (string)($row['categoria_nombre'] ?? ''),
        ];
    }

    /**
     * Registra un nuevo producto en el catalogo principal.
     */
    public function crear(string $nombre, int $categoriaId, int $unidadId, ?string $descripcion = null): int
    {
        self::initDb();

        $stmt = self::$db->prepare(
            'INSERT INTO catalogo_productos (nom_producto, descripcion, id_categoria, id_unidad, estado)
             VALUES (:nombre, :descripcion, :categoria, :unidad, :estado)'
        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':categoria' => $categoriaId,
            ':unidad' => $unidadId,
            ':estado' => 'Activo',
        ]);

        return (int)self::$db->lastInsertId();
    }
}