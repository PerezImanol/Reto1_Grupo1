<?php

use App\Models\HistoricoUbicaciones;

require_once("../model/HistoricoUbicaciones.php");
require_once("../dao/ConexionDB.php");

class HistoricoUbicacionesDAO
{
    //Variable de la conexion.
    private mysqli $conexion;

    //Initializador de la conexion.
    public function __construct()
    {
        $this->conexion = ConexionDB::conexion();
    }

    //Devuelve todos los historiales en la base de datos.
    public function buscarHistoricos(): array
    {
        //Preparacion de query
        $sql = "SELECT * FROM historico_ubicaciones";

        $resultado = $this->conexion->query($sql);
        $historicos = [];

        //Resultados de query
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

    //Busca un historial en concreto por ID en la base de datos y la devuelve.
    public function buscarHistoricoPorId(int $id): ?HistoricoUbicaciones
    {
        //Preparacion de query
        $sql = "SELECT * FROM historico_ubicaciones WHERE id_historico = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        //Resultados de query
        if ($fila = $resultado->fetch_assoc()) {
            $fechaInicio = new DateTime($fila['fecha_inicio']);

            if ($fila['fecha_fin'] !== null) {
                $fechaFin = new DateTime($fila['fecha_fin']);
            } else {
                $fechaFin = null;
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

    //Inserta un nuevo historial a la base de datos.
    public function insertarHistorico(HistoricoUbicaciones $historico): bool
    {
        //Preparacion de query
        $sql = "INSERT INTO historico_ubicaciones
                (id_equipamiento, id_ubicacion, fecha_inicio, fecha_fin)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);
        $idEquipamiento = $historico->getIdEquipamiento();
        $idUbicacion = $historico->getIdUbicacion();
        $fechaInicio = $historico->getFechaInicio()->format('Y-m-d H:i:s');
        if ($historico->getFechaFin() !== null) {
            $fechaFin = $historico->getFechaFin()->format('Y-m-d H:i:s');
        } else {
            $fechaFin = null;
        }

        $stmt->bind_param(
            "iiss",
            $idEquipamiento,
            $idUbicacion,
            $fechaInicio,
            $fechaFin
        );

        //Resultados de query
        return $stmt->execute();
    }

    //Actualiza un historial en la base de datos.
    public function actualizarHistorico(HistoricoUbicaciones $historico): bool
    {
        //Preparacion de query
        $sql = "UPDATE historico_ubicaciones
                SET id_equipamiento = ?,
                    id_ubicacion = ?,
                    fecha_inicio = ?,
                    fecha_fin = ?
                WHERE id_historico = ?";

        $stmt = $this->conexion->prepare($sql);
        $idEquipamiento = $historico->getIdEquipamiento();
        $idUbicacion = $historico->getIdUbicacion();
        $idHistorico = $historico->getIdHistorico();
        $fechaInicio = $historico->getFechaInicio()->format('Y-m-d H:i:s');
        if ($historico->getFechaFin() !== null) {
            $fechaFin = $historico->getFechaFin()->format('Y-m-d H:i:s');
        } else {
            $fechaFin = null;
        }

        $stmt->bind_param(
            "iissi",
            $idEquipamiento,
            $idUbicacion,
            $fechaInicio,
            $fechaFin,
            $idHistorico
        );

        //Resultados de query
        return $stmt->execute();
    }

    // Elimina un historial en la base de datos.
    public function eliminarHistorico(int $id): bool
    {
        //Preparacion de query
        $sql = "DELETE FROM historico_ubicaciones WHERE id_historico = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);

        //Resultados de query
        return $stmt->execute();
    }
}
?>