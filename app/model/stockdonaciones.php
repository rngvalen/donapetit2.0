 <?php
require_once "Model.php";

/**
 * Clase StockDonaciones
 * Maneja las operaciones sobre la tabla `stock_donaciones`
 */
class StockDonaciones extends Model {
    protected string $table = "stock_donaciones";
    protected string $pk    = "id_Stock_donaciones";

    /**
     * Crear un registro en stock de donaciones
     *
     * @param int $idDonante ID del usuario donante
     * @param int $cantidad Cantidad de productos
     * @param string $fechaVenc Fecha de vencimiento (YYYY-MM-DD)
     * @return int|false ID generado o false si falla
     */
    public function crear($idDonante, $cantidad, $fechaVenc) {
        return $this->insert([
            'id_usuario_donante' => $idDonante,
            'cantidad'           => $cantidad,
            'fecha_venc'         => $fechaVenc
        ]);
    }

    /**
     * Actualizar cantidad o fecha de vencimiento
     *
     * @param int $idStock ID del stock
     * @param int $cantidad Nueva cantidad
     * @param string $fechaVenc Nueva fecha de vencimiento
     * @return bool
     */
    public function actualizarStock($idStock, $cantidad, $fechaVenc) {
        return $this->update($idStock, [
            'cantidad'   => $cantidad,
            'fecha_venc' => $fechaVenc
        ]);
    }

    /**
     * Buscar stock por donante
     *
     * @param int $idDonante
     * @return array
     */
    public function buscarPorDonante($idDonante) {
        $stmt = self::$db->prepare("SELECT * FROM {$this->table} WHERE id_usuario_donante = ?");
        $stmt->execute([$idDonante]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Eliminar un registro de stock
     *
     * @param int $idStock
     * @return bool
     */
    public function eliminarStock($idStock) {
        return $this->delete($idStock);
    }
}
