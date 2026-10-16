<?php

use App\Models\HistoricoUbicaciones;

require_once("../dao/HistoricoUbicacionDAO.php");
require_once("../model/HistoricoUbicaciones.php");

class HistoricoUbicacionesController
{
    private HistoricoUbicacionesDAO $dao;

    public function __construct()
    {
        $this->dao = new HistoricoUbicacionesDAO();
    }

    // Devuelve todos los registros del histórico de ubicaciones.
    public function listar(): array
    {
        return $this->dao->buscarHistoricos();
    }

    // Busca un registro del histórico por su ID.
    public function buscarPorId(int $id): ?HistoricoUbicaciones
    {
        return $this->dao->buscarHistoricoPorId($id);
    }

    // Registra una nueva ubicación para un equipamiento.
    public function insertar(
        int $idEquipamiento,
        int $idUbicacion,
        DateTime $fechaInicio,
        ?DateTime $fechaFin
    ): bool {
        $historico = new HistoricoUbicaciones(
            0,
            $idEquipamiento,
            $idUbicacion,
            $fechaInicio,
            $fechaFin
        );

        return $this->dao->insertarHistorico($historico);
    }

    // Actualiza los datos de un registro del histórico.
    public function actualizar(
        int $idHistorico,
        int $idEquipamiento,
        int $idUbicacion,
        DateTime $fechaInicio,
        ?DateTime $fechaFin
    ): bool {
        $historico = new HistoricoUbicaciones(
            $idHistorico,
            $idEquipamiento,
            $idUbicacion,
            $fechaInicio,
            $fechaFin
        );

        return $this->dao->actualizarHistorico($historico);
    }

    // Elimina un registro del histórico por su ID.
    public function eliminar(int $id): bool
    {
        return $this->dao->eliminarHistorico($id);
    }
}
