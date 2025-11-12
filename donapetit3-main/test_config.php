<?php
/**
 * Script de prueba para verificar la configuración del proyecto
 */

echo "<!DOCTYPE html>\n";
echo "<html lang='es'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>Test de Configuración - DonAppetit</title>\n";
echo "    <style>\n";
echo "        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }\n";
echo "        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto; }\n";
echo "        h1 { color: #416d56; }\n";
echo "        .success { color: #28a745; font-weight: bold; }\n";
echo "        .error { color: #dc3545; font-weight: bold; }\n";
echo "        .info { color: #007bff; }\n";
echo "        .test { padding: 15px; margin: 10px 0; background: #f8f9fa; border-left: 4px solid #007bff; }\n";
echo "        .test.ok { border-left-color: #28a745; }\n";
echo "        .test.fail { border-left-color: #dc3545; }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";
echo "    <div class='container'>\n";
echo "        <h1>🔍 Test de Configuración - DonAppetit</h1>\n";

// Test 1: Versión de PHP
echo "        <div class='test ok'>\n";
echo "            <strong>✓ PHP Version:</strong> " . phpversion() . "\n";
echo "        </div>\n";

// Test 2: PHPMailer
echo "        <div class='test ";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';

    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        echo "ok'>\n";
        echo "            <strong>✓ PHPMailer:</strong> <span class='success'>Instalado correctamente</span>\n";
    } else {
        echo "fail'>\n";
        echo "            <strong>✗ PHPMailer:</strong> <span class='error'>No se pudo cargar la clase</span>\n";
    }
} else {
    echo "fail'>\n";
    echo "            <strong>✗ PHPMailer:</strong> <span class='error'>vendor/autoload.php no encontrado</span>\n";
}
echo "        </div>\n";

// Test 3: Conexión a base de datos
echo "        <div class='test ";
try {
    require_once __DIR__ . '/config/bdconexion.php';
    $database = new Database();
    $conn = $database->getConnection();

    if ($conn) {
        echo "ok'>\n";
        echo "            <strong>✓ Base de Datos:</strong> <span class='success'>Conexión exitosa a MySQL</span>\n";
        echo "            <br><span class='info'>Database: donappetit</span>\n";
    } else {
        echo "fail'>\n";
        echo "            <strong>✗ Base de Datos:</strong> <span class='error'>No se pudo conectar</span>\n";
    }
} catch (Exception $e) {
    echo "fail'>\n";
    echo "            <strong>✗ Base de Datos:</strong> <span class='error'>" . htmlspecialchars($e->getMessage()) . "</span>\n";
}
echo "        </div>\n";

// Test 4: Extensiones PHP necesarias
$extensiones = ['pdo', 'pdo_mysql', 'openssl', 'mbstring'];
foreach ($extensiones as $ext) {
    echo "        <div class='test ";
    if (extension_loaded($ext)) {
        echo "ok'>\n";
        echo "            <strong>✓ Extensión {$ext}:</strong> <span class='success'>Habilitada</span>\n";
    } else {
        echo "fail'>\n";
        echo "            <strong>✗ Extensión {$ext}:</strong> <span class='error'>No habilitada</span>\n";
    }
    echo "        </div>\n";
}

// Test 5: Archivos de configuración
$archivos = [
    'config/bdconexion.php' => 'Configuración de BD',
    'config/email.php' => 'Configuración de Email',
    'app/services/EmailService.php' => 'Servicio de Email',
    'app/model/Auth.php' => 'Modelo de Autenticación',
    'app/controllers/AuthController.php' => 'Controlador de Auth'
];

foreach ($archivos as $archivo => $descripcion) {
    echo "        <div class='test ";
    if (file_exists(__DIR__ . '/' . $archivo)) {
        echo "ok'>\n";
        echo "            <strong>✓ {$descripcion}:</strong> <span class='success'>Existe</span>\n";
    } else {
        echo "fail'>\n";
        echo "            <strong>✗ {$descripcion}:</strong> <span class='error'>No encontrado</span>\n";
    }
    echo "        </div>\n";
}

// Test 6: Tabla verificar_contrasena
echo "        <div class='test ";
try {
    if (isset($conn)) {
        $stmt = $conn->query("SHOW TABLES LIKE 'verificar_contrasena'");
        if ($stmt->rowCount() > 0) {
            echo "ok'>\n";
            echo "            <strong>✓ Tabla verificar_contrasena:</strong> <span class='success'>Existe en la BD</span>\n";
        } else {
            echo "fail'>\n";
            echo "            <strong>✗ Tabla verificar_contrasena:</strong> <span class='error'>No existe en la BD</span>\n";
        }
    } else {
        echo "fail'>\n";
        echo "            <strong>✗ Tabla verificar_contrasena:</strong> <span class='error'>Sin conexión a BD</span>\n";
    }
} catch (Exception $e) {
    echo "fail'>\n";
    echo "            <strong>✗ Tabla verificar_contrasena:</strong> <span class='error'>" . htmlspecialchars($e->getMessage()) . "</span>\n";
}
echo "        </div>\n";

echo "        <hr>\n";
echo "        <p><strong>Proyecto listo para usar:</strong></p>\n";
echo "        <ul>\n";
echo "            <li><a href='?controller=Auth&action=mostrarLogin'>Ir al Login</a></li>\n";
echo "            <li><a href='?controller=Auth&action=mostrarRegistro'>Ir al Registro</a></li>\n";
echo "            <li><a href='?controller=Auth&action=mostrarRecuperacion'>Recuperar Contraseña</a></li>\n";
echo "        </ul>\n";
echo "    </div>\n";
echo "</body>\n";
echo "</html>\n";
