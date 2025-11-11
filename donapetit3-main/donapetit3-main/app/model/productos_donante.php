    <?php
require_once "Model.php";

/**
 * Clase ProductoDonante
 * Relaciona productos con donantes en la tabla `productos_donante`
 */
class ProductoDonante extends Model {
    protected string $table = "productos_donante";
    protected string $pk    = "id_producto_donante";

    /**
     * Crear un registro producto-donante
     *
     * @param int $idProducto ID del producto
     * @param int $idDonante ID del donante
     * @param string $fecha Fecha de la relación (YYYY-MM-DD o DATETIME)                   //Mirar si sacar o no.
     * @return int|false ID generado o false si falla
     */
    public function crear($idProducto, $idDonante, $fecha) {
        return $this->insert([
            'id_producto' => $idProducto,
            'id_donante'  => $idDonante,
            'fecha'       => $fecha
        ]);
    }


    /**
     * Buscar registros por producto
     *
     * @param int $idProducto
     * @return array
     */
    public function buscarPorProducto($idProducto) {
        $stmt = self::$db->prepare("SELECT * FROM {$this->table} WHERE id_producto = ?");
        $stmt->execute([$idProducto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
