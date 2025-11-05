<?php
/**
 * Router principal de la aplicacion DonAppetit.
 */

$controller = $_GET['controller'] ?? null;
$action = $_GET['action'] ?? null;

if ($controller === null) {
    if (!empty($_SESSION['user'])) {
        $controller = 'Home';
        $action = $action ?? 'index';
    } else {
        $controller = 'Auth';
        $action = $action ?? 'mostrarLogin';
    }
}

if ($action === null) {
    $action = $controller === 'Auth' ? 'mostrarLogin' : 'index';
}

$controllerClass = $controller . 'Controller';
$controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo '<h2>Controlador no encontrado: ' . htmlspecialchars($controllerClass, ENT_QUOTES, 'UTF-8') . '</h2>';
    return;
}

require_once $controllerFile;

if (!class_exists($controllerClass)) {
    http_response_code(500);
    echo '<h2>Clase de controlador inexistente: ' . htmlspecialchars($controllerClass, ENT_QUOTES, 'UTF-8') . '</h2>';
    return;
}

$controllerInstance = new $controllerClass();

if (!method_exists($controllerInstance, $action)) {
    http_response_code(404);
    echo '<h2>Accion no disponible: ' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . '</h2>';
    return;
}

$controllerInstance->$action();