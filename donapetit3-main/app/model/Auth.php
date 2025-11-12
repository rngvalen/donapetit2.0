<?php
require_once __DIR__ . '/../../config/bdconexion.php';

class Auth {
    private $conn;
    private $lastError = null;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getLastError() {
        return $this->lastError;
    }

    // Registrar nuevo usuario con latitud y longitud
    public function registrarUsuario($nombre, $email, $contrasena, $rol = 'donante', $telefono = null, $latitud = null, $longitud = null) {
        $this->lastError = null;
        try {
            // Verificar si el email ya existe
            $check = $this->conn->prepare("SELECT * FROM usuarios WHERE Email = :email");
            $check->bindParam(':email', $email);
            $check->execute();
            if ($check->rowCount() > 0) {
                $this->lastError = 'El email ya esta registrado.';
                return false;
            }

            // Hashear contrasena
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Insertar nuevo usuario con ubicacion
            $sql = "INSERT INTO usuarios (Nombre, Email, `contrasena`, rol, telefono, activo, Latitud, Longitud)
                    VALUES (:nombre, :email, :contrasena, :rol, :telefono, 1, :latitud, :longitud)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contrasena', $hash);
            $stmt->bindParam(':rol', $rol);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':latitud', $latitud);
            $stmt->bindParam(':longitud', $longitud);

            if ($stmt->execute()) {
                return true;
            }

            $this->lastError = 'No se pudo registrar el usuario.';
            return false;
        } catch (\PDOException $e) {
            $this->lastError = 'Error al registrar: ' . $e->getMessage();
            return false;
        }
    }

    // Buscar usuario por email
    public function usuarioPorEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Verificar credenciales de inicio de sesion
    public function login($email, $password) {
        $this->lastError = null;
        try {
            $usuario = $this->usuarioPorEmail($email);

            if (!$usuario) {
                $this->lastError = 'Email o contrasena incorrectos.';
                return false;
            }

            if (!password_verify($password, $usuario['contrasena'])) {
                $this->lastError = 'Email o contrasena incorrectos.';
                return false;
            }

            return $usuario;
        } catch (\PDOException $e) {
            $this->lastError = 'Error en login: ' . $e->getMessage();
            return false;
        }
    }

    // Generar código de recuperación
    public function generarCodigoRecuperacion($email) {
        $this->lastError = null;
        try {
            $usuario = $this->usuarioPorEmail($email);
            
            if (!$usuario) {
                $this->lastError = 'No existe una cuenta con ese email.';
                return false;
            }
            
            // Generar código aleatorio de 6 dígitos
            $codigo = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            // Expiración: 15 minutos desde ahora
            $expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            
            // Desactivar códigos anteriores del usuario
            $delete = $this->conn->prepare("UPDATE verificar_contrasena SET activo = '0' WHERE id_usuario = :id_usuario");
            $delete->bindParam(':id_usuario', $usuario['id_usuario']);
            $delete->execute();

            // Insertar nuevo código
            $sql = "INSERT INTO verificar_contrasena (id_usuario, codigo, fecha_expiracion, activo)
                    VALUES (:id_usuario, :codigo, :expiracion, '1')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_usuario', $usuario['id_usuario']);
            $stmt->bindParam(':codigo', $codigo);
            $stmt->bindParam(':expiracion', $expiracion);
            
            if ($stmt->execute()) {
                return [
                    'codigo' => $codigo,
                    'email' => $usuario['Email'],
                    'nombre' => $usuario['Nombre']
                ];
            }
            
            $this->lastError = 'No se pudo generar el código.';
            return false;
        } catch (\PDOException $e) {
            $this->lastError = 'Error al generar código: ' . $e->getMessage();
            return false;
        }
    }

    // Verificar código de recuperación
    public function verificarCodigoRecuperacion($email, $codigo) {
        $this->lastError = null;
        try {
            $usuario = $this->usuarioPorEmail($email);
            
            if (!$usuario) {
                $this->lastError = 'Usuario no encontrado.';
                return false;
            }
            
            $sql = "SELECT * FROM verificar_contrasena
                    WHERE id_usuario = :id_usuario
                    AND codigo = :codigo
                    AND activo = '1'
                    AND fecha_expiracion > NOW()";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_usuario', $usuario['id_usuario']);
            $stmt->bindParam(':codigo', $codigo);
            $stmt->execute();
            
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$resultado) {
                $this->lastError = 'Código inválido o expirado.';
                return false;
            }

            return $resultado;
        } catch (\PDOException $e) {
            $this->lastError = 'Error al verificar código: ' . $e->getMessage();
            return false;
        }
    }

    // Cambiar contraseña con código
    public function cambiarContrasenaConCodigo($email, $codigo, $nuevaContrasena) {
        $this->lastError = null;
        try {
            $usuario = $this->usuarioPorEmail($email);
            
            if (!$usuario) {
                $this->lastError = 'Usuario no encontrado.';
                return false;
            }
            
            // Verificar código válido
            $codigoValido = $this->verificarCodigoRecuperacion($email, $codigo);
            
            if (!$codigoValido) {
                return false;
            }
            
            // Hashear nueva contraseña
            $hash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
            
            // Actualizar contraseña
            $sql = "UPDATE usuarios SET contrasena = :contrasena WHERE id_usuario = :id_usuario";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':contrasena', $hash);
            $stmt->bindParam(':id_usuario', $usuario['id_usuario']);
            
            if ($stmt->execute()) {
                // Marcar código como usado (desactivar)
                $update = $this->conn->prepare("UPDATE verificar_contrasena SET activo = '0' WHERE id_cod = :id");
                $update->bindParam(':id', $codigoValido['id_cod']);
                $update->execute();

                return true;
            }
            
            $this->lastError = 'No se pudo cambiar la contraseña.';
            return false;
        } catch (\PDOException $e) {
            $this->lastError = 'Error al cambiar contraseña: ' . $e->getMessage();
            return false;
        }
    }
}