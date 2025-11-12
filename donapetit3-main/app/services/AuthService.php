<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/Auth.php';
require_once __DIR__ . '/../model/codigo_verificacion.php';

class AuthService
{
    private Auth $authModel;
    private CodigoVerificacion $codigoModel;

    public function __construct()
    {
        $this->authModel = new Auth();
        $this->codigoModel = new CodigoVerificacion();
    }

    /**
     * Solicita el restablecimiento de contraseña
     * Genera un código de 6 dígitos y lo guarda con expiración de 15 minutos
     *
     * @param string $email Email del usuario
     * @return array ['codigo' => string, 'usuario' => array]
     * @throws RuntimeException Si el usuario no existe
     */
    public function requestPasswordReset(string $email): array
    {
        // Normalizar y validar el email
        $email = trim(strtolower($email));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('El email proporcionado no es válido.');
        }

        // Obtener el usuario por correo
        $usuario = $this->authModel->usuarioPorEmail($email);

        if (!$usuario) {
            throw new \RuntimeException('No existe una cuenta con ese email.');
        }

        // Generar código de 6 dígitos (string con ceros a la izquierda)
        $codigo = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Calcular expiración: +15 minutos desde ahora
        $expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Desactivar códigos previos del usuario
        $this->codigoModel->desactivarCodigosActivos((int)$usuario['id_usuario']);

        // Guardar el nuevo código
        $this->codigoModel->crear(
            (int)$usuario['id_usuario'],
            $codigo,
            $expiracion,
            1
        );

        return [
            'codigo' => $codigo,
            'usuario' => $usuario
        ];
    }

    /**
     * Verifica un código de recuperación
     *
     * @param string $email Email del usuario
     * @param string $codigo Código de 6 dígitos
     * @return bool True si el código es válido
     * @throws RuntimeException Si el código es inválido o expiró
     */
    public function verifyResetCode(string $email, string $codigo): bool
    {
        $email = trim(strtolower($email));
        $codigo = trim($codigo);

        // Obtener el usuario
        $usuario = $this->authModel->usuarioPorEmail($email);

        if (!$usuario) {
            throw new \RuntimeException('Usuario no encontrado.');
        }

        // Buscar código activo y no expirado
        $codigoValido = $this->codigoModel->buscarCodigoActivo(
            (int)$usuario['id_usuario'],
            $codigo
        );

        if (!$codigoValido) {
            throw new \RuntimeException('Código inválido o expirado.');
        }

        return true;
    }

    /**
     * Cambia la contraseña usando un código válido
     *
     * @param string $email Email del usuario
     * @param string $codigo Código de verificación
     * @param string $nuevaContrasena Nueva contraseña
     * @return bool True si se cambió correctamente
     * @throws RuntimeException Si hay algún error
     */
    public function resetPasswordWithCode(string $email, string $codigo, string $nuevaContrasena): bool
    {
        $email = trim(strtolower($email));
        $codigo = trim($codigo);

        // Verificar que el código sea válido
        $this->verifyResetCode($email, $codigo);

        // Obtener el usuario
        $usuario = $this->authModel->usuarioPorEmail($email);

        // Cambiar la contraseña usando el método del modelo Auth
        $resultado = $this->authModel->cambiarContrasenaConCodigo(
            $email,
            $codigo,
            $nuevaContrasena
        );

        if (!$resultado) {
            throw new \RuntimeException('No se pudo cambiar la contraseña.');
        }

        // Desactivar el código usado
        $codigoData = $this->codigoModel->buscarCodigoActivo(
            (int)$usuario['id_usuario'],
            $codigo
        );

        if ($codigoData) {
            $this->codigoModel->desactivarPorId((int)$codigoData['id_cod']);
        }

        return true;
    }
}
