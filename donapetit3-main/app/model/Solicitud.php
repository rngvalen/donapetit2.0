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
                ORDER BY s.fecha_solicitud DESC";

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
                    s.fecha_limite_retiro,
                    s.retiro_confirmado,
                    s.fecha_retiro,
                    COALESCE(d.nom_comercial, u.Nombre) as nombre_donante,
                    COUNT(ps.id_producto_solicitado) as total_productos
                FROM solicitud s
                INNER JOIN donante d ON s.id_donante = d.id_usu_donante
                INNER JOIN usuarios u ON d.id_usu_donante = u.id_usuario
                LEFT JOIN productos_solicitados ps ON s.id_solicitud = ps.id_solicitud
                WHERE s.id_receptor = :id_receptor
                GROUP BY s.id_solicitud, s.fecha_solicitud, s.estado, s.observacion,
                         s.fecha_limite_retiro, s.retiro_confirmado, s.fecha_retiro,
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
     * Aprueba una solicitud y RESERVA el stock (no lo descuenta todavia)
     */
    public function aprobarYReservar(int $idSolicitud): bool
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

            // RESERVAR stock (incrementar cantidad_reservada)
            foreach ($productos as $producto) {
                // Primero verificar que hay stock suficiente
                $sqlCheck = "SELECT cantidad_disponible, cantidad_reservada
                            FROM productos_donante
                            WHERE id_producto_donante = :id_producto_donante";

                $stmtCheck = self::$db->prepare($sqlCheck);
                $stmtCheck->execute([':id_producto_donante' => $producto['id_producto_donante']]);
                $stockActual = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

                if (!$stockActual) {
                    throw new \Exception("Producto no encontrado ID: {$producto['id_producto_donante']}");
                }

                $stockDisponible = $stockActual['cantidad_disponible'] - $stockActual['cantidad_reservada'];

                if ($stockDisponible < $producto['cantidad_solicitada']) {
                    throw new \Exception("Stock insuficiente. Disponible: {$stockDisponible}, Solicitado: {$producto['cantidad_solicitada']}");
                }

                // Ahora sí, reservar el stock
                $sqlReservar = "UPDATE productos_donante
                                SET cantidad_reservada = cantidad_reservada + :cantidad
                                WHERE id_producto_donante = :id_producto_donante";

                $stmtReservar = self::$db->prepare($sqlReservar);
                $stmtReservar->execute([
                    ':cantidad' => $producto['cantidad_solicitada'],
                    ':id_producto_donante' => $producto['id_producto_donante']
                ]);
            }

            // Establecer fecha limite de retiro (2 horas)
            $fechaLimite = date('Y-m-d H:i:s', strtotime('+2 hours'));
            $sqlFecha = "UPDATE solicitud
                        SET estado = 'Aprobada',
                            fecha_limite_retiro = :fecha_limite,
                            retiro_confirmado = 0
                        WHERE id_solicitud = :id";

            $stmtFecha = self::$db->prepare($sqlFecha);
            $stmtFecha->execute([
                ':fecha_limite' => $fechaLimite,
                ':id' => $idSolicitud
            ]);

            self::$db->commit();
            return true;

        } catch (\Throwable $e) {
            self::$db->rollBack();
            error_log("Error al aprobar y reservar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Confirma el retiro y descuenta el stock definitivamente
     */
    public function confirmarRetiro(int $idSolicitud): bool
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

            // DESCONTAR stock y liberar reserva
            foreach ($productos as $producto) {
                $sqlDescontar = "UPDATE productos_donante
                                SET cantidad_disponible = cantidad_disponible - :cantidad,
                                    cantidad_reservada = cantidad_reservada - :cantidad
                                WHERE id_producto_donante = :id_producto_donante
                                AND cantidad_disponible >= :cantidad
                                AND cantidad_reservada >= :cantidad";

                $stmtDescontar = self::$db->prepare($sqlDescontar);
                $stmtDescontar->execute([
                    ':cantidad' => $producto['cantidad_solicitada'],
                    ':id_producto_donante' => $producto['id_producto_donante']
                ]);

                // Verificar que se haya descontado el stock
                if ($stmtDescontar->rowCount() === 0) {
                    throw new \Exception("Error al descontar stock del producto ID: {$producto['id_producto_donante']}");
                }

                // Crear registros en detalle con donacion_efectiva = 1
                $sqlDetalle = "INSERT INTO detalle
                               (id_producto_solicitado, cantidad_donada, donacion_efectiva, fecha_registro)
                               VALUES (:id_producto_solicitado, :cantidad, 1, NOW())";

                $stmtDetalle = self::$db->prepare($sqlDetalle);
                $stmtDetalle->execute([
                    ':id_producto_solicitado' => $producto['id_producto_solicitado'],
                    ':cantidad' => $producto['cantidad_solicitada']
                ]);
            }

            // Marcar retiro confirmado
            $sqlConfirmar = "UPDATE solicitud
                            SET retiro_confirmado = 1,
                                fecha_retiro = NOW()
                            WHERE id_solicitud = :id";

            $stmtConfirmar = self::$db->prepare($sqlConfirmar);
            $stmtConfirmar->execute([':id' => $idSolicitud]);

            self::$db->commit();
            return true;

        } catch (\Throwable $e) {
            self::$db->rollBack();
            error_log("Error al confirmar retiro: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Libera el stock reservado de solicitudes expiradas (llamado por CRON)
     */
    public function liberarStockExpirado(): int
    {
        self::initDb();

        try {
            self::$db->beginTransaction();

            // Buscar solicitudes aprobadas sin confirmar que pasaron el limite
            $sql = "SELECT s.id_solicitud, ps.id_producto_donante, ps.cantidad_solicitada
                    FROM solicitud s
                    INNER JOIN productos_solicitados ps ON s.id_solicitud = ps.id_solicitud
                    WHERE s.estado = 'Aprobada'
                    AND s.retiro_confirmado = 0
                    AND s.fecha_limite_retiro < NOW()";

            $stmt = self::$db->query($sql);
            $expiradas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $contador = 0;
            $solicitudesAfectadas = [];

            // Liberar stock reservado
            foreach ($expiradas as $item) {
                $sqlLiberar = "UPDATE productos_donante
                              SET cantidad_reservada = cantidad_reservada - :cantidad
                              WHERE id_producto_donante = :id_producto_donante
                              AND cantidad_reservada >= :cantidad";

                $stmtLiberar = self::$db->prepare($sqlLiberar);
                $stmtLiberar->execute([
                    ':cantidad' => $item['cantidad_solicitada'],
                    ':id_producto_donante' => $item['id_producto_donante']
                ]);

                if (!in_array($item['id_solicitud'], $solicitudesAfectadas)) {
                    $solicitudesAfectadas[] = $item['id_solicitud'];
                }
            }

            // Marcar solicitudes como rechazadas
            foreach ($solicitudesAfectadas as $idSol) {
                $sqlRechazar = "UPDATE solicitud
                               SET estado = 'Rechazada',
                                   observacion = CONCAT(COALESCE(observacion, ''), '[Expirado - No retirado en tiempo]')
                               WHERE id_solicitud = :id";

                $stmtRechazar = self::$db->prepare($sqlRechazar);
                $stmtRechazar->execute([':id' => $idSol]);
                $contador++;
            }

            self::$db->commit();
            return $contador;

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
 * Cuenta solicitudes por estado de un receptor
 */
public function contarPorEstado(int $idReceptor, string $estado): int
{
    self::initDb();
    
    $sql = "SELECT COUNT(*) as total 
            FROM solicitud 
            WHERE id_receptor = :id_receptor 
            AND estado = :estado";
    
    $stmt = self::$db->prepare($sql);
    $stmt->execute([
        ':id_receptor' => $idReceptor,
        ':estado' => $estado
    ]);
    
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    return (int)($result['total'] ?? 0);
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

/**
 * Obtiene estadísticas completas del receptor
 */
public function obtenerEstadisticasReceptor(int $idReceptor): array
{
    self::initDb();

    // Total de productos solicitados
    $sqlTotalProductos = "SELECT COALESCE(SUM(ps.cantidad_solicitada), 0) as total
                          FROM solicitud s
                          INNER JOIN productos_solicitados ps ON s.id_solicitud = ps.id_solicitud
                          WHERE s.id_receptor = :id_receptor";

    $stmt = self::$db->prepare($sqlTotalProductos);
    $stmt->execute([':id_receptor' => $idReceptor]);
    $totalProductos = (int)($stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

    // Productos recibidos (solicitudes aprobadas)
    $sqlProductosRecibidos = "SELECT COALESCE(SUM(ps.cantidad_solicitada), 0) as total
                              FROM solicitud s
                              INNER JOIN productos_solicitados ps ON s.id_solicitud = ps.id_solicitud
                              WHERE s.id_receptor = :id_receptor
                              AND s.estado = 'Aprobada'";

    $stmt = self::$db->prepare($sqlProductosRecibidos);
    $stmt->execute([':id_receptor' => $idReceptor]);
    $productosRecibidos = (int)($stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

    // Top donantes (con los que más ha interactuado)
    $sqlTopDonantes = "SELECT
                          COALESCE(d.nom_comercial, u.Nombre) as nombre_donante,
                          COUNT(DISTINCT s.id_solicitud) as total_solicitudes,
                          SUM(CASE WHEN s.estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas
                      FROM solicitud s
                      INNER JOIN productos_solicitados ps ON s.id_solicitud = ps.id_solicitud
                      INNER JOIN productos_donante pd ON ps.id_producto_donante = pd.id_producto_donante
                      LEFT JOIN donante d ON pd.id_donante = d.id_usu_donante
                      LEFT JOIN usuarios u ON pd.id_donante = u.id_usuario
                      WHERE s.id_receptor = :id_receptor
                      GROUP BY pd.id_donante, d.nom_comercial, u.Nombre
                      ORDER BY total_solicitudes DESC
                      LIMIT 5";

    $stmt = self::$db->prepare($sqlTopDonantes);
    $stmt->execute([':id_receptor' => $idReceptor]);
    $topDonantes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Productos más solicitados
    $sqlTopProductos = "SELECT
                           c.nom_producto,
                           cat.nombre as categoria,
                           SUM(ps.cantidad_solicitada) as total_solicitado,
                           u.abreviatura as unidad
                       FROM solicitud s
                       INNER JOIN productos_solicitados ps ON s.id_solicitud = ps.id_solicitud
                       INNER JOIN productos_donante pd ON ps.id_producto_donante = pd.id_producto_donante
                       INNER JOIN catalogo_productos c ON pd.id_catalogo = c.id_catalogo
                       INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
                       INNER JOIN unidades u ON c.id_unidad = u.id_unidad
                       WHERE s.id_receptor = :id_receptor
                       GROUP BY c.id_catalogo, c.nom_producto, cat.nombre, u.abreviatura
                       ORDER BY total_solicitado DESC
                       LIMIT 5";

    $stmt = self::$db->prepare($sqlTopProductos);
    $stmt->execute([':id_receptor' => $idReceptor]);
    $topProductos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Resumen por estado
    $resumen = $this->obtenerResumenReceptor($idReceptor);

    return [
        'total_solicitudes' => $resumen['total'],
        'pendientes' => $resumen['Pendiente'],
        'aprobadas' => $resumen['Aprobada'],
        'rechazadas' => $resumen['Rechazada'],
        'total_productos_solicitados' => $totalProductos,
        'productos_recibidos' => $productosRecibidos,
        'top_donantes' => $topDonantes,
        'top_productos' => $topProductos
    ];
}
}