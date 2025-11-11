<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

class ProductoDonante extends Model
{
    protected string $table = 'productos_donante';
    protected string $pk = 'id_producto_donante';

    /**
     * Registra un producto en el inventario del donante
     */
    public function registrarProducto(
        int $idDonante,
        int $idCatalogo,
        int $cantidad,
        ?string $fechaVencimiento = null
    ): int {
        self::initDb();
        
        $sql = "INSERT INTO productos_donante 
                (id_donante, id_catalogo, cantidad_disponible, fecha_vencimiento, fecha_registro, estado) 
                VALUES (:id_donante, :id_catalogo, :cantidad, :fecha_venc, NOW(), 'Activo')";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':id_donante' => $idDonante,
            ':id_catalogo' => $idCatalogo,
            ':cantidad' => $cantidad,
            ':fecha_venc' => $fechaVencimiento
        ]);
        
        return (int) self::$db->lastInsertId();
    }

    /**
     * Obtiene el inventario completo de un donante
     */
    public function obtenerInventarioDonante(int $idDonante): array
    {
        self::initDb();
        
        $sql = "SELECT 
                    pd.id_producto_donante,
                    pd.cantidad_disponible,
                    pd.fecha_vencimiento,
                    pd.fecha_registro,
                    pd.estado,
                    c.nom_producto,
                    c.descripcion,
                    cat.nombre AS categoria,
                    u.nombre_unidad,
                    u.abreviatura
                FROM productos_donante pd
                INNER JOIN catalogo_productos c ON pd.id_catalogo = c.id_catalogo
                INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
                INNER JOIN unidades u ON c.id_unidad = u.id_unidad
                WHERE pd.id_donante = :id_donante
                AND pd.estado = 'Activo'
                ORDER BY pd.fecha_vencimiento ASC";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id_donante' => $idDonante]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los productos disponibles de todos los donantes
     */
    public function obtenerTodosDisponibles(): array
    {
        self::initDb();
        
        // Usar la vista SQL existente
        $sql = "SELECT 
                    nombre_donante,
                    nom_producto,
                    categoria,
                    cantidad_disponible,
                    unidad
                FROM vista_productos_disponibles 
                ORDER BY categoria, nom_producto";
        
        $stmt = self::$db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza la cantidad de un producto
     */
    public function actualizarCantidad(int $idProducto, int $cantidad): bool
    {
        self::initDb();
        
        $sql = "UPDATE productos_donante 
                SET cantidad_disponible = :cantidad 
                WHERE id_producto_donante = :id";
        
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            ':cantidad' => $cantidad,
            ':id' => $idProducto
        ]);
    }

    /**
     * Elimina un producto del inventario (marca como Inactivo)
     */
    public function eliminarProducto(int $idProducto): bool
    {
        self::initDb();
        
        $sql = "UPDATE productos_donante 
                SET estado = 'Inactivo' 
                WHERE id_producto_donante = :id";
        
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([':id' => $idProducto]);
    }
}