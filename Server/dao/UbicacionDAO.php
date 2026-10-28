<?php

use App\Models\Ubicacion;

require_once("../model/Ubicacion.php");
require_once("../dao/ConexionDB.php");

class UbicacionDAO
{
    private mysqli $conexion;

    public function __construct()
    {
        $this->conexion = ConexionDB::conexion();
    }

    public function buscarUbicaciones(): array
    {
        $ubicaciones = array();

        $sql = "SELECT * FROM ubicaciones";

        $resultado = $this->conexion->query($sql);

        while ($fila = $resultado->fetch_assoc()) {
            $ubicacion = new Ubicacion(
                $fila['id_ubicacion'],
                $fila['nombre'],
                $fila['descripcion']
            );

            $ubicaciones[] = $ubicacion;
        }

        return $ubicaciones;
    }

    public function buscarUbicacionPorId(int $id): ?Ubicacion
    {
        $sql = "SELECT * FROM ubicaciones WHERE id_ubicacion = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($fila = $resultado->fetch_assoc()) {
            return new Ubicacion(
                $fila['id_ubicacion'],
                $fila['nombre'],
                $fila['descripcion']
            );
        }

        return null;
    }

    public function insertarUbicacion(Ubicacion $ubicacion): bool
    {
        $sql = "INSERT INTO ubicaciones (nombre, descripcion) VALUES (?, ?)";

        $stmt = $this->conexion->prepare($sql);

        $nombre = $ubicacion->getNombre();
        $descripcion = $ubicacion->getDescripcion();

        $stmt->bind_param("ss", $nombre, $descripcion);

        return $stmt->execute();
    }

    public function actualizarUbicacion(Ubicacion $ubicacion): bool
    {
        $sql = "UPDATE ubicaciones 
                SET nombre = ?, descripcion = ?
                WHERE id_ubicacion = ?";

        $stmt = $this->conexion->prepare($sql);

        $nombre = $ubicacion->getNombre();
        $descripcion = $ubicacion->getDescripcion();
        $id = $ubicacion->getIdUbicacion();

        $stmt->bind_param("ssi", $nombre, $descripcion, $id);

        return $stmt->execute();
    }

    public function eliminarUbicacion(int $id): bool
    {
        $sql = "DELETE FROM ubicaciones WHERE id_ubicacion = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}

?>