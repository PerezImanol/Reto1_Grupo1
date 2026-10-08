<?php

use App\Models\HistoricoUbicaciones;

require_once("model/HistoricoUbicaciones.php");
require_once("dao/ConexionDB.php");

class HistoricoUbicacionesDAO
{
    private mysqli $conexion;

    public function __construct()
    {
        $this->conexion = ConexionDB::conexion();
    }

    public function buscarHistoricos(): array
    {
        $historicos = array();
        $sql = "SELECT * FROM historico_ubicaciones";
        $resultado = $this->conexion->query($sql);
        while ($fila = $resultado->fetch_assoc()) {
            $fechaInicio = new DateTime($fila['fecha_inicio']);
            $fechaFin = null;
            if ($fila['fecha_fin'] !== null) {
                $fechaFin = new DateTime($fila['fecha_fin']);
            }
            $historico = new HistoricoUbicaciones(
                $fila['id_historico'],
                $fila['id_equipamiento'],
                $fila['id_ubicacion'],
                $fechaInicio,
                $fechaFin
            );
            $historicos[] = $historico;
        }
        return $historicos;
    }

    public function buscarHistoricoPorId(int $id): ?HistoricoUbicaciones
    {
        $sql = "SELECT * FROM historico_ubicaciones WHERE id_historico = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($fila = $resultado->fetch_assoc()) {

            $fechaInicio = new DateTime($fila['fecha_inicio']);

            $fechaFin = null;

            if ($fila['fecha_fin'] !== null) {
                $fechaFin = new DateTime($fila['fecha_fin']);
            }

            return new HistoricoUbicaciones(
                $fila['id_historico'],
                $fila['id_equipamiento'],
                $fila['id_ubicacion'],
                $fechaInicio,
                $fechaFin
            );
        }

        return null;
    }

    public function insertarHistorico(HistoricoUbicaciones $historico): bool
    {
        $sql = "INSERT INTO historico_ubicaciones
                (id_equipamiento, id_ubicacion, fecha_inicio, fecha_fin)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        $idEquipamiento = $historico->getIdEquipamiento();
        $idUbicacion = $historico->getIdUbicacion();
        $fechaInicio = $historico->getFechaInicio()->format('Y-m-d H:i:s');

        $fechaFin = null;

        if ($historico->getFechaFin() !== null) {
            $fechaFin = $historico->getFechaFin()->format('Y-m-d H:i:s');
        }

        $stmt->bind_param(
            "iiss",
            $idEquipamiento,
            $idUbicacion,
            $fechaInicio,
            $fechaFin
        );

        return $stmt->execute();
    }

    public function actualizarHistorico(HistoricoUbicaciones $historico): bool
    {
        $sql = "UPDATE historico_ubicaciones
                SET id_equipamiento = ?,
                    id_ubicacion = ?,
                    fecha_inicio = ?,
                    fecha_fin = ?
                WHERE id_historico = ?";

        $stmt = $this->conexion->prepare($sql);

        $idEquipamiento = $historico->getIdEquipamiento();
        $idUbicacion = $historico->getIdUbicacion();
        $fechaInicio = $historico->getFechaInicio()->format('Y-m-d H:i:s');

        $fechaFin = null;

        if ($historico->getFechaFin() !== null) {
            $fechaFin = $historico->getFechaFin()->format('Y-m-d H:i:s');
        }

        $idHistorico = $historico->getIdHistorico();

        $stmt->bind_param(
            "iissi",
            $idEquipamiento,
            $idUbicacion,
            $fechaInicio,
            $fechaFin,
            $idHistorico
        );

        return $stmt->execute();
    }

    public function eliminarHistorico(int $id): bool
    {
        $sql = "DELETE FROM historico_ubicaciones WHERE id_historico = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

?>