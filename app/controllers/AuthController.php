<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/authservice.php';






class AuthController {
    private $auth;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // ⬇️ importa helpers de sesión/rol
        require_once __DIR__ . '/../core/auth_session.php';

        $this->auth = new AuthService();
    }

    public function mostrarRegistro(): void {
        require __DIR__ . '/../view/auth/register.php';
    }

    public function registrar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarRegistro'); exit;
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
            header('Location: ?controller=Auth&action=mostrarRegistro'); exit;
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

            $_SESSION['success'] = 'Registro exitoso. Ya podes iniciar sesion.';
            header('Location: ?controller=Auth&action=mostrarLogin'); exit;

        } catch (DomainException|InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?controller=Auth&action=mostrarRegistro'); exit;

        } catch (Throwable $e) {
            $_SESSION['error'] = 'No se pudo registrar el usuario.';
            header('Location: ?controller=Auth&action=mostrarRegistro'); exit;
        }
    }

    public function mostrarLogin(): void {
        require __DIR__ . '/../view/auth/login.php';
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarLogin'); exit;
        }

        $email    = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Debes completar todos los campos.';
            header('Location: ?controller=Auth&action=mostrarLogin'); exit;
        }

        try {
            $usuario = $this->auth->login($email, $password);

            // (Opcional) bloquear inactivos si tu tabla tiene 'activo'
            if (isset($usuario['activo']) && (int)$usuario['activo'] !== 1) {
                $_SESSION['error'] = 'Tu cuenta está inactiva.';
                header('Location: ?controller=Auth&action=mostrarLogin'); exit;
            }

            // Guarda sesión centralizada (id, name, email, rol)
            set_user_session($usuario);
            unset($_SESSION['error']);

            header('Location: ?controller=Home&action=index'); exit;

        } catch (DomainException $e) {
            $_SESSION['error'] = 'Email o contraseña incorrectos.';
            header('Location: ?controller=Auth&action=mostrarLogin'); exit;

        } catch (Throwable $e) {
            $_SESSION['error'] = 'Error en el inicio de sesion.';
            header('Location: ?controller=Auth&action=mostrarLogin'); exit;
        }
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // usa el helper para limpiar todo prolijo
        clear_session();

        header('Location: ?controller=Auth&action=mostrarLogin'); exit;
    }
}
