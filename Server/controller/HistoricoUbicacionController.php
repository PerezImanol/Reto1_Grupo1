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

    public function listar(): array
    {
        return $this->dao->buscarHistoricos();
    }

    public function buscarPorId(int $id): ?HistoricoUbicaciones
    {
        return $this->dao->buscarHistoricoPorId($id);
    }

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

    public function eliminar(int $id): bool
    {
        return $this->dao->eliminarHistorico($id);
    }
}
