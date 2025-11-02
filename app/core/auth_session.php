<?php
require_once __DIR__ . '/../../config/roles.php';

function _ensure_session_started(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function current_user(): ?array {
    _ensure_session_started();
    return $_SESSION['user'] ?? null; // ['id','name','email','rol']
}

function is_logged(): bool {
    return current_user() !== null;
}

function normalize_role(?string $role): ?string {
    if ($role === null) {
        return null;
    }

    $normalized = strtolower(trim($role));
    if ($normalized === '') {
        return null;
    }

    if ($normalized === 'admin') {
        $normalized = ROLE_ADMIN;
    }

    return $normalized;
}

function user_role(): ?string {
    $u = current_user();
    $role = $u['rol'] ?? null;
    return normalize_role(is_string($role) ? $role : null);
}

function has_role($roles): bool {
    if (!is_array($roles)) {
        $roles = [$roles];
    }

    $userRole = user_role();
    if ($userRole === null) {
        return false;
    }

    foreach ($roles as $role) {
        $candidate = normalize_role(is_string($role) ? $role : null);
        if ($candidate !== null && $candidate === $userRole) {
            return true;
        }
    }

    return false;
}

// Permisos opcionales (usa $PERMISOS del config)
function can(string $perm): bool {
    global $PERMISOS;
    if (!isset($PERMISOS[$perm])) {
        return false;
    }
    return has_role($PERMISOS[$perm]);
}

// Setea la sesion al loguear
function set_user_session(array $user): void {
    _ensure_session_started();
    // Seguridad: prevenir fijacion de sesion
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id'    => (int)($user['id'] ?? 0),
        'name'  => $user['nombre'] ?? ($user['name'] ?? ''),
        'email' => $user['email'] ?? '',
        'rol'   => normalize_role(is_string($user['rol'] ?? null) ? $user['rol'] : null) ?? ROLE_DONANTE,
    ];
}

// Limpia sesion al salir
function clear_session(): void {
    _ensure_session_started();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

// Guards de backend (para usar en controladores)
function requireLogin(): void {
    if (!is_logged()) {
        $_SESSION['error'] = 'Debes iniciar sesion.';
        header('Location: ?controller=Auth&action=mostrarLogin');
        exit;
    }
}

function requireRole($roles): void {
    requireLogin();
    if (!has_role($roles)) {
        forbidden();
    }
}

function requirePermission(string $perm): void {
    requireLogin();
    if (!can($perm)) {
        forbidden();
    }
}

function forbidden(): void {
    http_response_code(403);
    // Vista muy simple de 403
    echo '<h1>403 - Acceso denegado</h1><p>No tenes permisos para ver esta pagina.</p>';
    exit;
}