<?php
require_once __DIR__ . '/usuario.php';

class AuthService {
    private $users;

    public function __construct() {
        $this->users = new UserRepository();
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
}
