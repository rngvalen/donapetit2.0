<?php
declare(strict_types=1);

require_once __DIR__ . '/usuario.php';
require_once __DIR__ . '/codigo_verificacion.php';

class AuthService {
    private UserRepository $users;
    private CodigoVerificacion $codes;

    public function __construct() {
        $this->users = new UserRepository();
        $this->codes = new CodigoVerificacion();
    }

    public function register(
        string $nombre,
        string $email,
        string $password,
        ?string $rol = 'donante',
        ?string $telefono = null,
        ?string $latitud = null,
        ?string $longitud = null
    ): int {
        $email = strtolower(trim($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email invalido');
        }
        if (strlen($password) < 8) {
            throw new InvalidArgumentException('La contrasena debe tener al menos 8 caracteres');
        }
        if ($this->users->findByEmail($email)) {
            throw new DomainException('El email ya esta registrado');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        return $this->users->create([
            'nombre'        => $nombre,
            'email'         => $email,
            'password_hash' => $hash,
            'rol'           => $rol ?? 'donante',
            'telefono'      => $telefono,
            'latitud'       => $latitud,
            'longitud'      => $longitud,
            'activo'        => 1,
        ]);
    }

    public function login(string $email, string $password): array {
        $email = strtolower(trim($email));
        $user  = $this->users->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new DomainException('Credenciales invalidas');
        }

        // Rehash transparente si el algoritmo o costo cambio
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $new = password_hash($password, PASSWORD_DEFAULT);
            $this->users->updatePasswordHash((int)$user['id'], $new);
            $user['password_hash'] = $new;
        }

        if (isset($user['activo']) && (int)$user['activo'] !== 1) {
            throw new DomainException('Cuenta inactiva');
        }

        return $user;
    }

    public function requestPasswordReset(string $email): array {
        $email = strtolower(trim($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email invalido');
        }

        $user = $this->users->findByEmail($email);
        if (!$user) {
            throw new DomainException('No existe una cuenta con ese email');
        }

        $codigo = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiracion = (new DateTimeImmutable('+15 minutes'))->format('Y-m-d H:i:s');

        $this->codes->desactivarCodigosActivos((int)$user['id']);
        $this->codes->crear((int)$user['id'], $codigo, $expiracion);

        return [
            'codigo' => $codigo,
            'usuario' => $user,
        ];
    }

    public function verifyResetCode(string $email, string $codigo): array {
        $email = strtolower(trim($email));
        $codigo = trim($codigo);

        if ($codigo === '' || strlen($codigo) !== 6) {
            throw new InvalidArgumentException('Codigo invalido');
        }

        $user = $this->users->findByEmail($email);
        if (!$user) {
            throw new DomainException('Usuario no encontrado');
        }

        $registro = $this->codes->buscarCodigoActivo((int)$user['id'], $codigo);
        if (!$registro) {
            throw new DomainException('Codigo invalido o expirado');
        }

        return [
            'usuario' => $user,
            'codigo' => $registro,
        ];
    }

    public function resetPasswordWithCode(string $email, string $codigo, string $nuevaContrasena): void {
        if (strlen($nuevaContrasena) < 8) {
            throw new InvalidArgumentException('La contrasena debe tener al menos 8 caracteres');
        }

        $resultado = $this->verifyResetCode($email, $codigo);
        $user = $resultado['usuario'];
        $registro = $resultado['codigo'];

        $hash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
        $this->users->updatePasswordHash((int)$user['id'], $hash);
        $this->codes->desactivarPorId((int)$registro['id_cod']);
    }
}
