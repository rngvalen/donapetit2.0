<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

class Producto extends Model
{
    protected string $table = 'cargar_productos';
    protected string $pk = 'id_carga_producto';

    /**
     * Obtiene la lista de nombres disponibles en el catálogo
     */
    public function obtenerNombresDisponibles(): array
    {
        self::initDb();
        
        $sql = "SELECT DISTINCT nom_producto FROM cargar_productos ORDER BY nom_producto ASC";
        $stmt = self::$db->query($sql);
        $resultados = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        return array_values($resultados);
    }

    /**
     * Busca un producto en el catálogo por su nombre
     */
    public function obtenerIdPorNombre(string $nombre): ?int
    {
        self::initDb();
        
        $sql = "SELECT id_carga_producto FROM cargar_productos WHERE nom_producto = :nombre LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':nombre' => $nombre]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result ? (int)$result['id_carga_producto'] : null;
    }

   /**
 * Crea un nuevo producto en el catálogo base
 */
public function crearProductoCatalogo(
    string $nombre,
    int $idUnidad,
    int $idCategoria,
    int $idDonante,
    string $tipoOrigen = 'donante',
    int $estado = 1
): int {
    self::initDb();
    
    $now = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');
    
    try {
        $sql = "INSERT INTO cargar_productos 
                (nom_producto, id_unidades, id_categorias, id_donante, tipo_origen, estado, create_at) 
                VALUES (:nom_producto, :id_unidades, :id_categorias, :id_donante, :tipo_origen, :estado, :create_at)";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':nom_producto' => $nombre,
            ':id_unidades' => $idUnidad,
            ':id_categorias' => $idCategoria,
            ':id_donante' => $idDonante,
            ':tipo_origen' => $tipoOrigen,
            ':estado' => $estado,
            ':create_at' => $now
        ]);
        
        return (int) self::$db->lastInsertId();
        
    } catch (\PDOException $e) {
        error_log("Error al crear producto en catálogo: " . $e->getMessage());
        throw new \Exception("Error al crear producto: " . $e->getMessage());
    }
}

/**
 * Registra stock de un producto para un donante específico
 */
public function registrarStock(
    int $idProductoCatalogo,
    int $idDonante,
    int $cantidad,
    ?string $fechaVencimiento = null
): int {
    self::initDb();
    
    $now = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');
    
    // PASO 1: Crear registro en tabla productos (intermedia)
    $sqlProducto = "INSERT INTO productos (id_carga_producto, create_at, update_at, comentario) 
                    VALUES (:id_carga_producto, :create_at, :update_at, :comentario)";
    
    $stmtProducto = self::$db->prepare($sqlProducto);
    $stmtProducto->execute([
        ':id_carga_producto' => $idProductoCatalogo,
        ':create_at' => $now,
        ':update_at' => $now,
        ':comentario' => ''  // ✅ Cambiado de NULL a cadena vacía
    ]);
    
    $idProducto = (int) self::$db->lastInsertId();
    
    // PASO 2: Crear registro en stock_productos
    $sqlStock = "INSERT INTO stock_productos 
                (id_donante, cantidad, id_producto, fecha_venc, create_at, update_at) 
                VALUES (:id_donante, :cantidad, :id_producto, :fecha_venc, :create_at, :update_at)";
    
    $stmtStock = self::$db->prepare($sqlStock);
    $stmtStock->execute([
        ':id_donante' => $idDonante,
        ':cantidad' => $cantidad,
        ':id_producto' => $idProducto,
        ':fecha_venc' => $fechaVencimiento,
        ':create_at' => $now,
        ':update_at' => $now
    ]);
    
    return (int) self::$db->lastInsertId();
}
 /**
 * Obtiene el stock de productos de un donante específico
 */
public function obtenerStockDonante(int $idDonante): array
{
    self::initDb();
    
    $sql = "SELECT 
                sp.id_stock,
                sp.cantidad,
                sp.fecha_venc as fecha_vencimiento,
                cp.nom_producto,
                cp.id_carga_producto,
                u.nombre_unidad,
                u.abreviatura,
                c.nombre as categoria
            FROM stock_productos sp
            INNER JOIN productos p ON sp.id_producto = p.id_productos
            INNER JOIN cargar_productos cp ON p.id_carga_producto = cp.id_carga_producto
            LEFT JOIN unidades u ON cp.id_unidades = u.id_unidad
            LEFT JOIN categorias c ON cp.id_categorias = c.id_categoria
            WHERE sp.id_donante = :id_donante
            AND sp.cantidad > 0
            ORDER BY sp.fecha_venc ASC";
    
    $stmt = self::$db->prepare($sql);
    $stmt->execute([':id_donante' => $idDonante]);
    
    $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    $productos = [];
    foreach ($resultados as $row) {
        $productos[] = [
            'id_stock' => (int)$row['id_stock'],
            'id_producto' => (int)$row['id_carga_producto'],
            'nom_producto' => $row['nom_producto'] ?? 'Producto sin nombre',
            'cantidad' => (int)$row['cantidad'],
            'fecha_vencimiento' => $row['fecha_vencimiento'],
            'unidad' => $row['abreviatura'] ?? $row['nombre_unidad'] ?? '',
            'categoria' => $row['categoria'] ?? 'Sin categoría',
            'comentarios' => '',
        ];
    }
    
    return $productos;
}

    /**
     * Obtiene todos los productos del catálogo
     */
    public function obtenerCatalogo(int $limit = 100, int $offset = 0): array
    {
        self::initDb();
        
        $sql = "SELECT 
                    cp.id_carga_producto,
                    cp.nom_producto,
                    cp.estado,
                    cp.create_at,
                    u.nombre_unidad,
                    u.abreviatura,
                    c.nombre as categoria
                FROM cargar_productos cp
                LEFT JOIN unidades u ON cp.id_unidades = u.id_unidad
                LEFT JOIN categorias c ON cp.id_categorias = c.id_categoria
                ORDER BY cp.create_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function encontrarPorId($id): ?array
    {
        self::initDb();
        
        $sql = "SELECT 
                    cp.*,
                    u.nombre_unidad,
                    u.abreviatura,
                    c.nombre as categoria
                FROM cargar_productos cp
                LEFT JOIN unidades u ON cp.id_unidades = u.id_unidad
                LEFT JOIN categorias c ON cp.id_categorias = c.id_categoria
                WHERE cp.id_carga_producto = :id";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function eliminarPorId($id): bool
    {
        self::initDb();
        
        $sql = "DELETE FROM stock_productos WHERE id_stock = :id";
        $stmt = self::$db->prepare($sql);
        
        return $stmt->execute([':id' => $id]);
    }

    public function actualizarStock(
        int $idStock,
        int $cantidad,
        ?string $fechaVencimiento = null
    ): bool {
        self::initDb();
        
        $now = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');
        
        $sql = "UPDATE stock_productos 
                SET cantidad = :cantidad, 
                    fecha_venc = :fecha_venc,
                    update_at = :update_at
                WHERE id_stock = :id_stock";
        
        $stmt = self::$db->prepare($sql);
        
        return $stmt->execute([
            ':cantidad' => $cantidad,
            ':fecha_venc' => $fechaVencimiento,
            ':update_at' => $now,
            ':id_stock' => $idStock
        ]);
    }
}