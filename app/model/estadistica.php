<?php
require_once "Model.php";

class Estadistica extends Model {
    // Nombre de la tabla
    protected string $table = "estadisticas";
    // Clave primaria
    protected string $pk    = "id_estadistica";

    /**
     * Crear una estadística 
     *
     * @param int $idDonacion
     * @param int $total
     * @param int $mensual
     * @param int $anual
     * @return int|string  ID insertado o false                          
     */
    public function crear($idDonacion, $total, $mensual, $anual) {                       //Ver si sacar esto.
        return $this->insert([
            'id_donacion'       => $idDonacion,
            'total_donado'      => $total,
            'frecuencia_mensual'=> $mensual,
            'frecuencia_anual'  => $anual
        ]);
    }

    /**
     * Actualizar una estadística
     *
     * @param int $id
     * @param int $idDonacion
     * @param int $total
     * @param int $mensual
     * @param int $anual
     * @return bool
     */
    public function actualizarEstadistica($id, $idDonacion, $total, $mensual, $anual) {
        return $this->update($id, [
            'id_donacion'       => $idDonacion,
            'total_donado'      => $total,
            'frecuencia_mensual'=> $mensual,
            'frecuencia_anual'  => $anual
        ]);
    }
    /**
     * Buscar estadísticas por donación
     *
     * @param int $idDonacion
     * @return array
     */
    public function encontrarPorDonacion($idDonacion): array {
        $stmt = self::$db->prepare(
            "SELECT * FROM {$this->table} WHERE id_donacion = ?"
        );
        $stmt->execute([$idDonacion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Eliminar estadística
     *
     * @param int $id
     * @return bool
     */
    public function eliminarEstadistica($id): bool {
        return $this->delete($id);
    }
}
