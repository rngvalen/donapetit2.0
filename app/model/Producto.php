<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

/**
 * Modelo Producto alineado con el esquema actual de la base de datos.
 */
class Producto extends Model
{
    protected string $table = 'productos';
    protected string $pk = 'id_productos';

    private const JSON_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    /** @var array<string,int|null> */
    private static array $unidadCache = [];

    /** @var array<int,string> */
    private static array $categoriaCache = [];

    /**
     * Decodifica el contenido de la columna comentario.
     *
     * @param string|null $comentario
     * @return array<string,mixed>
     */
    private function decodeComentario(?string $comentario): array
    {
        if ($comentario === null) {
            return [];
        }

        $comentario = trim($comentario);
        if ($comentario === '') {
            return [];
        }

        $decoded = json_decode($comentario, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return ['nom_producto' => $comentario];
    }

    /**
     * Serializa el payload que se guarda en comentario.
     *
     * @param array<string,mixed> $data
     */
    private function encodeComentario(array $data): string
    {
        $defaults = [
            'nom_producto' => '',
            'unidad' => '',
            'cantidad' => null,
            'fecha_vencimiento' => null,
            'comentarios' => null,
            'estado' => 'DISPONIBLE',
            'categoria' => null,
            'categoria_id' => null,
        ];

        $payload = array_merge($defaults, array_intersect_key($data, $defaults));
        $json = json_encode($payload, self::JSON_OPTIONS);

        return $json !== false ? $json : json_encode($defaults, self::JSON_OPTIONS);
    }

    /**
     * Resuelve el ID de la unidad a partir de su abreviatura o nombre.
     */
    private function resolveUnidadId(?string $unidad): ?int
    {
        if ($unidad === null) {
            return null;
        }

        $unidad = trim($unidad);
        if ($unidad === '') {
            return null;
        }

        $cacheKey = strtolower($unidad);
        if (array_key_exists($cacheKey, self::$unidadCache)) {
            return self::$unidadCache[$cacheKey];
        }

        self::initDb();

        $sql = '
            SELECT id_unidad
            FROM unidades
            WHERE LOWER(abreviatura) = LOWER(:value)
               OR LOWER(nombre_unidad) = LOWER(:value)
            LIMIT 1
        ';

        $stmt = self::$db->prepare($sql);
        $stmt->execute([':value' => $unidad]);
        $id = $stmt->fetchColumn();

        $result = $id !== false ? (int) $id : null;
        self::$unidadCache[$cacheKey] = $result;

        return $result;
    }

    /**
     * Obtiene el nombre de una categoría a partir de su ID.
     */
    private function resolveCategoriaNombre(?int $categoriaId): ?string
    {
        if ($categoriaId === null) {
            return null;
        }

        if (array_key_exists($categoriaId, self::$categoriaCache)) {
            return self::$categoriaCache[$categoriaId];
        }

        self::initDb();

        $stmt = self::$db->prepare('SELECT nombre FROM categorias WHERE id_categoria = :id LIMIT 1');
        $stmt->execute([':id' => $categoriaId]);
        $nombre = $stmt->fetchColumn();

        $nombreVal = $nombre !== false ? (string) $nombre : null;
        self::$categoriaCache[$categoriaId] = $nombreVal;

        return $nombreVal;
    }

    /**
     * Query base con joins necesarios para las agregaciones.
     */
    private function baseSelect(): string
    {
        return '
            SELECT
                p.id_productos,
                p.comentario,
                p.create_at,
                p.update_at,
                p.id_unidad,
                p.id_categoria,
                u.abreviatura AS unidad_abreviatura,
                u.nombre_unidad AS unidad_nombre,
                c.nombre AS categoria_nombre,
                COALESCE(SUM(sp.cantidad), 0) AS stock_total,
                MAX(sp.update_at) AS stock_updated_at
            FROM productos p
            LEFT JOIN unidades u ON u.id_unidad = p.id_unidad
            LEFT JOIN categorias c ON c.id_categoria = p.id_categoria
            LEFT JOIN stock_productos sp ON sp.id_producto = p.id_productos
        ';
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function normalizeRow(array $row): array
    {
        $payload = $this->decodeComentario($row['comentario'] ?? null);

        $nombre = trim((string) ($payload['nom_producto'] ?? ''));
        if ($nombre === '') {
            $backup = trim((string) ($row['comentario'] ?? ''));
            $nombre = $backup !== '' ? $backup : 'Producto sin nombre';
        }

        $unidadPayload = trim((string) ($payload['unidad'] ?? ''));
        $unidad = $unidadPayload !== '' ? $unidadPayload : (string) ($row['unidad_abreviatura'] ?? '');
        if ($unidad === '') {
            $unidad = (string) ($row['unidad_nombre'] ?? '');
        }

        $categoria = $payload['categoria'] ?? $row['categoria_nombre'] ?? null;
        if ($categoria !== null) {
            $categoria = (string) $categoria;
            if ($categoria === '') {
                $categoria = null;
            }
        }

        $cantidad = null;
        if (isset($payload['cantidad']) && $payload['cantidad'] !== '' && $payload['cantidad'] !== null) {
            $cantidad = (int) $payload['cantidad'];
        }
        $stockTotal = isset($row['stock_total']) ? (int) $row['stock_total'] : null;
        if ($stockTotal !== null && $stockTotal > 0) {
            $cantidad = $stockTotal;
        }

        $estadoPayload = strtoupper(trim((string) ($payload['estado'] ?? '')));
        if ($estadoPayload === '') {
            $estadoPayload = $cantidad !== null && $cantidad > 0 ? 'DISPONIBLE' : 'SIN_ESTADO';
        }

        $comentarios = $payload['comentarios'] ?? null;
        if (is_string($comentarios)) {
            $comentarios = trim($comentarios);
            if ($comentarios === '') {
                $comentarios = null;
            }
        } else {
            $comentarios = null;
        }

        $fechaVencimiento = $payload['fecha_vencimiento'] ?? null;
        if (is_string($fechaVencimiento)) {
            $fechaVencimiento = trim($fechaVencimiento) !== '' ? trim($fechaVencimiento) : null;
        } else {
            $fechaVencimiento = null;
        }

        $categoriaId = $payload['categoria_id'] ?? $row['id_categoria'] ?? null;
        if ($categoriaId !== null) {
            $categoriaId = (int) $categoriaId;
        }

        return [
            'id_producto' => (int) ($row['id_productos'] ?? 0),
            'id_productos' => (int) ($row['id_productos'] ?? 0),
            'nom_producto' => $nombre,
            'unidad' => $unidad,
            'cantidad' => $cantidad,
            'fecha_vencimiento' => $fechaVencimiento,
            'comentarios' => $comentarios,
            'estado' => $estadoPayload,
            'categoria' => $categoria,
            'categoria_id' => $categoriaId,
            'unidad_id' => isset($row['id_unidad']) ? (int) $row['id_unidad'] : null,
            'created_at' => $row['create_at'] ?? null,
            'updated_at' => $row['update_at'] ?? null,
            'stock_total' => $stockTotal,
            'stock_updated_at' => $row['stock_updated_at'] ?? null,
        ];
    }

    /**
     * Consulta un producto con joins y agregaciones.
     *
     * @param mixed $id
     * @return array<string,mixed>|null
     */
    private function fetchAggregated($id): ?array
    {
        self::initDb();

        $sql = $this->baseSelect() . '
            WHERE p.id_productos = :id
            GROUP BY
                p.id_productos,
                p.comentario,
                p.create_at,
                p.update_at,
                p.id_unidad,
                p.id_categoria,
                u.abreviatura,
                u.nombre_unidad,
                c.nombre
        ';

        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    public static function all(int $limit = 100, int $offset = 0): array
    {
        self::initDb();

        $instance = new static();
        $sql = $instance->baseSelect() . '
            GROUP BY
                p.id_productos,
                p.comentario,
                p.create_at,
                p.update_at,
                p.id_unidad,
                p.id_categoria,
                u.abreviatura,
                u.nombre_unidad,
                c.nombre
            ORDER BY p.update_at DESC
            LIMIT :limit OFFSET :offset
        ';

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return array_map(static fn(array $row): array => $instance->normalizeRow($row), $rows);
    }

    /**
     * @return array<int,string>
     */
    public function obtenerNombresDisponibles(): array
    {
        $rows = self::all(1000, 0);
        $nombres = [];

        foreach ($rows as $row) {
            $nombre = trim((string) ($row['nom_producto'] ?? ''));
            if ($nombre === '') {
                continue;
            }
            $nombres[strtolower($nombre)] = $nombre;
        }

        $lista = array_values($nombres);
        sort($lista, SORT_NATURAL | SORT_FLAG_CASE);

        return $lista;
    }

    /**
     * Crea un nuevo registro en la tabla productos.
     */
    public function crear(
        string $nombre,
        string $unidad,
        ?int $cantidad = null,
        ?string $fechaVencimiento = null,
        ?string $comentarios = null,
        ?string $estado = null,
        ?int $categoriaId = null,
        ?string $categoriaNombre = null
    ): string {
        self::initDb();

        $unidadId = $this->resolveUnidadId($unidad);
        if ($unidadId === null) {
            throw new \InvalidArgumentException('Unidad no encontrada en el catalogo.');
        }

        if ($categoriaId === null) {
            throw new \InvalidArgumentException('La categoria es obligatoria.');
        }

        if ($categoriaNombre === null) {
            $categoriaNombre = $this->resolveCategoriaNombre($categoriaId);
        }

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $comentario = $this->encodeComentario([
            'nom_producto' => $nombre,
            'unidad' => $unidad,
            'cantidad' => $cantidad,
            'fecha_vencimiento' => $fechaVencimiento,
            'comentarios' => $comentarios,
            'estado' => $estado ?? 'DISPONIBLE',
            'categoria' => $categoriaNombre,
            'categoria_id' => $categoriaId,
        ]);

        return $this->insert([
            'comentario' => $comentario,
            'create_at' => $now,
            'update_at' => $now,
            'id_unidad' => $unidadId,
            'id_categoria' => $categoriaId,
        ]);
    }

    /**
     * Actualiza un producto existente.
     */
    public function actualizarProducto(
        $id,
        string $nombre,
        string $unidad,
        ?int $cantidad = null,
        ?string $fechaVencimiento = null,
        ?string $comentarios = null,
        ?string $estado = null,
        ?int $categoriaId = null,
        ?string $categoriaNombre = null
    ): bool {
        self::initDb();

        $row = $this->fetchAggregated($id);
        if ($row === null) {
            return false;
        }

        $payload = $this->decodeComentario($row['comentario'] ?? null);

        $payload['nom_producto'] = $nombre;
        $payload['unidad'] = $unidad;
        $payload['cantidad'] = $cantidad;
        $payload['fecha_vencimiento'] = $fechaVencimiento;
        $payload['comentarios'] = $comentarios;
        if ($estado !== null) {
            $payload['estado'] = $estado;
        }

        if ($categoriaId !== null) {
            $payload['categoria_id'] = $categoriaId;
            $payload['categoria'] = $categoriaNombre ?? $this->resolveCategoriaNombre($categoriaId);
        }

        $unidadId = $this->resolveUnidadId($unidad) ?? (isset($row['id_unidad']) ? (int) $row['id_unidad'] : null);
        if ($unidadId === null) {
            throw new \InvalidArgumentException('Unidad no encontrada en el catalogo.');
        }

        $categoriaDestino = $categoriaId ?? (isset($row['id_categoria']) ? (int) $row['id_categoria'] : null);
        if ($categoriaDestino === null) {
            throw new \InvalidArgumentException('La categoria es obligatoria.');
        }

        $comentario = $this->encodeComentario($payload);
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        return $this->update($id, [
            'comentario' => $comentario,
            'update_at' => $now,
            'id_unidad' => $unidadId,
            'id_categoria' => $categoriaDestino,
        ]);
    }

    /**
     * Busca productos por nombre (coincidencia parcial, case-insensitive).
     *
     * @return array<int,array<string,mixed>>
     */
    public function encontrarPorNombre(string $nombre): array
    {
        $nombre = trim($nombre);
        if ($nombre === '') {
            return self::all(1000, 0);
        }

        $needle = function_exists('mb_strtolower') ? mb_strtolower($nombre, 'UTF-8') : strtolower($nombre);
        $items = self::all(1000, 0);

        return array_values(array_filter($items, static function (array $item) use ($needle): bool {
            $value = (string) ($item['nom_producto'] ?? '');
            $haystack = function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
            return strpos($haystack, $needle) !== false;
        }));
    }

    /**
     * Obtiene un producto normalizado por ID.
     *
     * @param mixed $id
     * @return array<string,mixed>|null
     */
    public function encontrarPorId($id)
    {
        $row = $this->fetchAggregated($id);
        return $row ? $this->normalizeRow($row) : null;
    }

    /**
     * Elimina un producto, limpiando dependencias de stock.
     */
    public function eliminarPorId($id): bool
    {
        self::initDb();

        $stmt = self::$db->prepare('DELETE FROM stock_productos WHERE id_producto = :id');
        $stmt->execute([':id' => $id]);

        return $this->delete($id);
    }
}
