<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../model/authservice.php';
require_once __DIR__ . '/../services/EmailService.php';

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
                'forgotUrl' => '?controller=Auth&action=mostrarRecuperacion',
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

    public function mostrarRecuperacion(): void
    {
        $errorMessage = $_SESSION['error'] ?? null;
        $successMessage = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);

        $this->render(
            'auth.forgot-password',
            [
                'authPageTitle' => 'Recuperar contrasena - DonAppetit',
                'logoPath' => $this->resolveLogoPath(),
                'loginUrl' => '?controller=Auth&action=mostrarLogin',
                'errorMessage' => $errorMessage,
                'successMessage' => $successMessage,
            ],
            'auth'
        );
    }

    public function enviarCodigo(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));

        if ($email === '') {
            $_SESSION['error'] = 'Debes ingresar un email.';
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }

        try {
            $resultado = $this->auth->requestPasswordReset($email);

            $emailService = new EmailService();
            $nombre = $resultado['usuario']['nombre'] ?? $resultado['usuario']['email'];
            $envio = $emailService->enviarCodigoRecuperacion(
                $resultado['usuario']['email'],
                (string)$nombre,
                $resultado['codigo']
            );

            if (!$envio) {
                $_SESSION['error'] = 'No pudimos enviar el email. Verifica la configuracion SMTP.';
                header('Location: ?controller=Auth&action=mostrarRecuperacion');
                exit;
            }

            $_SESSION['recuperacion_email'] = $resultado['usuario']['email'];
            unset($_SESSION['recuperacion_codigo']);
            $_SESSION['success'] = 'Enviamos un codigo de verificacion. Revisa tu bandeja y carpeta de spam.';

            header('Location: ?controller=Auth&action=mostrarVerificarCodigo');
            exit;
        } catch (DomainException|InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (Throwable $e) {
            $_SESSION['error'] = 'No se pudo generar el codigo.';
        }

        header('Location: ?controller=Auth&action=mostrarRecuperacion');
        exit;
    }

    public function mostrarVerificarCodigo(): void
    {
        if (empty($_SESSION['recuperacion_email'])) {
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }

        $errorMessage = $_SESSION['error'] ?? null;
        $successMessage = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);

        $this->render(
            'auth.verify-code',
            [
                'authPageTitle' => 'Verificar codigo - DonAppetit',
                'logoPath' => $this->resolveLogoPath(),
                'email' => $_SESSION['recuperacion_email'],
                'errorMessage' => $errorMessage,
                'successMessage' => $successMessage,
            ],
            'auth'
        );
    }

    public function verificarCodigo(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarVerificarCodigo');
            exit;
        }

        $email = $_SESSION['recuperacion_email'] ?? null;
        $codigo = trim($_POST['codigo'] ?? '');

        if ($email === null) {
            $_SESSION['error'] = 'La sesion de recuperacion expiro.';
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }

        if ($codigo === '') {
            $_SESSION['error'] = 'Debes ingresar el codigo de 6 digitos.';
            header('Location: ?controller=Auth&action=mostrarVerificarCodigo');
            exit;
        }

        try {
            $this->auth->verifyResetCode($email, $codigo);
            $_SESSION['recuperacion_codigo'] = $codigo;
            $_SESSION['success'] = 'Codigo verificado. Define tu nueva contrasena.';
            header('Location: ?controller=Auth&action=mostrarNuevaContrasena');
            exit;
        } catch (DomainException|InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (Throwable $e) {
            $_SESSION['error'] = 'Ocurrio un error al validar el codigo.';
        }

        header('Location: ?controller=Auth&action=mostrarVerificarCodigo');
        exit;
    }

    public function mostrarNuevaContrasena(): void
    {
        if (
            empty($_SESSION['recuperacion_email']) ||
            empty($_SESSION['recuperacion_codigo'])
        ) {
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }

        $errorMessage = $_SESSION['error'] ?? null;
        $successMessage = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);

        $this->render(
            'auth.reset-password',
            [
                'authPageTitle' => 'Nueva contrasena - DonAppetit',
                'logoPath' => $this->resolveLogoPath(),
                'errorMessage' => $errorMessage,
                'successMessage' => $successMessage,
            ],
            'auth'
        );
    }

    public function cambiarContrasena(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarNuevaContrasena');
            exit;
        }

        $email = $_SESSION['recuperacion_email'] ?? null;
        $codigo = $_SESSION['recuperacion_codigo'] ?? null;
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($email === null || $codigo === null) {
            $_SESSION['error'] = 'La sesion de recuperacion expiro.';
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }

        if ($password === '' || $confirmPassword === '') {
            $_SESSION['error'] = 'Debes completar ambos campos.';
            header('Location: ?controller=Auth&action=mostrarNuevaContrasena');
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Las contrasenas no coinciden.';
            header('Location: ?controller=Auth&action=mostrarNuevaContrasena');
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = 'La contrasena debe tener al menos 8 caracteres.';
            header('Location: ?controller=Auth&action=mostrarNuevaContrasena');
            exit;
        }

        try {
            $this->auth->resetPasswordWithCode($email, $codigo, $password);
            unset($_SESSION['recuperacion_email'], $_SESSION['recuperacion_codigo']);
            $_SESSION['success'] = 'Contrasena actualizada. Ya podes iniciar sesion.';
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        } catch (DomainException|InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (Throwable $e) {
            $_SESSION['error'] = 'No se pudo actualizar la contrasena.';
        }

        header('Location: ?controller=Auth&action=mostrarNuevaContrasena');
        exit;
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
