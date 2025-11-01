<?php
require_once "Model.php";

/**
 * Clase CodigoVerificacion
 * Maneja las operaciones sobre la tabla `codigo_verificacion`
 */
class CodigoVerificacion extends Model {
    protected string $table = "codigo_verificacion";
    protected string $pk    = "id_cod";

    /**
     * Crear un nuevo código de verificación
     *
     * @param int $idUsuario ID del usuario
     * @param string $fechaExp Fecha de expiración (YYYY-MM-DD o DATETIME)
     * @param int $activo 1 si está activo, 0 si está inactivo
     * @return int|false ID generado o false si falla
     */
    public function crear($idUsuario, $fechaExp, $activo = 1) {
        return $this->insert([
            'id_usuario'       => $idUsuario,
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
}
