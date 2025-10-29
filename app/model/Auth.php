<?php
require_once __DIR__ . '/../../config/bdconexion.php';

class Auth {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // REGISTRAR NUEVO USUARIO CON LAT/LON
    public function registrarUsuario($nombre, $email, $contrasena, $rol = 'donante', $telefono = null, $latitud = null, $longitud = null) {
        try {
            // Verificar si el email ya existe
            $check = $this->conn->prepare("SELECT * FROM usuarios WHERE Email = :email");
            $check->bindParam(':email', $email);
            $check->execute();
            if ($check->rowCount() > 0) {
                echo "El email ya esta registrado.";
                return false;
            }

            // Hashear contrasena
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Insertar nuevo usuario con ubicación
            $sql = "INSERT INTO usuarios (Nombre, Email, `contrasena`, rol, telefono, activo, Latitud, Longitud)
                    VALUES (:nombre, :email, :contrasena, :rol, :telefono, 1, :latitud, :longitud)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contrasena', $hash);
            $stmt->bindParam(':rol', $rol);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':latitud', $latitud);
            $stmt->bindParam(':longitud', $longitud);

            $ok = $stmt->execute();

            if (!$ok) {
                var_dump($stmt->errorInfo());
            }

            return $ok;
        } catch (PDOException $e) {
            echo "Error al registrar: " . $e->getMessage();
            return false;
        }
    }

    // BUSCAR USUARIO POR EMAIL
    public function usuarioPorEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}