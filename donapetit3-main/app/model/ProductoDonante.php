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
     * NOTA: Muestra cantidad_disponible y cantidad_reservada por separado
     */
    public function obtenerInventarioDonante(int $idDonante): array
    {
        self::initDb();

        $sql = "SELECT
                    pd.id_producto_donante,
                    pd.cantidad_disponible,
                    pd.cantidad_reservada,
                    (pd.cantidad_disponible - pd.cantidad_reservada) as cantidad_real_disponible,
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
   /**
 * Obtiene todos los productos disponibles de todos los donantes (con ID)
 * NOTA: Solo muestra productos con stock real disponible (descontando reservas)
 */
public function obtenerTodosDisponibles(): array
{
    self::initDb();

    $sql = "SELECT
                pd.id_donante,
                d.nom_comercial as nombre_donante,
                c.nom_producto,
                cat.nombre as categoria,
                (pd.cantidad_disponible - pd.cantidad_reservada) as cantidad_disponible,
                u.nombre_unidad as unidad
            FROM productos_donante pd
            INNER JOIN donante d ON pd.id_donante = d.id_usu_donante
            INNER JOIN catalogo_productos c ON pd.id_catalogo = c.id_catalogo
            INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
            INNER JOIN unidades u ON c.id_unidad = u.id_unidad
            WHERE (pd.cantidad_disponible - pd.cantidad_reservada) > 0
            AND pd.estado = 'Activo'
            ORDER BY d.nom_comercial, cat.nombre, c.nom_producto";

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

    /**
     * Obtiene todos los productos de todos los donantes para el panel admin
     *
     * @param int $limit Limite de registros
     * @param int $offset Offset para paginacion
     * @return array<int,array<string,mixed>> Lista de productos
     */
    public function obtenerTodosParaAdmin(int $limit = 500, int $offset = 0): array
    {
        self::initDb();

        $sql = "SELECT
                    pd.id_producto_donante as id_producto,
                    c.nom_producto,
                    cat.nombre as categoria,
                    u.abreviatura as unidad,
                    pd.cantidad_disponible as cantidad,
                    pd.estado,
                    pd.fecha_vencimiento,
                    pd.fecha_registro,
                    d.nom_comercial as comentarios
                FROM productos_donante pd
                INNER JOIN catalogo_productos c ON pd.id_catalogo = c.id_catalogo
                INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
                INNER JOIN unidades u ON c.id_unidad = u.id_unidad
                INNER JOIN donante d ON pd.id_donante = d.id_usu_donante
                ORDER BY pd.fecha_registro DESC
                LIMIT :limit OFFSET :offset";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}