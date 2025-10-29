<?php
require_once "Model.php";

class StockProductoDonacion extends Model {
    protected string $table = "stock_producto_donacion";
    protected string $pk = "id_stock_producto_donacion";

    // Guarda stock del producto cargado
    public function crear($id_producto, $cantidad, $fecha_vencimiento) {
        $now = date('Y-m-d H:i:s');
        return $this->insert([
            'id_producto' => $id_producto,
            'cantidad' => $cantidad,
            'fecha_vencimiento' => $fecha_vencimiento,
            'create_at' => $now
        ]);
    }

    // Obtener stock asociado a un producto
    public function obtenerPorProducto($id_producto) {
        self::initDb();
        $stmt = self::$db->prepare("
            SELECT * FROM {$this->table}
            WHERE id_producto = ?
        ");
        $stmt->execute([$id_producto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
