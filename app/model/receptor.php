<?php
require_once "Model.php";

/**
 * Modelo Receptor para la tabla 'receptores'.
 * Proporciona métodos CRUD específicos para la entidad receptor.
 */
class Receptor extends Model {
    /**
     * Nombre de la tabla asociada al modelo.
     * @var string
     */
    protected string $table = "receptores";

    /**
     * Nombre de la clave primaria de la tabla.
     * @var string
     */
    protected string $pk    = "id_usu_receptor";

    /**
     * Crea un nuevo receptor en la base de datos.
     * 
     * @param string $renacom      Número RENACOM del receptor.
     * @param string $institucion  Nombre de la institución.
     * @param string $responsable  Nombre del responsable.
     * @return string              ID del nuevo registro insertado.
     */
    public function crear($renacom, $institucion, $responsable) {
        return $this->insert([
            'num_renacom'    => $renacom,
            'nom_institucion'=> $institucion,
            'responsable'    => $responsable
        ]);
    }

    /**
     * Actualiza los datos de un receptor existente.
     * 
     * @param mixed  $id           ID del receptor a actualizar.
     * @param string $renacom      Nuevo número RENACOM.
     * @param string $institucion  Nuevo nombre de la institución.
     * @param string $responsable  Nuevo responsable.
     * @return bool                true si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarReceptor($id, $renacom, $institucion, $responsable) { 
        return $this->update($id, [
            'num_renacom'    => $renacom,
            'nom_institucion'=> $institucion,
            'responsable'    => $responsable
        ]);
    }

    /**
     * Busca un receptor por su número RENACOM.
     * 
     * @param mixed $renacom  Número RENACOM a buscar.
     * @return array|null     Array asociativo con los datos del receptor o null si no existe.
     */
    public function encontrarPorId($renacom): array|null {
        return $this->find($renacom); // devuelve array|null
    }

    /**
     * Busca receptores por nombre de institución (búsqueda parcial).
     * 
     * @param string $institucion  Nombre (o parte) de la institución a buscar.
     * @return array               Lista de receptores encontrados como arrays asociativos.
     */
    public function encontrarPorInst(string $institucion): array {
        $stmt = self::$db->prepare(
            "SELECT * FROM {$this->table} WHERE nom_institucion LIKE ?"
        );
        $stmt->execute(["%" . $institucion . "%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina un receptor por su ID.
     * 
     * @param mixed $id  ID del receptor a eliminar.
     * @return bool      true si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminarReceptor($id): bool {
        return $this->delete($id);
    }
}

