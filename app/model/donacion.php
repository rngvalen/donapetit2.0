<?php
require_once __DIR__ . '/model.php';

class Donacion extends Model {
    protected string $table = "donacion";
    protected string $pk    = "id_donacion";

    private string $detalleTable = 'detalles_donacion';
    private string $productosTable = 'productos';

    /** @var array<string,bool> */
    private static array $columnCache = [];

    private function hasColumn(string $table, string $column): bool
    {
        self::initDb();

        $cacheKey = $table . ':' . $column;
        if (array_key_exists($cacheKey, self::$columnCache)) {
            return self::$columnCache[$cacheKey];
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            throw new \InvalidArgumentException('Nombre de tabla invalido.');
        }

        $sql = sprintf(
            'SHOW COLUMNS FROM `%s` LIKE %s',
            $table,
            self::$db->quote($column)
        );

        $stmt = self::$db->query($sql);
        $exists = $stmt !== false && (bool) $stmt->fetch(\PDO::FETCH_ASSOC);
        self::$columnCache[$cacheKey] = $exists;

        return $exists;
    }

    private function extraerNombreProducto(?string $comentario): string
    {
        if ($comentario === null) {
            return 'Producto sin nombre';
        }

        $comentario = trim($comentario);
        if ($comentario === '') {
            return 'Producto sin nombre';
        }

        $decoded = json_decode($comentario, true);
        if (is_array($decoded)) {
            foreach (['nom_producto', 'nombre', 'producto', 'label', 'titulo'] as $key) {
                if (!empty($decoded[$key])) {
                    $value = trim((string) $decoded[$key]);
                    if ($value !== '') {
                        return $value;
                    }
                }
            }
        }

        return $comentario;
    }

    public function crear($idProducto, $cantidad, $fecha, $estado = "DISPONIBLE") {
        throw new \LogicException('La tabla donacion requiere mas datos (ej. id_retiros); operacion no soportada.');
    }

    public function actualizarEstado($idDonacion, $estado) {
        self::initDb();
        if (!$this->hasColumn($this->table, 'estado')) {
            return false;
        }

        return $this->update($idDonacion, ['estado' => $estado]);
    }

    public function buscarPorProducto($idProducto) {
        self::initDb();

        $stmt = self::$db->prepare("SELECT * FROM {$this->detalleTable} WHERE id_productofk = ?");
        $stmt->execute([$idProducto]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function eliminarDonacion($idDonacion) {
        self::initDb();

        return $this->delete($idDonacion);
    }

    public function totalRegistros(): int {
        self::initDb();

        $stmt = self::$db->query("SELECT COUNT(*) FROM {$this->detalleTable}");

        return (int) ($stmt->fetchColumn() ?: 0);
    }

    public function totalCantidad(): int {
        self::initDb();

        if (!$this->hasColumn($this->detalleTable, 'cantidad_donado')) {
            return 0;
        }

        $stmt = self::$db->query("SELECT COALESCE(SUM(cantidad_donado), 0) AS total FROM {$this->detalleTable}");

        return (int) ($stmt->fetchColumn() ?: 0);
    }

    public function totalPorEstado(string $estado): int {
        self::initDb();

        if ($this->hasColumn($this->table, 'estado')) {
            $stmt = self::$db->prepare("SELECT COALESCE(SUM(cantidad_donado), 0) FROM {$this->table} WHERE estado = ?");
            $stmt->execute([$estado]);
            return (int) ($stmt->fetchColumn() ?: 0);
        }

        return $this->totalCantidad();
    }

    public function topProductos(int $limit = 5): array {
        self::initDb();

        if (!$this->hasColumn($this->detalleTable, 'cantidad_donado')) {
            return [
                'labels' => ['Sin datos'],
                'values' => [0],
            ];
        }

        $sql = "
            SELECT
                dd.id_productofk AS product_id,
                SUM(dd.cantidad_donado) AS total,
                p.comentario
            FROM {$this->detalleTable} dd
            LEFT JOIN {$this->productosTable} p ON p.id_productos = dd.id_productofk
            WHERE dd.id_productofk IS NOT NULL
            GROUP BY dd.id_productofk, p.comentario
            ORDER BY total DESC
            LIMIT :limit
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        if ($rows === []) {
            return [
                'labels' => ['Sin datos'],
                'values' => [0],
            ];
        }

        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = $this->extraerNombreProducto($row['comentario'] ?? null);
            $values[] = (int) ($row['total'] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    public function frecuenciaMensual(int $months = 6): array {
        self::initDb();

        $months = max(1, $months);
        $current = new \DateTimeImmutable('first day of this month');
        $start = $current->sub(new \DateInterval('P' . ($months - 1) . 'M'));
        $startDate = $start->format('Y-m-01 00:00:00');

        $dateColumns = [];
        foreach (['fecha_donacion', 'create_at'] as $col) {
            if ($this->hasColumn($this->detalleTable, $col)) {
                $dateColumns[] = 'dd.' . $col;
            }
        }
        if ($this->hasColumn($this->table, 'create_at')) {
            $dateColumns[] = 'd.create_at';
        }

        if ($dateColumns === []) {
            $labels = [];
            $values = [];
            $monthNames = [
                1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
            ];
            for ($i = 0; $i < $months; $i++) {
                $point = $start->add(new \DateInterval('P' . $i . 'M'));
                $labels[] = $monthNames[(int) $point->format('n')];
                $values[] = 0;
            }
            return ['labels' => $labels, 'values' => $values];
        }

        $dateExpr = 'COALESCE(' . implode(', ', $dateColumns) . ')';

        $sql = "
            SELECT
                DATE_FORMAT({$dateExpr}, '%Y-%m') AS month_key,
                SUM(dd.cantidad_donado) AS total
            FROM {$this->detalleTable} dd
            LEFT JOIN {$this->table} d ON d.id_donacion = dd.id_donacionfk
            WHERE {$dateExpr} IS NOT NULL
              AND {$dateExpr} >= :startDate
            GROUP BY month_key
            ORDER BY month_key
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([':startDate' => $startDate]);
        $data = [];

        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $key = (string) ($row['month_key'] ?? '');
            if ($key === '') {
                continue;
            }
            $data[$key] = (int) ($row['total'] ?? 0);
        }

        $labels = [];
        $values = [];
        $monthNames = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
        ];

        for ($i = 0; $i < $months; $i++) {
            $point = $start->add(new \DateInterval('P' . $i . 'M'));
            $key = $point->format('Y-m');
            $labels[] = $monthNames[(int) $point->format('n')];
            $values[] = $data[$key] ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
