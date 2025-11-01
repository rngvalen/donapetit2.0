<?php
require_once "Model.php";

class Direccion extends Model {
    // Nombre de la tabla
    protected string $table = "direcciones";
    // Clave primaria
    protected string $pk    = "id_direccion";

    /**
     * Crear una nueva dirección
     *
     * @param int $idUsuario    ID del usuario dueño de la dirección
     * @param string $calle     Nombre de la calle
     * @param int $numero       Número de la calle
     * @return int|string       ID insertado o false
     */
    public function crear($idUsuario, $calle, $numero) {
        return $this->insert([
            'id_usuario_direcc' => $idUsuario,
            'nom_calle'         => $calle,
            'num_calle'         => $numero
        ]);
    }

    /**
     * Actualizar dirección
     *
     * @param int $id           ID de la dirección
     * @param int $idUsuario    ID del usuario dueño
     * @param string $calle     Nombre de la calle
     * @param int $numero       Número de la calle
     * @return bool
     */
    public function actualizarDireccion($id, $idUsuario, $calle, $numero) {
        return $this->update($id, [
            'id_usuario_direcc' => $idUsuario,
            'nom_calle'         => $calle,
            'num_calle'         => $numero
        ]);
    }


    /**
     * Buscar todas las direcciones de un usuario
     *
     * @param int $idUsuario
     * @return array
     */
    public function findByUsuario($idUsuario): array {
        $stmt = self::$db->prepare(
            "SELECT * FROM {$this->table} WHERE id_usuario_direcc = ?"
        );
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Eliminar dirección
     *
     * @param int $id
     * @return bool
     */
    public function eliminarDireccion($id): bool {
        return $this->delete($id);
    }
}
