<?php
namespace App\Models;

use JsonSerializable;

class Ubicacion implements JsonSerializable
{
    //El ID de la ubicacion.
    private int $id_ubicacion;

    //El nombre de la ubicacion.
    private string $nombre;

    //La descripcion de la ubicacion.
    private string $descripcion;

    //El constructor de clase.
    public function __construct(
        int $id_ubicacion,
        string $nombre,
        string $descripcion
    ) {
        $this->id_ubicacion = $id_ubicacion;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
    }

    //Metodo implementado de JsonSerializable, necesario para poder usar postman y el enrutador.
    public function jsonSerialize(): mixed
    {
        return [
            'id_ubicacion' => $this->id_ubicacion,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion
        ];
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
     *
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
     * Devuelve el nombre de la ubicacion.
     *
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * Cambia el nombre de la ubicacion.
     *
     * @param string $nombre
     *
     * @return self
     */
    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Devuelve la descripcion de la ubicacion.
     *
     * @return string
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * Cambia la descripcion de la ubicacion.
     *
     * @param string $descripcion
     *
     * @return self
     */
    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;
        return $this;
    }
}
