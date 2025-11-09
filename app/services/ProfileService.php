<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bdconexion.php';

/**
 * Orquesta las operaciones necesarias para completar el perfil
 * (donante/receptor + direccin asociada).
 */
class ProfileService
{
    private \PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function needsCompletion(int $userId, ?string $rawRole): bool
    {
        if ($userId <= 0) {
            return false;
        }

        $role = strtolower((string)$rawRole);
        if ($role === 'donante') {
            return !$this->recordExists('donante', 'id_usu_donante', $userId);
        }

        if ($role === 'receptor') {
            return !$this->recordExists('receptor', 'id_usu_receptor', $userId);
        }

        return false;
    }

    public function saveDonante(int $userId, array $data): void
    {
        $this->conn->beginTransaction();

        try {
            $sql = <<<SQL
                INSERT INTO donante (id_usu_donante, nom_comercial, CUIT)
                VALUES (:id, :nombre, :cuit)
                ON DUPLICATE KEY UPDATE
                    nom_comercial = VALUES(nom_comercial),
                    CUIT = VALUES(CUIT)
            SQL;

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id' => $userId,
                ':nombre' => $data['nombre_comercial'],
                ':cuit' => $data['cuit'],
            ]);

            $this->upsertDireccion(
                $userId,
                $data['nombre_calle'],
                $data['num_calle'],
                $data['latitud'] ?? null,
                $data['longitud'] ?? null
            );

            $this->conn->commit();
        } catch (\Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    public function saveReceptor(int $userId, array $data): void
    {
        $this->conn->beginTransaction();

        try {
            $sql = <<<SQL
                INSERT INTO receptor (id_usu_receptor, num_renacom, nom_institucion, responsable)
                VALUES (:id, :renacom, :institucion, :responsable)
                ON DUPLICATE KEY UPDATE
                    num_renacom = VALUES(num_renacom),
                    nom_institucion = VALUES(nom_institucion),
                    responsable = VALUES(responsable)
            SQL;

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id' => $userId,
                ':renacom' => $data['num_renacom'],
                ':institucion' => $data['nom_institucion'],
                ':responsable' => $data['responsable'],
            ]);

            $this->upsertDireccion(
                $userId,
                $data['nombre_calle'],
                $data['num_calle'],
                $data['latitud'] ?? null,
                $data['longitud'] ?? null
            );

            $this->conn->commit();
        } catch (\Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    private function recordExists(string $table, string $column, int $userId): bool
    {
        $stmt = $this->conn->prepare(
            "SELECT 1 FROM {$table} WHERE {$column} = :id LIMIT 1"
        );
        $stmt->execute([':id' => $userId]);

        return (bool)$stmt->fetchColumn();
    }

    private function upsertDireccion(int $userId, string $calle, int $numero, ?float $latitud, ?float $longitud): void
    {
        $stmt = $this->conn->prepare(
            'SELECT id_direccion FROM direcciones WHERE id_usuario_direcc = :id LIMIT 1'
        );
        $stmt->execute([':id' => $userId]);

        $idDireccion = $stmt->fetchColumn();

        if ($idDireccion) {
            $update = $this->conn->prepare(
                'UPDATE direcciones
                 SET nom_calle = :calle,
                     num_calle = :numero,
                     Latitud = :lat,
                     Longitud = :lng
                 WHERE id_direccion = :dir'
            );
            $update->execute([
                ':calle' => $calle,
                ':numero' => $numero,
                ':lat' => $latitud,
                ':lng' => $longitud,
                ':dir' => $idDireccion,
            ]);

            return;
        }

        $insert = $this->conn->prepare(
            'INSERT INTO direcciones (id_usuario_direcc, nom_calle, num_calle, Latitud, Longitud)
             VALUES (:id, :calle, :numero, :lat, :lng)'
        );
        $insert->execute([
            ':id' => $userId,
            ':calle' => $calle,
            ':numero' => $numero,
            ':lat' => $latitud,
            ':lng' => $longitud,
        ]);
    }
}
