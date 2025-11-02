<?php
require_once __DIR__ . '/../../config/bdconexion.php';

class UserRepository {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function create(array $data): int {
        $sql = "INSERT INTO usuarios
            (Nombre, Email, contrasena, rol, telefono, Latitud, Longitud, activo)
            VALUES (:nombre, :email, :password_hash, :rol, :telefono, :latitud, :longitud, :activo)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':nombre'        => $data['nombre'],
            ':email'         => strtolower($data['email']),
            ':password_hash' => $data['password_hash'],
            ':rol'           => $data['rol'] ?? 'donante',
            ':telefono'      => $data['telefono'] ?? null,
            ':latitud'       => $data['latitud'] ?? null,
            ':longitud'      => $data['longitud'] ?? null,
            ':activo'        => (string)($data['activo'] ?? '1'),
        ]);
        return (int)$this->conn->lastInsertId();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->conn->prepare(
            "SELECT
                id_usuario AS id,
                Nombre AS nombre,
                Email AS email,
                contrasena AS password_hash,
                rol,
                telefono,
                Latitud AS latitud,
                Longitud AS longitud,
                activo
            FROM usuarios
            WHERE Email = :email"
        );
        $stmt->execute([':email' => strtolower($email)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->conn->prepare(
            "SELECT
                id_usuario AS id,
                Nombre AS nombre,
                Email AS email,
                contrasena AS password_hash,
                rol,
                telefono,
                Latitud AS latitud,
                Longitud AS longitud,
                activo
            FROM usuarios
            WHERE id_usuario = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(): array {
        $sql = "SELECT
                id_usuario AS id,
                Nombre AS nombre,
                Email AS email,
                contrasena AS password_hash,
                rol,
                telefono,
                Latitud AS latitud,
                Longitud AS longitud,
                activo
            FROM usuarios
            ORDER BY id_usuario DESC";
        return $this->conn->query($sql)->fetchAll();
    }

    public function updatePasswordHash(int $id, string $newHash): void {
        $stmt = $this->conn->prepare(
            "UPDATE usuarios SET contrasena = :h WHERE id_usuario = :id"
        );
        $stmt->execute([':h' => $newHash, ':id' => $id]);
    }
}
