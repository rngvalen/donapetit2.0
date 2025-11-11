<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/Auth.php';

class AuthController {
    private $auth;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->auth = new Auth();
    }

    public function mostrarRegistro(): void {
        require __DIR__ . '/../view/auth/register.php';
    }

    public function registrar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarRegistro');
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        $telefono = trim($_POST['telefono'] ?? '');
        $rol = $_POST['rol'] ?? 'donante';
        $latitud = $_POST['latitud'] ?? null;
        $longitud = $_POST['longitud'] ?? null;

        if ($nombre === '' || $email === '' || $contrasena === '') {
            $_SESSION['error'] = 'Faltan campos obligatorios.';
            header('Location: ?controller=Auth&action=mostrarRegistro');
            exit;
        }

        if ($this->auth->usuarioPorEmail($email)) {
            $_SESSION['error'] = 'El email ya esta registrado.';
            header('Location: ?controller=Auth&action=mostrarRegistro');
            exit;
        }

        $ok = $this->auth->registrarUsuario(
            $nombre,
            $email,
            $contrasena,
            $rol,
            $telefono !== '' ? $telefono : null,
            $latitud !== '' ? $latitud : null,
            $longitud !== '' ? $longitud : null
        );

        if ($ok) {
            $_SESSION['success'] = 'Registro exitoso. Ya podes iniciar sesion.';
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        $_SESSION['error'] = $this->auth->getLastError() ?? 'No se pudo registrar el usuario.';
        header('Location: ?controller=Auth&action=mostrarRegistro');
        exit;
    }

    public function mostrarLogin(): void {
        require __DIR__ . '/../view/auth/login.php';
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Debes completar todos los campos.';
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        $usuario = $this->auth->login($email, $password);

        if (!$usuario) {
            $_SESSION['error'] = $this->auth->getLastError() ?? 'Email o contrasena incorrectos.';
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        $_SESSION['user'] = [
            'id' => $usuario['id_usuario'] ?? $usuario['ID_Usuario'] ?? $usuario['id'] ?? null,
            'name' => $usuario['Nombre'] ?? $usuario['nombre'] ?? '',
            'rol' => $usuario['rol'] ?? '',
            'email' => $usuario['Email'] ?? $usuario['email'] ?? ''
        ];

        unset($_SESSION['error']);

        header('Location: ?controller=Home&action=index');
        exit;
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        header('Location: ?controller=Auth&action=mostrarLogin');
        exit;
    }
}
