<?php
declare(strict_types=1);
require_once __DIR__ . '/../model/Auth.php';
require_once __DIR__ . '/../services/EmailService.php';

class AuthController {
    private $auth;
    private $emailService;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->auth = new Auth();
        $this->emailService = new EmailService();
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

    // ========== RECUPERACIÓN DE CONTRASEÑA ==========

    // Mostrar formulario de recuperación
    public function mostrarRecuperacion(): void {
        require __DIR__ . '/../view/auth/forgot-password.php';
    }

    // Enviar código de recuperación
    public function enviarCodigo(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        if ($email === '') {
            $_SESSION['error'] = 'Debes ingresar tu email.';
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }

        $resultado = $this->auth->generarCodigoRecuperacion($email);

        if (!$resultado) {
            $_SESSION['error'] = $this->auth->getLastError() ?? 'No se pudo generar el código.';
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }

        // Enviar código por email
        $emailEnviado = $this->emailService->enviarCodigoRecuperacion(
            $resultado['email'],
            $resultado['nombre'],
            $resultado['codigo']
        );

        if (!$emailEnviado) {
            $_SESSION['error'] = 'El código fue generado pero no se pudo enviar el email. Contacta al administrador.';
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }

        // Guardar email en sesión para el siguiente paso
        $_SESSION['recuperacion_email'] = $email;
        $_SESSION['success'] = 'Se ha enviado un código de recuperación a tu email. Revisa tu bandeja de entrada.';

        header('Location: ?controller=Auth&action=mostrarVerificarCodigo');
        exit;
    }

    // Mostrar formulario de verificación de código
    public function mostrarVerificarCodigo(): void {
        if (!isset($_SESSION['recuperacion_email'])) {
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }
        require __DIR__ . '/../view/auth/verify-code.php';
    }

    // Verificar código y mostrar cambio de contraseña
    public function verificarCodigo(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarVerificarCodigo');
            exit;
        }

        $email = $_SESSION['recuperacion_email'] ?? null;
        $codigo = trim($_POST['codigo'] ?? '');

        if (!$email || $codigo === '') {
            $_SESSION['error'] = 'Debes ingresar el código.';
            header('Location: ?controller=Auth&action=mostrarVerificarCodigo');
            exit;
        }

        $codigoValido = $this->auth->verificarCodigoRecuperacion($email, $codigo);

        if (!$codigoValido) {
            $_SESSION['error'] = $this->auth->getLastError() ?? 'Código inválido o expirado.';
            header('Location: ?controller=Auth&action=mostrarVerificarCodigo');
            exit;
        }

        $_SESSION['recuperacion_codigo'] = $codigo;
        header('Location: ?controller=Auth&action=mostrarNuevaContrasena');
        exit;
    }

    // Mostrar formulario de nueva contraseña
    public function mostrarNuevaContrasena(): void {
        if (!isset($_SESSION['recuperacion_email']) || !isset($_SESSION['recuperacion_codigo'])) {
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }
        require __DIR__ . '/../view/auth/reset-password.php';
    }

    // Cambiar contraseña
    public function cambiarContrasena(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=Auth&action=mostrarNuevaContrasena');
            exit;
        }

        $email = $_SESSION['recuperacion_email'] ?? null;
        $codigo = $_SESSION['recuperacion_codigo'] ?? null;
        $nuevaContrasena = $_POST['password'] ?? '';
        $confirmarContrasena = $_POST['confirm_password'] ?? '';

        if (!$email || !$codigo) {
            $_SESSION['error'] = 'Sesión expirada.';
            header('Location: ?controller=Auth&action=mostrarRecuperacion');
            exit;
        }

        if ($nuevaContrasena === '' || $confirmarContrasena === '') {
            $_SESSION['error'] = 'Debes completar todos los campos.';
            header('Location: ?controller=Auth&action=mostrarNuevaContrasena');
            exit;
        }

        if (strlen($nuevaContrasena) < 8) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 8 caracteres.';
            header('Location: ?controller=Auth&action=mostrarNuevaContrasena');
            exit;
        }

        if ($nuevaContrasena !== $confirmarContrasena) {
            $_SESSION['error'] = 'Las contraseñas no coinciden.';
            header('Location: ?controller=Auth&action=mostrarNuevaContrasena');
            exit;
        }

        $ok = $this->auth->cambiarContrasenaConCodigo($email, $codigo, $nuevaContrasena);

        if (!$ok) {
            $_SESSION['error'] = $this->auth->getLastError() ?? 'No se pudo cambiar la contraseña.';
            header('Location: ?controller=Auth&action=mostrarNuevaContrasena');
            exit;
        }

        // Limpiar sesión de recuperación
        unset($_SESSION['recuperacion_email'], $_SESSION['recuperacion_codigo']);
        
        $_SESSION['success'] = 'Contraseña cambiada exitosamente. Ya podes iniciar sesión.';
        header('Location: ?controller=Auth&action=mostrarLogin');
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