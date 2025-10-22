<?php
require_once __DIR__ . '/../model/Auth.php';

class AuthController {
    private $auth;

    public function __construct() {
        $this->auth = new Auth();
    }

    // Muestra el formulario
    public function mostrarRegistro() {
        require __DIR__ . '/../view/auth/register.php';
    }

    // Procesa el registro
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? null;
            $email = $_POST['email'] ?? null;
            $contrasena = $_POST['contrasena'] ?? null;
            $telefono = $_POST['telefono'] ?? null;
            $rol = $_POST['rol'] ?? 'donante';
            $latitud = $_POST['latitud'] ?? null;
            $longitud = $_POST['longitud'] ?? null;

            if (!$nombre || !$email || !$contrasena) {
                echo "Faltan campos obligatorios.";
                return;
            }

            $ok = $this->auth->registrarUsuario($nombre, $email, $contrasena, $rol, $telefono, $latitud, $longitud);

            if ($ok) {
                echo "<h3>Registro exitoso. Redirigiendo al login...</h3>";
                header("refresh:2;url=?controller=Auth&action=mostrarLogin");
                exit;
            } else {
                echo "<h3>No se pudo registrar el usuario.</h3>";
            }
        } else {
            header("Location: ?controller=Auth&action=mostrarRegistro");
        }
    }

    // Método para mostrar login (agregado)
    public function mostrarLogin() {
        require __DIR__ . '/../view/auth/login.php';
    }
}
