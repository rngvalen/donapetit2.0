<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

/**
 * Modelo Producto para administrar la tabla productos.
 */
class Producto extends Model
{
    protected string $table = 'productos';
    protected string $pk = 'id_productos';

    /**
     * Genera un payload serializado para guardar en la columna comentario.
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

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $json !== false ? $json : json_encode($defaults, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Decodifica la columna comentario y normaliza el arreglo resultante.
     *
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function normalizeRow(array $row): array
    {
        $decoded = [];
        if (isset($row['comentario']) && is_string($row['comentario'])) {
            $decoded = json_decode($row['comentario'], true) ?: [];
        }

        if (!is_array($decoded)) {
            $decoded = [];
        }

        $defaults = [
            'nom_producto' => '',
            'unidad' => '',
            'cantidad' => null,
            'fecha_vencimiento' => null,
            'comentarios' => null,
            'estado' => 'DISPONIBLE',
            'categoria_id' => null,
            'categoria' => null,
        ];

        $data = array_merge($defaults, $decoded);

        if ($data['cantidad'] !== null) {
            $data['cantidad'] = (int)$data['cantidad'];
        }

        if ($data['fecha_vencimiento'] === '') {
            $data['fecha_vencimiento'] = null;
        }

        if ($data['comentarios'] === '') {
            $data['comentarios'] = null;
        }

        if ($data['categoria_id'] !== null) {
            $data['categoria_id'] = (int)$data['categoria_id'];
        }
        if ($data['categoria'] === '') {
            $data['categoria'] = null;
        }

        $data['id_producto'] = isset($row['id_productos']) ? (int)$row['id_productos'] : null;
        $data['id_productos'] = $data['id_producto'];
        $data['created_at'] = $row['create_at'] ?? null;
        $data['updated_at'] = $row['update_at'] ?? null;

        return $data;
    }

    /**
     * Obtiene la lista de nombres disponibles a partir de los productos existentes.
     *
     * @return array<int,string>
     */
    public function obtenerNombresDisponibles(): array
    {
        $todos = self::all(1000, 0);
        $nombres = [];
        foreach ($todos as $row) {
            $nombre = trim((string)($row['nom_producto'] ?? ''));
            if ($nombre === '') {
                continue;
            }
            $nombres[$nombre] = true;
        }
        $lista = array_keys($nombres);
        sort($lista, SORT_NATURAL | SORT_FLAG_CASE);
        return $lista;
    }

    /**
     * Crea un nuevo producto almacenando los datos en formato JSON dentro de comentario.
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

        $now = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');
        $comment = $this->encodeComentario([
            'nom_producto' => $nombre,
            'unidad' => $unidad,
            'cantidad' => $cantidad,
            'fecha_vencimiento' => $fechaVencimiento,
            'comentarios' => $comentarios,
            'estado' => $estado ?? 'DISPONIBLE',
            'categoria_id' => $categoriaId,
            'categoria' => $categoriaNombre,
        ]);

        return $this->insert([
            'create_at' => $now,
            'update_at' => $now,
            'comentario' => $comment,
        ]);
    }

    /**
     * Actualiza un producto existente serializando nuevamente sus datos.
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

        $existing = parent::find($id);
        if (!$existing) {
            return false;
        }

        $decoded = $this->normalizeRow($existing);

        $decoded['nom_producto'] = $nombre;
        $decoded['unidad'] = $unidad;
        $decoded['cantidad'] = $cantidad;
        $decoded['fecha_vencimiento'] = $fechaVencimiento;
        $decoded['comentarios'] = $comentarios;
        if ($estado !== null) {
            $decoded['estado'] = $estado;
        }
        if ($categoriaId !== null) {
            $decoded['categoria_id'] = $categoriaId;
        }
        if ($categoriaNombre !== null) {
            $decoded['categoria'] = $categoriaNombre;
        }

        $now = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        return $this->update($id, [
            'comentario' => $this->encodeComentario($decoded),
            'update_at' => $now,
        ]);
    }

    /**
     * Busca por nombre dentro del JSON almacenado.
     *
     * @return array<int,array<string,mixed>>
     */
    public function encontrarPorNombre(string $nombre): array
    {
        $todos = self::all(1000, 0);
        $needle = trim($nombre);
        if ($needle === '') {
            return $todos;
        }
        $needleLower = function_exists('mb_strtolower') ? mb_strtolower($needle, 'UTF-8') : strtolower($needle);
        $filtered = [];
        foreach ($todos as $row) {
            $value = (string)($row['nom_producto'] ?? '');
            $haystack = function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
            if (strpos($haystack, $needleLower) !== false) {
                $filtered[] = $row;
            }
        }
        return $filtered;
    }

    public function encontrarPorId($id)
    {
        $row = parent::find($id);
        return $row ? $this->normalizeRow($row) : null;
    }

    public function eliminarPorId($id): bool
    {
        return $this->delete($id);
    }

    public static function all(int $limit = 100, int $offset = 0): array
    {
        $rows = parent::all($limit, $offset);
        $instance = new static();
        return array_map(static fn($row) => $instance->normalizeRow($row), $rows);
    }


    /**
 * Registra stock de un producto para un donante específico
 * 
 * @param int $idProducto ID del producto del catálogo
 * @param int $idDonante ID del usuario donante
 * @param int $cantidad Cantidad disponible
 * @param string|null $fechaVencimiento Fecha de vencimiento (Y-m-d)
 * @return int ID del stock creado
 */
public function registrarStock(
    int $idProducto,
    int $idDonante,
    int $cantidad,
    ?string $fechaVencimiento = null
): int {
    self::initDb();
    
    $sql = "INSERT INTO stock_productos_donacion 
            (id_producto, id_donante, stock_productos, fecha_venc) 
            VALUES (:id_producto, :id_donante, :stock_productos, :fecha_venc)";
    
    $stmt = self::$db->prepare($sql);
    $stmt->execute([
        ':id_producto' => $idProducto,
        ':id_donante' => $idDonante,
        ':stock_productos' => $cantidad,
        ':fecha_venc' => $fechaVencimiento
    ]);
    
    return (int) self::$db->lastInsertId();
}

/**
 * Busca el ID de un producto por su nombre en el catálogo
 * 
 * @param string $nombre Nombre del producto
 * @return int|null ID del producto o null si no existe
 */
public function obtenerIdPorNombre(string $nombre): ?int
{
    self::initDb();
    
    $sql = "SELECT id_productos FROM productos WHERE comentario LIKE :nombre LIMIT 1";
    
    $stmt = self::$db->prepare($sql);
    $stmt->execute([':nombre' => '%"nom_producto":"' . $nombre . '"%']);
    
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    return $result ? (int)$result['id_productos'] : null;
}
}
