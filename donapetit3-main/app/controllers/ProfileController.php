<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../../config/bdconexion.php';

class Profilecontroller extends Controller
{
    /**
     * Muestra el formulario para completar perfil según el rol
     */
    public function completarPerfil(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        $userName = $_SESSION['user']['name'];
        $userRole = $_SESSION['user']['rol'] ?? 'donante';

        if ($userRole === 'donante') {
            $this->render('profile.completar_donante', compact('userName'));
        } else {
            $this->render('profile.completar_receptor', compact('userName'));
        }
    }

    /**
     * Guarda los datos adicionales del donante + dirección
     */
    public function guardarDonante(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user'])) {
            header('Location: ?controller=Home&action=index');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $nombreComercial = trim($_POST['nombre_comercial'] ?? '');
        $cuit = trim($_POST['cuit'] ?? '');
        $nombreCalle = trim($_POST['nombre_calle'] ?? '');
        $numCalle = trim($_POST['num_calle'] ?? '');
        $latitud = trim($_POST['latitud'] ?? '');
        $longitud = trim($_POST['longitud'] ?? '');

        // Validación
        if ($nombreComercial === '' || $cuit === '' || $nombreCalle === '' || $numCalle === '') {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: ?controller=Profile&action=completarPerfil');
            exit;
        }

        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Iniciar transacción
            $conn->beginTransaction();

            // 1. Insertar en tabla donante
            $sql = "INSERT INTO donante (id_usu_donante, nom_comercial, CUIT) 
                    VALUES (:id, :nombre, :cuit)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $userId);
            $stmt->bindParam(':nombre', $nombreComercial);
            $stmt->bindParam(':cuit', $cuit);
            $stmt->execute();

            // 2. Insertar en tabla direcciones
            $sqlDir = "INSERT INTO direcciones (id_usuario_direcc, nom_calle, num_calle, Latitud, Longitud) 
                       VALUES (:id, :calle, :numero, :lat, :lng)";
            $stmtDir = $conn->prepare($sqlDir);
            $stmtDir->bindParam(':id', $userId);
            $stmtDir->bindParam(':calle', $nombreCalle);
            $stmtDir->bindParam(':numero', $numCalle);
            $stmtDir->bindParam(':lat', $latitud);
            $stmtDir->bindParam(':lng', $longitud);
            $stmtDir->execute();

            // Confirmar transacción
            $conn->commit();

            $_SESSION['success'] = 'Perfil completado exitosamente.';
            header('Location: ?controller=Home&action=index');
            exit;

        } catch (\PDOException $e) {
            // Revertir en caso de error
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Error al guardar donante: " . $e->getMessage());
            $_SESSION['error'] = 'Error al guardar el perfil.';
            header('Location: ?controller=Profile&action=completarPerfil');
            exit;
        }
    }

    /**
     * Guarda los datos adicionales del receptor + dirección
     */
    public function guardarReceptor(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user'])) {
            header('Location: ?controller=Home&action=index');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $numRenacom = trim($_POST['num_renacom'] ?? '');
        $nombreInstitucion = trim($_POST['nom_institucion'] ?? '');
        $responsable = trim($_POST['responsable'] ?? '');
        $nombreCalle = trim($_POST['nombre_calle'] ?? '');
        $numCalle = trim($_POST['num_calle'] ?? '');
        $latitud = trim($_POST['latitud'] ?? '');
        $longitud = trim($_POST['longitud'] ?? '');

        // Validación
        if ($numRenacom === '' || $nombreInstitucion === '' || $responsable === '' || $nombreCalle === '' || $numCalle === '') {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: ?controller=Profile&action=completarPerfil');
            exit;
        }

        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Iniciar transacción
            $conn->beginTransaction();

            // 1. Insertar en tabla receptor
            $sql = "INSERT INTO receptor (id_usu_receptor, num_renacom, nom_institucion, responsable) 
                    VALUES (:id, :renacom, :institucion, :responsable)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $userId);
            $stmt->bindParam(':renacom', $numRenacom);
            $stmt->bindParam(':institucion', $nombreInstitucion);
            $stmt->bindParam(':responsable', $responsable);
            $stmt->execute();

            // 2. Insertar en tabla direcciones
            $sqlDir = "INSERT INTO direcciones (id_usuario_direcc, nom_calle, num_calle, Latitud, Longitud) 
                       VALUES (:id, :calle, :numero, :lat, :lng)";
            $stmtDir = $conn->prepare($sqlDir);
            $stmtDir->bindParam(':id', $userId);
            $stmtDir->bindParam(':calle', $nombreCalle);
            $stmtDir->bindParam(':numero', $numCalle);
            $stmtDir->bindParam(':lat', $latitud);
            $stmtDir->bindParam(':lng', $longitud);
            $stmtDir->execute();

            // Confirmar transacción
            $conn->commit();

            $_SESSION['success'] = 'Perfil completado exitosamente.';
            header('Location: ?controller=Home&action=index');
            exit;

        } catch (\PDOException $e) {
            // Revertir en caso de error
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Error al guardar receptor: " . $e->getMessage());
            $_SESSION['error'] = 'Error al guardar el perfil.';
            header('Location: ?controller=Profile&action=completarPerfil');
            exit;
        }
    }
}