<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/AuthService.php';

class AuthController {
    private $auth;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->auth = new AuthService();
    }

    public function mostrarRegistro(): void {
        require __DIR__ . '/../view/auth/register.php';
    }

    public function registrar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarRegistro');
            exit;
        }

        $nombre     = trim($_POST['nombre'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        $telefono   = trim($_POST['telefono'] ?? '');
        $rol        = $_POST['rol'] ?? 'donante';
        $latitud    = $_POST['latitud'] ?? null;
        $longitud   = $_POST['longitud'] ?? null;

        if ($nombre === '' || $email === '' || $contrasena === '') {
            $_SESSION['error'] = 'Faltan campos obligatorios.';
            header('Location: ?controller=Auth&action=mostrarRegistro');
            exit;
        }

        try {
            $this->auth->register(
                $nombre,
                $email,
                $contrasena,
                $rol,
                $telefono !== '' ? $telefono : null,
                $latitud !== '' ? $latitud : null,
                $longitud !== '' ? $longitud : null
            );

            $_SESSION['success'] = 'Registro exitoso. Ya podés iniciar sesión.';
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;

        } catch (DomainException|InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?controller=Auth&action=mostrarRegistro');
            exit;

        } catch (Throwable $e) {
            $_SESSION['error'] = 'No se pudo registrar el usuario.';
            header('Location: ?controller=Auth&action=mostrarRegistro');
            exit;
        }
    }

    public function mostrarLogin(): void {
        require __DIR__ . '/../view/auth/login.php';
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Debes completar todos los campos.';
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        try {
            $usuario = $this->auth->login($email, $password);

            // Seguridad: prevenir fijación de sesión
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id'    => (int)($usuario['id'] ?? 0),
                'name'  => $usuario['nombre'] ?? '',
                'rol'   => $usuario['rol'] ?? '',
                'email' => $usuario['email'] ?? '',
            ];
            unset($_SESSION['error']);

            header('Location: ?controller=Home&action=index');
            exit;

        } catch (DomainException $e) {
            $_SESSION['error'] = 'Email o contraseña incorrectos.';
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;

        } catch (Throwable $e) {
            $_SESSION['error'] = 'Error en el inicio de sesión.';
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }
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
