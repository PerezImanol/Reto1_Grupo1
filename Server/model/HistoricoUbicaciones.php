<?php
namespace App\Models;

use DateTime;

class HistoricoUbicaciones
{
    private int $id_historico;
    private int $id_equipamiento;
    private int $id_ubicacion;
    private DateTime $fecha_inicio;
    private ?DateTime $fecha_fin;

    public function __construct(
        int $id_historico,
        int $id_equipamiento,
        int $id_ubicacion,
        DateTime $fecha_inicio,
        ?DateTime $fecha_fin
    ) {
        $this->id_historico = $id_historico;
        $this->id_equipamiento = $id_equipamiento;
        $this->id_ubicacion = $id_ubicacion;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    public function getIdHistorico(): int
    {
        return $this->id_historico;
    }

    public function setIdHistorico(int $id_historico): self
    {
        $this->id_historico = $id_historico;

        return $this;
    }

    public function getIdEquipamiento(): int
    {
        return $this->id_equipamiento;
    }

    public function setIdEquipamiento(int $id_equipamiento): self
    {
        $this->id_equipamiento = $id_equipamiento;

        return $this;
    }

    public function getIdUbicacion(): int
    {
        return $this->id_ubicacion;
    }

    public function setIdUbicacion(int $id_ubicacion): self
    {
        $this->id_ubicacion = $id_ubicacion;

        return $this;
    }

    public function getFechaInicio(): DateTime
    {
        return $this->fecha_inicio;
    }

    public function setFechaInicio(DateTime $fecha_inicio): self
    {
        $this->fecha_inicio = $fecha_inicio;

        return $this;
    }

    public function getFechaFin(): ?DateTime
    {
        return $this->fecha_fin;
    }

    public function setFechaFin(?DateTime $fecha_fin): self
    {
        $this->fecha_fin = $fecha_fin;

        return $this;
    }
}
