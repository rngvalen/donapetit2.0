<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

/**
 * Modelo Categoria: permite obtener las categorías registradas.
 */
class Categoria extends Model
{
    protected string $table = 'categorias';
    protected string $pk = 'id_categoria';

    /**
     * Devuelve las categorías activas ordenadas por nombre.
     *
     * @return array<int,array{id:int,nombre:string}>
     */
    public function todas(): array
    {
        self::initDb();
        $sql = 'SELECT id_categoria, nombre FROM ' . $this->table . ' ORDER BY nombre ASC';
        $stmt = self::$db->query($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return array_values(array_map(
            static function (array $row): array {
                return [
                    'id' => (int)($row['id_categoria'] ?? 0),
                    'nombre' => (string)($row['nombre'] ?? ''),
                ];
            },
            $rows
        ));
    }
}

