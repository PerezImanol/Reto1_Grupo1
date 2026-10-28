<?php
namespace App\Models;
use App\Enums\EnumCategoria;
class Equipamiento
{
    private int $id_equipamiento;
    private string $nombre;
    private string $descripcion;
    private string $marca;
    private string $modelo;
    private EnumCategoria $categoria; //Cambiar mas adelante a Enum cuando se haga el enumerado
    private int $id_ubicacion_actual;

    public function __construct(
        int $id_equipamiento,
        string $nombre,
        string $descripcion,
        string $marca,
        string $modelo,
        EnumCategoria $categoria,
        int $id_ubicacion_actual
    ) {
        $this->id_equipamiento = $id_equipamiento;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->categoria = $categoria;
        $this->id_ubicacion_actual = $id_ubicacion_actual;
    }

    /**
     * Get the value of id_equipamiento
     */
    public function getIdEquipamiento(): int
    {
        return $this->id_equipamiento;
    }

    /**
     * Set the value of id_equipamiento
     */
    public function setIdEquipamiento(int $id_equipamiento): self
    {
        $this->id_equipamiento = $id_equipamiento;

        return $this;
    }

    /**
     * Get the value of nombre
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     */
    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get the value of descripcion
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * Set the value of descripcion
     */
    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get the value of marca
     */
    public function getMarca(): string
    {
        return $this->marca;
    }

    /**
     * Set the value of marca
     */
    public function setMarca(string $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get the value of modelo
     */
    public function getModelo(): string
    {
        return $this->modelo;
    }

    /**
     * Set the value of modelo
     */
    public function setModelo(string $modelo): self
    {
        $this->modelo = $modelo;

        return $this;
    }

    /**
     * Get the value of categoria
     */
    public function getCategoria(): EnumCategoria
    {
        return $this->categoria;
    }

    /**
     * Set the value of categoria
     */
    public function setCategoria(EnumCategoria $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get the value of id_ubicacion_actual
     */
    public function getIdUbicacionActual(): int
    {
        return $this->id_ubicacion_actual;
    }

    /**
     * Set the value of id_ubicacion_actual
     */
    public function setIdUbicacionActual(int $id_ubicacion_actual): self
    {
        $this->id_ubicacion_actual = $id_ubicacion_actual;

        return $this;
    }
}
