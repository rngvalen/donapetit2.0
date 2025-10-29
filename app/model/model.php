<?php
declare(strict_types=1);

/**
 * Clase base Model para operaciones CRUD usando PDO.
 */
class Model
{
    /** @var \PDO|null Instancia PDO compartida para la conexion */
    protected static ?\PDO $db = null;

    /** @var string Nombre de la tabla asociada */
    protected string $table;

    /** @var string Nombre de la clave primaria */
    protected string $pk = 'id';

    /**
     * Inicializa la conexion compartida si aun no existe.
     */
    protected static function initDb(): void
    {
        if (self::$db instanceof \PDO) {
            return;
        }

        $configPath = __DIR__ . '/../../config/bdconexion.php';
        $pdo = require $configPath;

        if (!$pdo instanceof \PDO) {
            throw new \RuntimeException('El archivo de configuracion debe retornar una instancia de PDO.');
        }

        self::$db = $pdo;
    }

    /**
     * Busca un registro por la clave primaria.
     */
    public function find($id)
    {
        self::initDb();

        $stmt = self::$db->prepare("SELECT * FROM {$this->table} WHERE {$this->pk} = ? LIMIT 1");
        $stmt->execute([$id]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve todos los registros con paginacion.
     */
    public static function all(int $limit = 100, int $offset = 0): array
    {
        self::initDb();

        $instance = new static();
        $table = $instance->table;

        $sql = "SELECT * FROM $table LIMIT :limit OFFSET :offset";
        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Inserta un nuevo registro en la tabla.
     */
    public function insert(array $data): string
    {
        self::initDb();

        $cols = array_keys($data);
        $placeholders = array_fill(0, count($cols), '?');
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(',', $cols),
            implode(',', $placeholders)
        );

        $stmt = self::$db->prepare($sql);
        $stmt->execute(array_values($data));

        return self::$db->lastInsertId();
    }

    /**
     * Actualiza un registro por la clave primaria.
     */
    public function update($id, array $data): bool
    {
        self::initDb();

        $sets = implode(
            ',',
            array_map(
                static fn($column): string => $column . ' = ?',
                array_keys($data)
            )
        );

        $sql = sprintf('UPDATE %s SET %s WHERE %s = ?', $this->table, $sets, $this->pk);
        $stmt = self::$db->prepare($sql);

        $values = array_values($data);
        $values[] = $id;

        return $stmt->execute($values);
    }

    /**
     * Elimina un registro por la clave primaria.
     */
    public function delete($id): bool
    {
        self::initDb();

        $stmt = self::$db->prepare("DELETE FROM {$this->table} WHERE {$this->pk} = ?");

        return $stmt->execute([$id]);
    }
}