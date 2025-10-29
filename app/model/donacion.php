<?php
require_once __DIR__ . '/model.php';

class Donacion extends Model {
    protected string $table = "donacion";
    protected string $pk    = "id_donacion";
    /** @var array<string,bool> */
    private static array $columnCache = [];

    private function hasColumn(string $column): bool
    {
        self::initDb();

        $cacheKey = $this->table . ':' . $column;
        if (array_key_exists($cacheKey, self::$columnCache)) {
            return self::$columnCache[$cacheKey];
        }

        $stmt = self::$db->prepare('SHOW COLUMNS FROM ' . $this->table . ' LIKE ?');
        $stmt->execute([$column]);
        $exists = (bool) $stmt->fetch(\PDO::FETCH_ASSOC);
        self::$columnCache[$cacheKey] = $exists;

        return $exists;
    }

    private function productTableHas(string $column): bool
    {
        self::initDb();

        $cacheKey = 'productos:' . $column;
        if (array_key_exists($cacheKey, self::$columnCache)) {
            return self::$columnCache[$cacheKey];
        }

        $stmt = self::$db->prepare('SHOW COLUMNS FROM productos LIKE ?');
        $stmt->execute([$column]);
        $exists = (bool) $stmt->fetch(\PDO::FETCH_ASSOC);
        self::$columnCache[$cacheKey] = $exists;

        return $exists;
    }

    public function crear($idProducto, $cantidad, $fecha, $estado = "DISPONIBLE") {
        throw new \LogicException('La tabla donacion requiere mas datos (ej. id_retiros); operacion no soportada.');
    }

    public function actualizarEstado($idDonacion, $estado) {
        self::initDb();
        if (!$this->hasColumn('estado')) {
            return false;
        }

        return $this->update($idDonacion, ['estado' => $estado]);
    }

    public function buscarPorProducto($idProducto) {
        self::initDb();

        $column = $this->hasColumn('id_producto') ? 'id_producto' : 'id_productos';

        $stmt = self::$db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
        $stmt->execute([$idProducto]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function eliminarDonacion($idDonacion) {
        self::initDb();

        return $this->delete($idDonacion);
    }

    public function totalRegistros(): int {
        self::initDb();

        $stmt = self::$db->query("SELECT COUNT(*) FROM {$this->table}");

        return (int) ($stmt->fetchColumn() ?: 0);
    }

    public function totalCantidad(): int {
        self::initDb();

        $stmt = self::$db->query("SELECT COALESCE(SUM(cantidad_donado), 0) AS total FROM {$this->table}");

        return (int) ($stmt->fetchColumn() ?: 0);
    }

    public function totalPorEstado(string $estado): int {
        self::initDb();

        if (!$this->hasColumn('estado')) {
            return $this->totalCantidad();
        }

        $stmt = self::$db->prepare("SELECT COALESCE(SUM(cantidad_donado), 0) FROM {$this->table} WHERE estado = ?");
        $stmt->execute([$estado]);

        return (int) ($stmt->fetchColumn() ?: 0);
    }

    public function topProductos(int $limit = 5): array {
        self::initDb();

        $fkColumn = $this->hasColumn('id_producto') ? 'id_producto' : 'id_productos';
        $productPk = $this->productTableHas('id_producto') ? 'id_producto' : 'id_productos';

        $labelCandidate = null;
        foreach (['nom_producto', 'nombre', 'comentario'] as $candidate) {
            if ($this->productTableHas($candidate)) {
                $labelCandidate = $candidate;
                break;
            }
        }

        $fallbackExpr = "CONCAT('Producto #', d.{$fkColumn})";
        $labelExpr = $fallbackExpr;
        if ($labelCandidate !== null) {
            $labelExpr = "COALESCE(p.{$labelCandidate}, {$fallbackExpr})";
        }

        $sql = "
            SELECT
                {$labelExpr} AS nombre,
                SUM(d.cantidad_donado) AS total
            FROM {$this->table} d
            LEFT JOIN productos p ON p.{$productPk} = d.{$fkColumn}
            WHERE d.{$fkColumn} IS NOT NULL
            GROUP BY d.{$fkColumn}
            ORDER BY total DESC
            LIMIT :limit
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = (string) ($row['nombre'] ?? 'Sin datos');
            $values[] = (int) ($row['total'] ?? 0);
        }

        if ($labels === []) {
            return [
                'labels' => ['Sin datos'],
                'values' => [0],
            ];
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

        $dateColumn = null;
        if ($this->hasColumn('fecha_publicacion')) {
            $dateColumn = 'fecha_publicacion';
        } elseif ($this->hasColumn('create_at')) {
            $dateColumn = 'create_at';
        }

        if ($dateColumn === null) {
            $labels = [];
            $values = [];
            $monthNames = [
                1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
            ];
            $start = new \DateTimeImmutable('first day of this month');
            $start = $start->sub(new \DateInterval('P' . ($months - 1) . 'M'));
            for ($i = 0; $i < $months; $i++) {
                $point = $start->add(new \DateInterval('P' . $i . 'M'));
                $labels[] = $monthNames[(int) $point->format('n')];
                $values[] = 0;
            }
            return [ 'labels' => $labels, 'values' => $values ];
        }

        $sql = "
            SELECT
                DATE_FORMAT({$dateColumn}, '%Y-%m') AS month_key,
                SUM(cantidad_donado) AS total
            FROM {$this->table}
            WHERE {$dateColumn} IS NOT NULL
              AND {$dateColumn} >= :startDate
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
