<?php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

class CodigoVerificacion extends Model
{
    protected string $table = 'codigo_verificacion';
    protected string $pk = 'id_cod';

    public function crear(int $idUsuario, string $codigo, string $fechaExpiracion, bool $activo = true): int
    {
        return (int)$this->insert([
            'id_usuario'       => $idUsuario,
            'codigo'           => $codigo,
            'fecha_expiracion' => $fechaExpiracion,
            'activo'           => $activo ? '1' : '0',
        ]);
    }

    public function desactivarCodigosActivos(int $idUsuario): void
    {
        self::initDb();
        $stmt = self::$db->prepare(
            "UPDATE {$this->table} SET activo = '0' WHERE id_usuario = :id"
        );
        $stmt->execute([':id' => $idUsuario]);
    }

    public function buscarCodigoActivo(int $idUsuario, string $codigo): ?array
    {
        self::initDb();
        $stmt = self::$db->prepare(
            "SELECT *
             FROM {$this->table}
             WHERE id_usuario = :id
               AND codigo = :codigo
               AND activo = '1'
               AND fecha_expiracion > NOW()
             LIMIT 1"
        );
        $stmt->execute([
            ':id' => $idUsuario,
            ':codigo' => $codigo,
        ]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function desactivarPorId(int $idCodigo): void
    {
        self::initDb();
        $stmt = self::$db->prepare(
            "UPDATE {$this->table} SET activo = '0' WHERE {$this->pk} = :id"
        );
        $stmt->execute([':id' => $idCodigo]);
    }
}
