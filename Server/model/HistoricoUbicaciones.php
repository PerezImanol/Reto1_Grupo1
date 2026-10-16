<?php
namespace App\Models;

use DateTime;

use JsonSerializable;
use Override;

class HistoricoUbicaciones implements JsonSerializable
{
    //El ID del historial.
    private int $id_historico;

    //El ID del equipamiento.
    private int $id_equipamiento;

    //El ID de la ubicacion.
    private int $id_ubicacion;

    //La fecha de inicio.
    private DateTime $fecha_inicio;

    //La fecha de fin.
    private ?DateTime $fecha_fin;

    //El constructor de clase.
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

    //Metodo implementado de JsonSerializable, necesario para poder usar postman y el enrutador.
    public function jsonSerialize(): mixed
    {
        return [
            'id_historico' => $this->id_historico,
            'id_equipamiento' => $this->id_equipamiento,
            'id_ubicacion' => $this->id_ubicacion,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
        ];
    }

    /**
     * Devuelve el ID del historial.
     * 
     * @return int
     */
    public function getIdHistorico(): int
    {
        return $this->id_historico;
    }

    /**
     * Cambia el ID del historial.
     * @param int $id_historico
     *
     * @return self
     */
    public function setIdHistorico(int $id_historico): self
    {
        $this->id_historico = $id_historico;

        return $this;
    }

    /**
     * Devuelve el ID del equipamiento.
     * 
     * @return int
     */
    public function getIdEquipamiento(): int
    {
        return $this->id_equipamiento;
    }

    /**
     * Cambia el ID del equipamiento.
     * @param int $id_equipamiento
     *
     * @return self
     */
    public function setIdEquipamiento(int $id_equipamiento): self
    {
        $this->id_equipamiento = $id_equipamiento;

        return $this;
    }

    /**
     * Devuelve el ID de la ubicacion.
     * 
     * @return int
     */
    public function getIdUbicacion(): int
    {
        return $this->id_ubicacion;
    }

    /**
     * Cambia el ID de la ubicacion.
     * @param int $id_ubicacion
     *
     * @return self
     */
    public function setIdUbicacion(int $id_ubicacion): self
    {
        $this->id_ubicacion = $id_ubicacion;

        return $this;
    }

    /**
     * Devuelve la fecha de inicio.
     * 
     * @return DateTime
     */
    public function getFechaInicio(): DateTime
    {
        return $this->fecha_inicio;
    }

    /**
     * Cambia la fecha de inicio.
     * @param DateTime $fecha_inicio
     *
     * @return self
     */
    public function setFechaInicio(DateTime $fecha_inicio): self
    {
        $this->fecha_inicio = $fecha_inicio;

        return $this;
    }

    /**
     * Devuelve la fecha de fin.
     * 
     * @return DateTime
     */
    public function getFechaFin(): ?DateTime
    {
        return $this->fecha_fin;
    }

    /**
     * Cambia la fecha de fin.
     * @param DateTime $fecha_fin
     *
     * @return self
     */
    public function setFechaFin(?DateTime $fecha_fin): self
    {
        $this->fecha_fin = $fecha_fin;

        return $this;
    }
}
