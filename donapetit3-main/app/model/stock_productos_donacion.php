<?php
require_once "Model.php";

/**
 * Clase StockProductosDonacion
 * Relaciona productos con stock de donaciones
 */
class StockProductosDonacion extends Model {
    protected string $table = "stock_productos_donacion";
    protected string $pk    = "id_stock_productos_donaciones";

    /**
     * Crear un registro de relación stock-producto
     *
     * @param int $idProducto ID del producto
     * @param int $idStock ID del stock en donaciones
     * @return int|false ID generado o false si falla
     */
    public function crear($idProducto, $idStock) {
        return $this->insert([
            'id_producto'      => $idProducto,
            'Stock__Donaciones' => $idStock
        ]);
    }

    /**
     * Buscar productos por stock
     *
     * @param int $idStock
     * @return array
     */
    public function buscarPorStock($idStock) {
        $stmt = self::$db->prepare("SELECT * FROM {$this->table} WHERE Stock__Donaciones = ?");
        $stmt->execute([$idStock]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar stock por producto
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
