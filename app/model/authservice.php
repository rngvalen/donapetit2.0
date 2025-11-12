<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bdconexion.php';
require_once __DIR__ . '/codigo_verificacion.php';

/**
 * AuthService
 * Servicio de autenticación y recuperación de contraseña
 */
class AuthService
{
    private $conn;
    private CodigoVerificacion $codigoVerificacion;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->codigoVerificacion = new CodigoVerificacion();
    }

    /**
     * Solicitar restablecimiento de contraseña
     * Genera un código de 6 dígitos y lo persiste en la base de datos
     *
     * @param string $email Email del usuario
     * @return array ['codigo' => string, 'usuario' => array]
     * @throws Exception Si el usuario no existe o hay error en BD
     */
    public function requestPasswordReset(string $email): array
    {
        // Normalizar email
        $email = strtolower(trim($email));

        if (empty($email)) {
            throw new Exception('El email es requerido.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('El formato del email no es válido.');
        }

        // Buscar usuario por email
        $usuario = $this->obtenerUsuarioPorEmail($email);

        if (!$usuario) {
            throw new Exception('No existe una cuenta con ese email.');
        }

        // Generar código de 6 dígitos (string con padding de ceros)
        $codigo = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Calcular fecha de expiración: +15 minutos
        $fechaExpiracion = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Desactivar códigos previos del usuario
        $this->codigoVerificacion->desactivarCodigosActivos($usuario['id_usuario']);

        // Guardar nuevo código con fecha de expiración
        $idCodigo = $this->codigoVerificacion->crear(
            $usuario['id_usuario'],
            $codigo,
            $fechaExpiracion
        );

        if (!$idCodigo) {
            throw new Exception('No se pudo generar el código de recuperación.');
        }

        return [
            'codigo' => $codigo,
            'usuario' => [
                'id' => $usuario['id_usuario'],
                'email' => $usuario['Email'],
                'nombre' => $usuario['Nombre'] ?? $usuario['Email']
            ]
        ];
    }

    /**
     * Obtener usuario por email
     *
     * @param string $email
     * @return array|false
     */
    private function obtenerUsuarioPorEmail(string $email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE Email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
