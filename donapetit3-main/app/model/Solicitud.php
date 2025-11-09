<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

/**
 * Modelo Solicitud para administrar solicitudes y solicitudes_detalle
 * Basado en la estructura real de la base de datos donappetit
 */
class Solicitud extends Model
{
    protected string $table = 'solicitudes';
    protected string $pk = 'id_solicitud';

    /**
     * Crea una nueva solicitud
     * 
     * @param int $idReceptor ID del receptor que solicita
     * @return int ID de la solicitud creada
     */
    public function crearSolicitud(int $idReceptor): int
    {
        self::initDb();
        
        $now = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO solicitudes (create_at, update_at, id_receptorfk) 
                VALUES (:create_at, :update_at, :id_receptor)";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':create_at' => $now,
            ':update_at' => $now,
            ':id_receptor' => $idReceptor
        ]);
        
        return (int) self::$db->lastInsertId();
    }

    /**
     * Agrega un detalle a una solicitud
     * 
     * @param int $idSolicitud ID de la solicitud
     * @param int $idStock ID del stock del producto
     * @param int $cantidad Cantidad solicitada
     * @param string|null $fechaProgramada Fecha programada para retiro
     * @return int ID del detalle creado
     */
    public function agregarDetalle(
        int $idSolicitud,
        int $idStock,
        int $cantidad,
        ?string $fechaProgramada = null
    ): int {
        self::initDb();
        
        // Si no hay fecha programada, usar fecha actual + 1 día
        if ($fechaProgramada === null) {
            $fechaProgramada = date('Y-m-d H:i:s', strtotime('+1 day'));
        } else {
            $fechaProgramada = $fechaProgramada . ' 00:00:00';
        }
        
        $sql = "INSERT INTO solicitudes_detalle 
                (id_solicitudfk, fecha_programada, fecha_retiro, estado, cantidad) 
                VALUES (:id_solicitud, :fecha_programada, :fecha_retiro, :estado, :cantidad)";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':id_solicitud' => $idSolicitud,
            ':fecha_programada' => $fechaProgramada,
            ':fecha_retiro' => '0000-00-00 00:00:00',
            ':estado' => 0, // 0 = pendiente
            ':cantidad' => $cantidad
        ]);
        
        $idDetalle = (int) self::$db->lastInsertId();
        
        // Crear relación en movimiento
        $this->crearMovimiento($idDetalle, $idStock);
        
        return $idDetalle;
    }

    /**
     * Crea un registro de movimiento relacionando solicitud con stock
     * 
     * @param int $idSolicitudDetalle ID del detalle de solicitud
     * @param int $idStock ID del stock
     */
    private function crearMovimiento(int $idSolicitudDetalle, int $idStock): void
    {
        self::initDb();
        
        // Obtener información del stock
        $sqlStock = "SELECT id_producto, id_donante FROM stock_productos WHERE id_stock = :id_stock";
        $stmt = self::$db->prepare($sqlStock);
        $stmt->execute([':id_stock' => $idStock]);
        $stock = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$stock) {
            return;
        }
        
        $now = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO movimiento 
                (id_solicitud_detalle, id_donacionfk, id_productofk, id_stockfk, create_at) 
                VALUES (:id_solicitud_detalle, :id_donante, :id_producto, :id_stock, :create_at)";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':id_solicitud_detalle' => $idSolicitudDetalle,
            ':id_donante' => $stock['id_donante'],
            ':id_producto' => $stock['id_producto'],
            ':id_stock' => $idStock,
            ':create_at' => $now
        ]);
    }

    /**
     * Obtiene las solicitudes pendientes de un donante específico
     * 
     * @param int $idDonante ID del donante
     * @return array Lista de solicitudes pendientes
     */
    public function solicitudesPendientesPorDonante(int $idDonante): array
    {
        self::initDb();
        
        $sql = "SELECT 
                    s.id_solicitud,
                    s.create_at as fecha_solicitud,
                    sd.id_solicitud_detalle,
                    sd.cantidad,
                    sd.estado,
                    sd.fecha_programada,
                    m.id_productofk,
                    m.id_stockfk,
                    cp.nom_producto as nombre_producto,
                    u.nombre_unidad as unidad,
                    u.abreviatura as unidad_abrev,
                    cat.nombre as categoria,
                    r.nom_institucion as institucion_receptor,
                    r.responsable as responsable_receptor
                FROM solicitudes s
                INNER JOIN solicitudes_detalle sd ON s.id_solicitud = sd.id_solicitudfk
                INNER JOIN movimiento m ON sd.id_solicitud_detalle = m.id_solicitud_detalle
                INNER JOIN productos p ON m.id_productofk = p.id_productos
                INNER JOIN cargar_productos cp ON p.id_carga_producto = cp.id_carga_producto
                INNER JOIN unidades u ON cp.id_unidades = u.id_unidad
                INNER JOIN categorias cat ON cp.id_categorias = cat.id_categoria
                INNER JOIN receptor r ON s.id_receptorfk = r.id_usu_receptor
                WHERE m.id_donacionfk = :id_donante 
                AND sd.estado = 0
                ORDER BY s.create_at DESC";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id_donante' => $idDonante]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Confirma una solicitud y resta el stock
     * 
     * @param int $idSolicitudDetalle ID del detalle de solicitud
     * @param int $idDonante ID del donante que confirma
     * @return bool true si se confirmó exitosamente
     */
    public function confirmarSolicitud(int $idSolicitudDetalle, int $idDonante): bool
    {
        self::initDb();
        
        try {
            self::$db->beginTransaction();
            
            // Obtener información de la solicitud
            $sql = "SELECT sd.cantidad, m.id_stockfk, m.id_donacionfk
                    FROM solicitudes_detalle sd
                    INNER JOIN movimiento m ON sd.id_solicitud_detalle = m.id_solicitud_detalle
                    WHERE sd.id_solicitud_detalle = :id_detalle";
            
            $stmt = self::$db->prepare($sql);
            $stmt->execute([':id_detalle' => $idSolicitudDetalle]);
            $detalle = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$detalle) {
                self::$db->rollBack();
                return false;
            }
            
            // Verificar que el donante sea el dueño del stock
            if ((int)$detalle['id_donacionfk'] !== $idDonante) {
                self::$db->rollBack();
                return false;
            }
            
            $cantidadSolicitada = (int)$detalle['cantidad'];
            $idStock = (int)$detalle['id_stockfk'];
            
            // Obtener stock actual
            $sqlStock = "SELECT cantidad FROM stock_productos WHERE id_stock = :id_stock";
            $stmt = self::$db->prepare($sqlStock);
            $stmt->execute([':id_stock' => $idStock]);
            $stock = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$stock) {
                self::$db->rollBack();
                return false;
            }
            
            $stockActual = (int)$stock['cantidad'];
            
            // Verificar que hay suficiente stock
            if ($stockActual < $cantidadSolicitada) {
                self::$db->rollBack();
                return false;
            }
            
            // Restar el stock
            $nuevoStock = $stockActual - $cantidadSolicitada;
            $sqlUpdate = "UPDATE stock_productos 
                         SET cantidad = :nuevo_stock,
                             update_at = :update_at
                         WHERE id_stock = :id_stock";
            
            $stmt = self::$db->prepare($sqlUpdate);
            $stmt->execute([
                ':nuevo_stock' => $nuevoStock,
                ':update_at' => date('Y-m-d H:i:s'),
                ':id_stock' => $idStock
            ]);
            
            // Actualizar estado de la solicitud a confirmada (1)
            $sqlEstado = "UPDATE solicitudes_detalle 
                         SET estado = 1 
                         WHERE id_solicitud_detalle = :id_detalle";
            
            $stmt = self::$db->prepare($sqlEstado);
            $stmt->execute([':id_detalle' => $idSolicitudDetalle]);
            
            self::$db->commit();
            return true;
            
        } catch (\Exception $e) {
            self::$db->rollBack();
            error_log("Error al confirmar solicitud: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Rechaza una solicitud
     * 
     * @param int $idSolicitudDetalle ID del detalle de solicitud
     * @param int $idDonante ID del donante que rechaza
     * @return bool true si se rechazó exitosamente
     */
    public function rechazarSolicitud(int $idSolicitudDetalle, int $idDonante): bool
    {
        self::initDb();
        
        // Verificar que el donante sea el dueño
        $sql = "SELECT m.id_donacionfk
                FROM solicitudes_detalle sd
                INNER JOIN movimiento m ON sd.id_solicitud_detalle = m.id_solicitud_detalle
                WHERE sd.id_solicitud_detalle = :id_detalle";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id_detalle' => $idSolicitudDetalle]);
        $detalle = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$detalle || (int)$detalle['id_donacionfk'] !== $idDonante) {
            return false;
        }
        
        // Actualizar estado a rechazada (2)
        $sqlUpdate = "UPDATE solicitudes_detalle 
                     SET estado = 2 
                     WHERE id_solicitud_detalle = :id_detalle";
        
        $stmt = self::$db->prepare($sqlUpdate);
        return $stmt->execute([':id_detalle' => $idSolicitudDetalle]);
    }

    /**
     * Obtiene productos disponibles con stock para solicitar
     * 
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de productos disponibles
     */
    public function obtenerProductosDisponibles(int $limit = 100, int $offset = 0): array
    {
        self::initDb();
        
        $sql = "SELECT 
                    sp.id_stock,
                    sp.cantidad as cantidad_disponible,
                    sp.fecha_venc,
                    sp.id_producto,
                    sp.id_donante,
                    cp.nom_producto as nombre_producto,
                    d.nom_comercial as nombre_donante,
                    u.Latitud,
                    u.Longitud,
                    un.nombre_unidad as unidad,
                    un.abreviatura as unidad_abrev,
                    cat.nombre as categoria
                FROM stock_productos sp
                INNER JOIN productos p ON sp.id_producto = p.id_productos
                INNER JOIN cargar_productos cp ON p.id_carga_producto = cp.id_carga_producto
                INNER JOIN donante d ON sp.id_donante = d.id_usu_donante
                INNER JOIN usuarios u ON d.id_usu_donante = u.id_usuario
                INNER JOIN unidades un ON cp.id_unidades = un.id_unidad
                INNER JOIN categorias cat ON cp.id_categorias = cat.id_categoria
                WHERE sp.cantidad > 0
                ORDER BY sp.create_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}