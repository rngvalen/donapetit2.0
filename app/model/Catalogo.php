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
}