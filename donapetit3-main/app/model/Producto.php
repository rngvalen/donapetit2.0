<?php
require_once "Model.php";

class Producto extends Model {
    protected string $table = "productos";
    protected string $pk    = "id_producto";

    // Crea un producto nuevo
    public function crear($nombre, $unidad, $cantidad, $comentarios, $estado, $categoria_id, $id_donante) {
        $now = date('Y-m-d H:i:s');
        return $this->insert([
            'nom_producto' => $nombre,
            'unidad' => $unidad,
            'cantidad' => $cantidad,
            'comentarios' => $comentarios,
            'estado' => $estado ?? 'DISPONIBLE',
            'categoria_id' => $categoria_id,
            'id_donante' => $id_donante,
            'create_at' => $now,
            'update_at' => $now
        ]);
    }

    // Obtiene todos los productos cargados por un donante
    public function obtenerPorDonante($id_donante) {
        self::initDb();
        $stmt = self::$db->prepare("SELECT * FROM {$this->table} WHERE id_donante = ?");
        $stmt->execute([$id_donante]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodos() {
        return parent::all(100, 0);
    }
}
?>
