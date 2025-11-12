<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

class Solicitud extends Model
{
    protected string $table = 'solicitud';
    protected string $pk = 'id_solicitud';

    /**
     * Crea una nueva solicitud
     */
    public function crearSolicitud(
        int $idReceptor,
        int $idDonante,
        ?string $comentarios = null
    ): int {
        self::initDb();
        
        $sql = "INSERT INTO solicitud 
                (id_receptor, id_donante, fecha_solicitud, estado, observacion) 
                VALUES (:id_receptor, :id_donante, NOW(), 'Pendiente', :observacion)";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':id_receptor' => $idReceptor,
            ':id_donante' => $idDonante,
            ':observacion' => $comentarios
        ]);
        
        return (int) self::$db->lastInsertId();
    }

    /**
     * Agrega productos a una solicitud
     */
    public function agregarProducto(
        int $idSolicitud,
        int $idProductoDonante,
        int $cantidad
    ): bool {
        self::initDb();
        
        $sql = "INSERT INTO productos_solicitados 
                (id_solicitud, id_producto_donante, cantidad_solicitada) 
                VALUES (:id_solicitud, :id_producto_donante, :cantidad)";
        
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            ':id_solicitud' => $idSolicitud,
            ':id_producto_donante' => $idProductoDonante,
            ':cantidad' => $cantidad
        ]);
    }

    /**
     * Obtiene solicitudes recibidas por un donante
     */
    public function obtenerSolicitudesDonante(int $idDonante): array
    {
        self::initDb();

        $sql = "SELECT
                    s.id_solicitud,
                    s.fecha_solicitud,
                    s.estado,
                    s.observacion,
                    s.fecha_limite_retiro,
                    s.retiro_confirmado,
                    s.fecha_retiro,
                    r.nom_institucion,
                    u.Nombre as nombre_receptor,
                    u.Email as email_receptor,
                    COUNT(ps.id_producto_solicitado) as total_productos
                FROM solicitud s
                INNER JOIN receptor r ON s.id_receptor = r.id_usu_receptor
                INNER JOIN usuarios u ON r.id_usu_receptor = u.id_usuario
                LEFT JOIN productos_solicitados ps ON s.id_solicitud = ps.id_solicitud
                WHERE s.id_donante = :id_donante
                GROUP BY s.id_solicitud, s.fecha_solicitud, s.estado, s.observacion,
                         s.fecha_limite_retiro, s.retiro_confirmado, s.fecha_retiro,
                         r.nom_institucion, u.Nombre, u.Email
                ORDER BY
                    CASE
                        WHEN s.estado = 'Pendiente' THEN 1
                        WHEN s.estado = 'Aprobada' AND s.retiro_confirmado = 0 THEN 2
                        ELSE 3
                    END,
                    s.fecha_solicitud DESC";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id_donante' => $idDonante]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene solicitudes realizadas por un receptor
     */
    public function obtenerSolicitudesReceptor(int $idReceptor): array
    {
        self::initDb();
        
        $sql = "SELECT 
                    s.id_solicitud,
                    s.fecha_solicitud,
                    s.estado,
                    s.observacion,
                    d.nom_comercial,
                    u.Nombre as nombre_donante,
                    COUNT(ps.id_producto_solicitado) as total_productos
                FROM solicitud s
                INNER JOIN donante d ON s.id_donante = d.id_usu_donante
                INNER JOIN usuarios u ON d.id_usu_donante = u.id_usuario
                LEFT JOIN productos_solicitados ps ON s.id_solicitud = ps.id_solicitud
                WHERE s.id_receptor = :id_receptor
                GROUP BY s.id_solicitud, s.fecha_solicitud, s.estado, s.observacion,
                         d.nom_comercial, u.Nombre
                ORDER BY s.fecha_solicitud DESC";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id_receptor' => $idReceptor]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el detalle de una solicitud con sus productos
     */
    public function obtenerDetalleSolicitud(int $idSolicitud): ?array
    {
        self::initDb();
        
        // Obtener datos de la solicitud
        $sql = "SELECT 
                    s.*,
                    r.nom_institucion,
                    r.num_renacom,
                    d.nom_comercial,
                    d.CUIT,
                    ur.Nombre as nombre_receptor,
                    ur.Email as email_receptor,
                    ud.Nombre as nombre_donante,
                    ud.Email as email_donante
                FROM solicitud s
                INNER JOIN receptor r ON s.id_receptor = r.id_usu_receptor
                INNER JOIN donante d ON s.id_donante = d.id_usu_donante
                INNER JOIN usuarios ur ON r.id_usu_receptor = ur.id_usuario
                INNER JOIN usuarios ud ON d.id_usu_donante = ud.id_usuario
                WHERE s.id_solicitud = :id";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id' => $idSolicitud]);
        $solicitud = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$solicitud) {
            return null;
        }
        
        // Obtener productos de la solicitud
        $sqlProductos = "SELECT 
                            ps.id_producto_solicitado,
                            ps.cantidad_solicitada,
                            pd.cantidad_disponible,
                            pd.id_producto_donante,
                            c.nom_producto,
                            cat.nombre as categoria,
                            u.abreviatura as unidad
                        FROM productos_solicitados ps
                        INNER JOIN productos_donante pd ON ps.id_producto_donante = pd.id_producto_donante
                        INNER JOIN catalogo_productos c ON pd.id_catalogo = c.id_catalogo
                        INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
                        INNER JOIN unidades u ON c.id_unidad = u.id_unidad
                        WHERE ps.id_solicitud = :id";
        
        $stmtProductos = self::$db->prepare($sqlProductos);
        $stmtProductos->execute([':id' => $idSolicitud]);
        $solicitud['productos'] = $stmtProductos->fetchAll(\PDO::FETCH_ASSOC);
        
        return $solicitud;
    }

    /**
     * Cambia el estado de una solicitud
     */
    public function cambiarEstado(int $idSolicitud, string $nuevoEstado): bool
    {
        self::initDb();
        
        $estadosValidos = ['Pendiente', 'Aprobada', 'Rechazada'];
        if (!in_array($nuevoEstado, $estadosValidos)) {
            return false;
        }
        
        $sql = "UPDATE solicitud 
                SET estado = :estado 
                WHERE id_solicitud = :id";
        
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            ':estado' => $nuevoEstado,
            ':id' => $idSolicitud
        ]);
    }

    /**
     * Aprueba solicitud y RESERVA el stock (no lo descuenta aún)
     * Establece un límite de 2 horas para que el receptor retire
     */
    public function aprobarYReservarStock(int $idSolicitud): bool
    {
        self::initDb();

        try {
            self::$db->beginTransaction();

            // Obtener productos de la solicitud
            $sql = "SELECT id_producto_solicitado, id_producto_donante, cantidad_solicitada
                    FROM productos_solicitados
                    WHERE id_solicitud = :id";

            $stmt = self::$db->prepare($sql);
            $stmt->execute([':id' => $idSolicitud]);
            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Verificar que hay stock suficiente y reservar
            foreach ($productos as $producto) {
                // Verificar stock disponible
                $sqlCheck = "SELECT cantidad_disponible, cantidad_reservada
                            FROM productos_donante
                            WHERE id_producto_donante = :id";
                $stmtCheck = self::$db->prepare($sqlCheck);
                $stmtCheck->execute([':id' => $producto['id_producto_donante']]);
                $stockActual = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

                $disponible = $stockActual['cantidad_disponible'] - $stockActual['cantidad_reservada'];

                if ($disponible < $producto['cantidad_solicitada']) {
                    throw new \Exception("Stock insuficiente para uno de los productos");
                }

                // Reservar stock (mover de disponible a reservado)
                $sqlReservar = "UPDATE productos_donante
                               SET cantidad_reservada = cantidad_reservada + :cantidad
                               WHERE id_producto_donante = :id";
                $stmtReservar = self::$db->prepare($sqlReservar);
                $stmtReservar->execute([
                    ':cantidad' => $producto['cantidad_solicitada'],
                    ':id' => $producto['id_producto_donante']
                ]);
            }

            // Establecer fecha límite de retiro (2 horas desde ahora)
            $fechaLimite = date('Y-m-d H:i:s', strtotime('+2 hours'));

            $sqlUpdate = "UPDATE solicitud
                         SET estado = 'Aprobada',
                             fecha_limite_retiro = :fecha_limite,
                             retiro_confirmado = 0,
                             visto_por_receptor = 0
                         WHERE id_solicitud = :id";

            $stmtUpdate = self::$db->prepare($sqlUpdate);
            $stmtUpdate->execute([
                ':fecha_limite' => $fechaLimite,
                ':id' => $idSolicitud
            ]);

            self::$db->commit();
            return true;

        } catch (\Throwable $e) {
            self::$db->rollBack();
            error_log("Error al aprobar y reservar stock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Confirma que el receptor retiró los productos
     * DESCUENTA el stock reservado definitivamente
     */
    public function confirmarRetiro(int $idSolicitud): bool
    {
        self::initDb();

        try {
            self::$db->beginTransaction();

            // Obtener productos de la solicitud
            $sql = "SELECT ps.id_producto_solicitado, ps.id_producto_donante, ps.cantidad_solicitada
                    FROM productos_solicitados ps
                    INNER JOIN solicitud s ON ps.id_solicitud = s.id_solicitud
                    WHERE s.id_solicitud = :id
                    AND s.estado = 'Aprobada'
                    AND s.retiro_confirmado = 0";

            $stmt = self::$db->prepare($sql);
            $stmt->execute([':id' => $idSolicitud]);
            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($productos)) {
                throw new \Exception("Solicitud no válida para confirmar retiro");
            }

            // Descontar stock definitivamente
            foreach ($productos as $producto) {
                // Reducir cantidad_disponible y cantidad_reservada
                $cantidad = $producto['cantidad_solicitada'];
                $sqlDescontar = "UPDATE productos_donante
                                SET cantidad_disponible = cantidad_disponible - ?,
                                    cantidad_reservada = cantidad_reservada - ?
                                WHERE id_producto_donante = ?";

                $stmtDescontar = self::$db->prepare($sqlDescontar);
                $stmtDescontar->execute([
                    $cantidad,
                    $cantidad,
                    $producto['id_producto_donante']
                ]);

                // Registrar en detalle como donación efectiva
                $sqlDetalle = "INSERT INTO detalle
                              (id_producto_solicitado, cantidad_donada, donacion_efectiva, fecha_registro)
                              VALUES (:id_producto_solicitado, :cantidad, 1, NOW())";

                $stmtDetalle = self::$db->prepare($sqlDetalle);
                $stmtDetalle->execute([
                    ':id_producto_solicitado' => $producto['id_producto_solicitado'],
                    ':cantidad' => $producto['cantidad_solicitada']
                ]);
            }

            // Marcar solicitud como retirada
            $sqlUpdate = "UPDATE solicitud
                         SET retiro_confirmado = 1,
                             fecha_retiro = NOW()
                         WHERE id_solicitud = :id";

            $stmtUpdate = self::$db->prepare($sqlUpdate);
            $stmtUpdate->execute([':id' => $idSolicitud]);

            self::$db->commit();
            return true;

        } catch (\Throwable $e) {
            self::$db->rollBack();
            error_log("Error al confirmar retiro: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Libera stock de solicitudes expiradas (más de 2 horas sin confirmar retiro)
     * Devuelve el stock reservado a disponible
     */
    public function liberarStockExpirado(): int
    {
        self::initDb();

        try {
            self::$db->beginTransaction();

            // Buscar solicitudes aprobadas, no retiradas y expiradas
            $sql = "SELECT s.id_solicitud, ps.id_producto_donante, ps.cantidad_solicitada
                    FROM solicitud s
                    INNER JOIN productos_solicitados ps ON s.id_solicitud = ps.id_solicitud
                    WHERE s.estado = 'Aprobada'
                    AND s.retiro_confirmado = 0
                    AND s.fecha_limite_retiro < NOW()";

            $stmt = self::$db->prepare($sql);
            $stmt->execute();
            $reservasExpiradas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $solicitudesLiberadas = [];

            foreach ($reservasExpiradas as $reserva) {
                // Liberar stock (devolver de reservado a disponible)
                $sqlLiberar = "UPDATE productos_donante
                              SET cantidad_reservada = cantidad_reservada - :cantidad
                              WHERE id_producto_donante = :id
                              AND cantidad_reservada >= :cantidad";

                $stmtLiberar = self::$db->prepare($sqlLiberar);
                $stmtLiberar->execute([
                    ':cantidad' => $reserva['cantidad_solicitada'],
                    ':id' => $reserva['id_producto_donante']
                ]);

                $solicitudesLiberadas[$reserva['id_solicitud']] = true;
            }

            // Cambiar estado de solicitudes expiradas a "Rechazada"
            if (!empty($solicitudesLiberadas)) {
                $ids = array_keys($solicitudesLiberadas);
                $placeholders = implode(',', array_fill(0, count($ids), '?'));

                $sqlUpdate = "UPDATE solicitud
                             SET estado = 'Rechazada',
                                 observacion = CONCAT(IFNULL(observacion, ''), ' [Expirado - No retirado en tiempo]')
                             WHERE id_solicitud IN ($placeholders)";

                $stmtUpdate = self::$db->prepare($sqlUpdate);
                $stmtUpdate->execute($ids);
            }

            self::$db->commit();
            return count($solicitudesLiberadas);

        } catch (\Throwable $e) {
            self::$db->rollBack();
            error_log("Error al liberar stock expirado: " . $e->getMessage());
            return 0;
        }
    }
    /**
 * Cuenta solicitudes pendientes de un donante
 */
public function contarSolicitudesPendientes(int $idDonante): int
{
    self::initDb();
    
    $sql = "SELECT COUNT(*) as total 
            FROM solicitud 
            WHERE id_donante = :id_donante 
            AND estado = 'Pendiente'";
    
    $stmt = self::$db->prepare($sql);
    $stmt->execute([':id_donante' => $idDonante]);
    
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    return (int)($result['total'] ?? 0);
}

/**
 * Cuenta solicitudes por estado de un receptor (solo no vistas)
 */
public function contarPorEstado(int $idReceptor, string $estado): int
{
    self::initDb();

    $sql = "SELECT COUNT(*) as total
            FROM solicitud
            WHERE id_receptor = :id_receptor
            AND estado = :estado
            AND visto_por_receptor = 0";

    $stmt = self::$db->prepare($sql);
    $stmt->execute([
        ':id_receptor' => $idReceptor,
        ':estado' => $estado
    ]);

    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    return (int)($result['total'] ?? 0);
}

/**
 * Marcar todas las solicitudes aprobadas/rechazadas como vistas por el receptor
 */
public function marcarComoVistoPorReceptor(int $idReceptor): bool
{
    self::initDb();

    $sql = "UPDATE solicitud
            SET visto_por_receptor = 1
            WHERE id_receptor = :id_receptor
            AND estado IN ('Aprobada', 'Rechazada')
            AND visto_por_receptor = 0";

    $stmt = self::$db->prepare($sql);
    return $stmt->execute([':id_receptor' => $idReceptor]);
}

/**
 * Obtiene resumen de solicitudes del receptor
 */
public function obtenerResumenReceptor(int $idReceptor): array
{
    self::initDb();

    $sql = "SELECT
                estado,
                COUNT(*) as total
            FROM solicitud
            WHERE id_receptor = :id_receptor
            GROUP BY estado";

    $stmt = self::$db->prepare($sql);
    $stmt->execute([':id_receptor' => $idReceptor]);

    $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $resumen = [
        'Pendiente' => 0,
        'Aprobada' => 0,
        'Rechazada' => 0,
        'total' => 0
    ];

    foreach ($resultados as $row) {
        $estado = $row['estado'];
        $total = (int)$row['total'];
        $resumen[$estado] = $total;
        $resumen['total'] += $total;
    }

    return $resumen;
}
}