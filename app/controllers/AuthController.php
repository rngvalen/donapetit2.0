<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../model/authservice.php';

class AuthController extends Controller
{
    private AuthService $auth;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require_once __DIR__ . '/../core/auth_session.php';

        $this->auth = new AuthService();
    }

    public function mostrarRegistro(): void
    {
        $errorMessage = $_SESSION['error'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);

        $this->render(
            'auth.register',
            [
                'authPageTitle' => 'Registrarse - DonAppetit',
                'logoPath' => $this->resolveLogoPath(),
                'loginUrl' => '?controller=Auth&action=mostrarLogin',
                'errorMessage' => $errorMessage,
            ],
            'auth'
        );
    }

    public function registrar(): void
    {
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

            $_SESSION['success'] = 'Registro exitoso. Ya podes iniciar sesion.';
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

    public function mostrarLogin(): void
    {
        $errorMessage = $_SESSION['error'] ?? null;
        $successMessage = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);

        $this->render(
            'auth.login',
            [
                'authPageTitle' => 'Iniciar sesion - DonAppetit',
                'logoPath' => $this->resolveLogoPath(),
                'registerUrl' => '?controller=Auth&action=mostrarRegistro',
                'forgotUrl' => '#',
                'errorMessage' => $errorMessage,
                'successMessage' => $successMessage,
            ],
            'auth'
        );
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        $email    = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Debes completar todos los campos.';
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        try {
            $usuario = $this->auth->login($email, $password);

            if (isset($usuario['activo']) && (int)$usuario['activo'] !== 1) {
                $_SESSION['error'] = 'Tu cuenta esta inactiva.';
                header('Location: ?controller=Auth&action=mostrarLogin');
                exit;
            }

            set_user_session($usuario);
            unset($_SESSION['error']);

            header('Location: ?controller=Home&action=index');
            exit;
        } catch (DomainException $e) {
            $_SESSION['error'] = 'Email o contrasena incorrectos.';
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        } catch (Throwable $e) {
            $_SESSION['error'] = 'Error en el inicio de sesion.';
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        clear_session();

        header('Location: ?controller=Auth&action=mostrarLogin');
        exit;
    }

    private function resolveLogoPath(): string
    {
        $scriptName = (string)($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        $projectBase = $scriptDir;

        $appPosition = strpos($projectBase, '/app/');
        if ($appPosition !== false) {
            $projectBase = substr($projectBase, 0, $appPosition);
        }

        if ($projectBase === '') {
            $projectBase = '/';
        }

        $basePath = rtrim($projectBase, '/');
        $publicCandidate = $basePath . '/public/assets/img/logo-don.png';
        $assetsCandidate = $basePath . '/assets/img/logo-don.png';

        $documentRoot = rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), '\\/');
        if ($documentRoot !== '') {
            if (is_file($documentRoot . $publicCandidate)) {
                return $publicCandidate;
            }

            if (is_file($documentRoot . $assetsCandidate)) {
                return $assetsCandidate;
            }
        }

        return $publicCandidate;
    }
}
