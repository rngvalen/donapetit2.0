<?php
require_once "Model.php";

/**
 * Clase Donante
 * Maneja operaciones sobre la tabla `donante`
 */
class Donante extends Model {
    protected string $table = "donante";
    protected string $pk    = "id_usu_donante";

    /** comentarios 
     * Crear un nuevo donante
     *
     * @param int $idUsuario ID del usuario (referencia a tabla usuarios)
     * @param string $nombreComercial Nombre del comercio/supermercado
     * @param string $cuit CUIT del donante
     * @return bool|int Devuelve el ID insertado o false si falla
     */
    public function crear($idUsuario, $nombreComercial, $cuit) {
        return $this->insert([
            'id_usu_donante' => $idUsuario,
            'nom_comercial'  => $nombreComercial,
            'CUIT'           => $cuit
        ]);
    }

    /**
     * Actualizar datos de un donante
     *
     * @param int $idUsuario ID del donante
     * @param string $nombreComercial Nuevo nombre comercial
     * @param string $cuit Nuevo CUIT
     * @return bool true si se actualizó, false si no
     */
    public function actualizarDonante($idUsuario, $nombreComercial, $cuit) {
        return $this->update($idUsuario, [
            'nom_comercial' => $nombreComercial,
            'CUIT'          => $cuit
        ]);
    }

    /**
     * Buscar un donante por CUIT
     *
     * @param string $cuit CUIT a buscar
     * @return array|null Registro del donante o null si no existe
     */
    public function buscarPorCUIT($cuit) {
        $stmt = self::$db->prepare("SELECT * FROM {$this->table} WHERE CUIT = ?");
        $stmt->execute([$cuit]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Eliminar donante por ID de usuario
     *
     * @param int $idUsuario
     * @return bool
     */
    public function eliminarDonante($idUsuario) {
        return $this->delete($idUsuario);
    }
}
