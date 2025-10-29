<?php
require_once __DIR__ . '/../../config/bdconexion.php';

class Auth {
    private $conn;
    private $lastError = null;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getLastError() {
        return $this->lastError;
    }

    // Registrar nuevo usuario con latitud y longitud
    public function registrarUsuario($nombre, $email, $contrasena, $rol = 'donante', $telefono = null, $latitud = null, $longitud = null) {
        $this->lastError = null;

        try {
            // Verificar si el email ya existe
            $check = $this->conn->prepare("SELECT * FROM usuarios WHERE Email = :email");
            $check->bindParam(':email', $email);
            $check->execute();

            if ($check->rowCount() > 0) {
                $this->lastError = 'El email ya esta registrado.';
                return false;
            }

            // Hashear contrasena
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Insertar nuevo usuario con ubicacion
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

            if ($stmt->execute()) {
                return true;
            }

            $this->lastError = 'No se pudo registrar el usuario.';
            return false;
        } catch (PDOException $e) {
            $this->lastError = 'Error al registrar: ' . $e->getMessage();
            return false;
        }
    }

    // Buscar usuario por email
    public function usuarioPorEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar credenciales de inicio de sesion
    public function login($email, $password) {
        $this->lastError = null;

        try {
            $usuario = $this->usuarioPorEmail($email);

            if (!$usuario) {
                $this->lastError = 'Email o contrasena incorrectos.';
                return false;
            }

            if (!password_verify($password, $usuario['contrasena'])) {
                $this->lastError = 'Email o contrasena incorrectos.';
                return false;
            }

            return $usuario;
        } catch (PDOException $e) {
            $this->lastError = 'Error en login: ' . $e->getMessage();
            return false;
        }
    }
}
