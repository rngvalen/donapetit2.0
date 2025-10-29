<?php
require_once __DIR__ . '/../model/Auth.php';

class AuthController {
    private $auth;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->auth = new Auth();
    }

    // Mostrar formulario de registro
    public function mostrarRegistro() {
        require __DIR__ . '/../view/auth/register.php';
    }

    // Procesar registro
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?controller=Auth&action=mostrarRegistro");
            exit;
        }

        $nombre = $_POST['nombre'] ?? null;
        $email = $_POST['email'] ?? null;
        $contrasena = $_POST['contrasena'] ?? null;
        $telefono = $_POST['telefono'] ?? null;
        $rol = $_POST['rol'] ?? 'donante';
        $latitud = $_POST['latitud'] ?? null;
        $longitud = $_POST['longitud'] ?? null;

        if (!$nombre || !$email || !$contrasena) {
            $_SESSION['error'] = "Faltan campos obligatorios";
            header("Location: ?controller=Auth&action=mostrarRegistro");
            exit;
        }

        $ok = $this->auth->registrarUsuario($nombre, $email, $contrasena, $rol, $telefono, $latitud, $longitud);

        if ($ok) {
            $_SESSION['success'] = "Registro exitoso. Iniciá sesión ✨";
            header("Location: ?controller=Auth&action=mostrarLogin");
            exit;
        } else {
            $_SESSION['error'] = "No se pudo registrar el usuario";
            header("Location: ?controller=Auth&action=mostrarRegistro");
            exit;
        }
    }

    // Mostrar login
    public function mostrarLogin() {
        require __DIR__ . '/../view/auth/login.php';
    }

    // Procesar login ✅
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?controller=Auth&action=mostrarLogin");
            exit;
        }

        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$email || !$password) {
            $_SESSION['error'] = "Debe completar todos los campos";
            header("Location: ?controller=Auth&action=mostrarLogin");
            exit;
        }

        $usuario = $this->auth->login($email, $password);

        if (!$usuario) {
            $_SESSION['error'] = "Email o contraseña incorrectos";
            header("Location: ?controller=Auth&action=mostrarLogin");
            exit;
        }

        // ✅ Guardamos datos del usuario - AJUSTA LOS NOMBRES DE COLUMNAS
        $_SESSION['user'] = [
            'id'   => $usuario['id_usuario'] ?? $usuario['ID_Usuario'] ?? $usuario['id'],
            'name' => $usuario['nombre'] ?? $usuario['Nombre'],
            'rol'  => $usuario['rol'] ?? $usuario['Rol']
        ];

        header("Location: ?controller=Home&action=index");
        exit;
    }

    // Logout ✅
    public function logout() {
        session_destroy();
        header("Location: ?controller=Auth&action=mostrarLogin");
        exit;
    }
}