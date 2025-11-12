<?php
require_once "Model.php";

/**
 * Clase CodigoVerificacion
 * Maneja las operaciones sobre la tabla `verificar_contrasena`
 */
class CodigoVerificacion extends Model {
    protected string $table = "verificar_contrasena";
    protected string $pk    = "id_cod";

    /**
     * Crear un nuevo código de verificación
     *
     * @param int $idUsuario ID del usuario
     * @param string $codigo Código de 6 dígitos
     * @param string $fechaExp Fecha de expiración (YYYY-MM-DD HH:MM:SS)
     * @param int $activo 1 si está activo, 0 si está inactivo
     * @return int|string ID generado o false si falla
     */
    public function crear(int $idUsuario, string $codigo, string $fechaExp, int $activo = 1)
    {
        return $this->insert([
            'id_usuario'       => $idUsuario,
            'codigo'           => $codigo,
            'fecha_expiracion' => $fechaExp,
            'activo'           => $activo
        ]);
    }

    /**
     * Desactiva todos los códigos activos de un usuario
     *
     * @param int $idUsuario ID del usuario
     * @return bool
     */
    public function desactivarCodigosActivos(int $idUsuario): bool
    {
        self::initDb();

        $sql = "UPDATE {$this->table}
                SET activo = 0
                WHERE id_usuario = :id_usuario
                AND activo = 1";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute([':id_usuario' => $idUsuario]);
    }

    /**
     * Busca un código activo y no expirado para un usuario
     *
     * @param int $idUsuario ID del usuario
     * @param string $codigo Código a verificar
     * @return array|null Datos del código o null si no existe/expiró
     */
    public function buscarCodigoActivo(int $idUsuario, string $codigo): ?array
    {
        self::initDb();

        $sql = "SELECT *
                FROM {$this->table}
                WHERE id_usuario = :id_usuario
                AND codigo = :codigo
                AND activo = 1
                AND fecha_expiracion > NOW()";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $idUsuario,
            ':codigo' => $codigo
        ]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Desactiva un código específico por su ID
     *
     * @param int $id ID del código
     * @return bool
     */
    public function desactivarPorId(int $id): bool
    {
        return $this->update($id, ['activo' => 0]);
    }

    /**
     * Buscar código por usuario (mantener compatibilidad)
     *
     * @param int $idUsuario ID del usuario
     * @return array|null
     */
    public function buscarPorUsuario($idUsuario): ?array
    {
        self::initDb();
        $stmt = self::$db->prepare("SELECT * FROM {$this->table} WHERE id_usuario = ? ORDER BY fecha_expiracion DESC LIMIT 1");
        $stmt->execute([$idUsuario]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Actualizar estado (activar o desactivar un código)
     *
     * @param int $id ID del código
     * @param int $activo 1 = activo, 0 = inactivo
     * @return bool
     */
    public function actualizarEstado($id, $activo): bool
    {
        return $this->update($id, ['activo' => $activo]);
    }

    /**
     * Eliminar un código
     *
     * @param int $id ID del código
     * @return bool
     */
    public function eliminarCodigo($id): bool
    {
        return $this->delete($id);
    }
}
