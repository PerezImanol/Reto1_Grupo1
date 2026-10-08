<?php
namespace App\Models;

class Ubicacion
{
    private int $id_ubicacion;
    private string $nombre;
    private string $descripcion;

    public function __construct(
        int $id_ubicacion,
        string $nombre,
        string $descripcion
    ) {
        $this->id_ubicacion = $id_ubicacion;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
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

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;
        return $this;
    }
}
