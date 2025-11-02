<?php
require_once __DIR__ . '/authservice.php';

class Auth {
    private $svc;
    private $lastError = null;

    public function __construct() {
        $this->svc = new AuthService();
    }

    public function getLastError() {
        return $this->lastError;
    }

    public function registrarUsuario($nombre, $email, $contrasena, $rol = 'donante', $telefono = null, $latitud = null, $longitud = null) {
        $this->lastError = null;
        try {
            $this->svc->register($nombre, $email, $contrasena, $rol, $telefono, $latitud, $longitud);
            return true;
        } catch (Throwable $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function usuarioPorEmail($email) {
        // Compatibilidad con codigo antiguo.
        require_once __DIR__ . '/usuario.php';
        $repo = new UserRepository();
        return $repo->findByEmail($email);
    }

    public function login($email, $password) {
        $this->lastError = null;
        try {
            return $this->svc->login($email, $password);
        } catch (Throwable $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}
