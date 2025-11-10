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
     * Registra la entrega efectiva de una solicitud
     */
    public function registrarEntrega(int $idSolicitud): bool
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
            
            // Crear registros en detalle con donacion_efectiva = 1
            foreach ($productos as $producto) {
                $sqlDetalle = "INSERT INTO detalle 
                               (id_producto_solicitado, cantidad_donada, donacion_efectiva, fecha_registro) 
                               VALUES (:id_producto_solicitado, :cantidad, 1, NOW())";
                
                $stmtDetalle = self::$db->prepare($sqlDetalle);
                $stmtDetalle->execute([
                    ':id_producto_solicitado' => $producto['id_producto_solicitado'],
                    ':cantidad' => $producto['cantidad_solicitada']
                ]);
            }
            
            // Actualizar estado de solicitud
            $this->cambiarEstado($idSolicitud, 'Aprobada');
            
            self::$db->commit();
            return true;
            
        } catch (\Throwable $e) {
            self::$db->rollBack();
            error_log("Error al registrar entrega: " . $e->getMessage());
            return false;
        }
    }
}