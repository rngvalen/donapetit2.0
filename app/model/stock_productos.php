<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

class StockProducto extends Model
{
    protected string $table = 'stock_productos';
    protected string $pk = 'id_stock';

    public function registrarStock(int $idDonante, int $idProducto, int $cantidad, ?string $fechaVencimiento = null): int
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $fechaNormalizada = $this->normalizeFecha($fechaVencimiento) ?? (new \DateTimeImmutable())->format('Y-m-d');

        $payload = [
            'id_donante' => $idDonante,
            'cantidad' => $cantidad,
            'id_producto' => $idProducto,
            'create_at' => $now,
            'update_at' => $now,
            'fecha_venc' => $fechaNormalizada,
        ];

        return (int)$this->insert($payload);
    }

    public function actualizarCantidadPorProducto(int $idProducto, int $cantidad, ?string $fechaVencimiento = null): bool
    {
        self::initDb();
        $fechaNormalizada = $this->normalizeFecha($fechaVencimiento);

        $sql = '
            UPDATE stock_productos
            SET cantidad = :cantidad,
                update_at = :update_at';

        $params = [
            ':cantidad' => $cantidad,
            ':update_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ':producto' => $idProducto,
        ];

        if ($fechaNormalizada !== null) {
            $sql .= ', fecha_venc = :fecha_venc';
            $params[':fecha_venc'] = $fechaNormalizada;
        }

        $sql .= ' WHERE id_producto = :producto';

        $stmt = self::$db->prepare($sql);
        $stmt->execute($params);

        $updated = $stmt->rowCount() > 0;
        return $updated;
    }

    public function obtenerDonantePorProducto(int $idProducto): ?int
    {
        self::initDb();

        $stmt = self::$db->prepare(
            'SELECT id_donante FROM stock_productos WHERE id_producto = :producto LIMIT 1'
        );
        $stmt->execute([':producto' => $idProducto]);
        $donor = $stmt->fetchColumn();

        return $donor !== false ? (int)$donor : null;
    }

    private function normalizeFecha(?string $fecha): ?string
    {
        if ($fecha === null) {
            return null;
        }

        $valor = trim($fecha);
        if ($valor === '') {
            return null;
        }

        $formats = ['Y-m-d', 'd/m/Y', 'Y/m/d', 'Y-m-d H:i:s', \DateTimeInterface::ATOM];
        foreach ($formats as $format) {
            $dt = \DateTimeImmutable::createFromFormat($format, $valor);
            if ($dt instanceof \DateTimeImmutable) {
                return $dt->format('Y-m-d');
            }
        }

        try {
            $dt = new \DateTimeImmutable($valor);
            return $dt->format('Y-m-d');
        } catch (\Exception $exception) {
            return null;
        }
    }
}
