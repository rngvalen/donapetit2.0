<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

/**
 * Estadisticas derivadas de las donaciones efectivas.
 * Trabaja con la tabla 'detalle' que registra donaciones confirmadas.
 */
class Donacion extends Model
{
    protected string $table = 'detalle';
    protected string $pk = 'id_detalle';

    /**
     * Cantidad de registros confirmados (donaciones efectivas).
     *
     * @param int|null $idDonante Si se proporciona, filtra por donante
     * @return int
     */
    public function totalRegistros(?int $idDonante = null): int
    {
        self::initDb();

        if ($idDonante === null) {
            $stmt = self::$db->query('SELECT COUNT(*) FROM detalle WHERE donacion_efectiva = 1');
            return (int)($stmt->fetchColumn() ?: 0);
        }

        $sql = "SELECT COUNT(*)
                FROM detalle d
                INNER JOIN productos_solicitados ps ON ps.id_producto_solicitado = d.id_producto_solicitado
                INNER JOIN productos_donante pd ON pd.id_producto_donante = ps.id_producto_donante
                WHERE d.donacion_efectiva = 1
                AND pd.id_donante = ?";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([$idDonante]);
        return (int)($stmt->fetchColumn() ?: 0);
    }

    /**
     * Total de unidades donadas.
     *
     * @param int|null $idDonante Si se proporciona, filtra por donante
     * @return int
     */
    public function totalCantidad(?int $idDonante = null): int
    {
        self::initDb();

        if ($idDonante === null) {
            $stmt = self::$db->query('SELECT COALESCE(SUM(cantidad_donada), 0) FROM detalle WHERE donacion_efectiva = 1');
            return (int)($stmt->fetchColumn() ?: 0);
        }

        $sql = "SELECT COALESCE(SUM(d.cantidad_donada), 0)
                FROM detalle d
                INNER JOIN productos_solicitados ps ON ps.id_producto_solicitado = d.id_producto_solicitado
                INNER JOIN productos_donante pd ON pd.id_producto_donante = ps.id_producto_donante
                WHERE d.donacion_efectiva = 1
                AND pd.id_donante = ?";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([$idDonante]);
        return (int)($stmt->fetchColumn() ?: 0);
    }

    /**
     * Compatibilidad con el API anterior (usa totalCantidad).
     *
     * @param string $estado Ignorado, siempre retorna total de donaciones efectivas
     * @param int|null $idDonante Si se proporciona, filtra por donante
     * @return int
     */
    public function totalPorEstado(string $estado, ?int $idDonante = null): int
    {
        return $this->totalCantidad($idDonante);
    }

    /**
     * Productos mas donados (para grafico de barras).
     *
     * @param int $limit Limite de productos a retornar
     * @param int|null $idDonante Si se proporciona, filtra por donante
     * @return array{labels:array<int,string>,values:array<int,int>}
     */
    public function topProductos(int $limit = 5, ?int $idDonante = null): array
    {
        self::initDb();

        $sql = "
            SELECT
                cat.nom_producto,
                SUM(d.cantidad_donada) AS total
            FROM detalle d
            INNER JOIN productos_solicitados ps ON ps.id_producto_solicitado = d.id_producto_solicitado
            INNER JOIN productos_donante pd ON pd.id_producto_donante = ps.id_producto_donante
            INNER JOIN catalogo_productos cat ON cat.id_catalogo = pd.id_catalogo
            WHERE d.donacion_efectiva = 1"
            . ($idDonante !== null ? " AND pd.id_donante = :id_donante" : "") . "
            GROUP BY cat.nom_producto
            ORDER BY total DESC
            LIMIT :limit
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        if ($idDonante !== null) {
            $stmt->bindValue(':id_donante', $idDonante, \PDO::PARAM_INT);
        }
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
            $labels[] = (string)$row['nom_producto'];
            $values[] = (int)$row['total'];
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * Donaciones agrupadas por mes para graficar tendencia.
     *
     * @param int $months Cantidad de meses a mostrar
     * @param int|null $idDonante Si se proporciona, filtra por donante
     * @return array{labels:array<int,string>,values:array<int,int>}
     */
    public function frecuenciaMensual(int $months = 6, ?int $idDonante = null): array
    {
        self::initDb();

        $months = max(1, $months);
        $current = new \DateTimeImmutable('first day of this month');
        $start = $current->sub(new \DateInterval('P' . ($months - 1) . 'M'));
        $startDate = $start->format('Y-m-01 00:00:00');

        $sql = "
            SELECT DATE_FORMAT(d.fecha_registro, '%Y-%m') AS month_key,
                   SUM(d.cantidad_donada) AS total
            FROM detalle d
            INNER JOIN productos_solicitados ps ON ps.id_producto_solicitado = d.id_producto_solicitado
            INNER JOIN productos_donante pd ON pd.id_producto_donante = ps.id_producto_donante
            WHERE d.donacion_efectiva = 1
              AND d.fecha_registro >= :start"
              . ($idDonante !== null ? " AND pd.id_donante = :id_donante" : "") . "
            GROUP BY month_key
            ORDER BY month_key
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':start', $startDate, \PDO::PARAM_STR);
        if ($idDonante !== null) {
            $stmt->bindValue(':id_donante', $idDonante, \PDO::PARAM_INT);
        }
        $stmt->execute();

        $data = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $data[(string)$row['month_key']] = (int)($row['total'] ?? 0);
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
            $labels[] = $monthNames[(int)$point->format('n')];
            $values[] = $data[$key] ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
