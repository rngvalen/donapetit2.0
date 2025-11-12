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
     * @param string $fechaExp Fecha de expiración (YYYY-MM-DD o DATETIME)
     * @param int $activo 1 si está activo, 0 si está inactivo
     * @return int|false ID generado o false si falla
     */
    public function crear($idUsuario, $codigo, $fechaExp, $activo = 1) {
        return $this->insert([
            'id_usuario'       => $idUsuario,
            'codigo'           => $codigo,
            'fecha_expiracion' => $fechaExp,
            'activo'           => $activo
        ]);
    }

    /**
     * Actualizar estado (activar o desactivar un código)
     *
     * @param int $id ID del código
     * @param int $activo 1 = activo, 0 = inactivo
     * @return bool
     */
    public function actualizarEstado($id, $activo) {
        return $this->update($id, ['activo' => $activo]);
    }

    /**
     * Buscar código por usuario
     *
     * @param int $idUsuario ID del usuario
     * @return array|null
     */
    public function buscarPorUsuario($idUsuario) {
        self::initDb();
        $stmt = self::$db->prepare("SELECT * FROM {$this->table} WHERE id_usuario = ?");
        $stmt->execute([$idUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Eliminar un código
     *
     * @param int $id ID del código
     * @return bool
     */
    public function eliminarCodigo($id) {
        return $this->delete($id);
    }

    /**
     * Desactivar todos los códigos activos de un usuario
     *
     * @param int $idUsuario ID del usuario
     * @return bool
     */
    public function desactivarCodigosActivos($idUsuario) {
        self::initDb();
        $stmt = self::$db->prepare("UPDATE {$this->table} SET activo = 0 WHERE id_usuario = ? AND activo = 1");
        return $stmt->execute([$idUsuario]);
    }

    /**
     * Buscar código activo y válido (no expirado) por usuario y código
     *
     * @param int $idUsuario ID del usuario
     * @param string $codigo Código de 6 dígitos
     * @return array|null
     */
    public function buscarCodigoActivo($idUsuario, $codigo) {
        self::initDb();
        $stmt = self::$db->prepare(
            "SELECT * FROM {$this->table}
             WHERE id_usuario = ?
             AND codigo = ?
             AND activo = 1
             AND fecha_expiracion > NOW()
             LIMIT 1"
        );
        $stmt->execute([$idUsuario, $codigo]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Desactivar código por ID
     *
     * @param int $id ID del código
     * @return bool
     */
    public function desactivarPorId($id) {
        return $this->update($id, ['activo' => 0]);
    }
}
