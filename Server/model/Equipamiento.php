<?php
namespace App\Models;

use App\Enums\EnumCategoria;
use JsonSerializable;

class Equipamiento implements JsonSerializable
{
    //El ID del equipamiento.
    private int $id_equipamiento;

    //El nombre del equipamiento.
    private string $nombre;

    //La descripcion del equipamiento.
    private string $descripcion;

    //La marca del equipamiento.
    private string $marca;

    //El modelo del equipamiento.
    private string $modelo;

    //La categoria del equipamiento.
    private EnumCategoria $categoria;

    //El ID de la ubicacion actual.
    private int $id_ubicacion_actual;

    //El constructor de clase.
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

    //Metodo implementado de JsonSerializable, necesario para poder usar postman y el enrutador.
    public function jsonSerialize(): mixed
    {
        return [
            'id_equipamiento' => $this->id_equipamiento,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'categoria' => isset($this->categoria->value) ? $this->categoria->value : $this->categoria->name,
            'id_ubicacion_actual' => $this->id_ubicacion_actual
        ];
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
     * Devuelve el nombre del equipamiento.
     * 
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * Cambia el nombre del equipamiento.
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
     * Devuelve la descripcion del equipamiento.
     *
     * @return string
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * Cambia la descripcion del equipamiento.
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

    /**
     * Devuelve la marca del equipamiento.
     * 
     * @return string
     */
    public function getMarca(): string
    {
        return $this->marca;
    }

    /**
     * Cambia la marca del equipamiento.
     * 
     * @param string $marca
     *
     * @return self
     */
    public function setMarca(string $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Devuelve el modelo del equipamiento.
     * 
     * @return string
     */
    public function getModelo(): string
    {
        return $this->modelo;
    }

    /**
     * Cambia el modelo del equipamiento.
     * 
     * @param string $modelo
     *
     * @return self
     */
    public function setModelo(string $modelo): self
    {
        $this->modelo = $modelo;

        return $this;
    }

    /**
     * Devuelve la categora del equipamiento.
     * 
     * @return EnumCategoria
     */
    public function getCategoria(): EnumCategoria
    {
        return $this->categoria;
    }

    /**
     * Devuelve la categoria del equipamiento.
     * 
     * @param EnumCategoria $categoria
     *
     * @return self
     */
    public function setCategoria(EnumCategoria $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Devuelve la ID de la ubicacion actual del equipamiento.
     * 
     * @return int
     */
    public function getIdUbicacionActual(): int
    {
        return $this->id_ubicacion_actual;
    }

    /**
     * Devuelve la ID de la ubicacion actual del equipamiento.
     * 
     * @param int $id_ubicacion_actual
     *
     * @return self
     */
    public function setIdUbicacionActual(int $id_ubicacion_actual): self
    {
        $this->id_ubicacion_actual = $id_ubicacion_actual;

        return $this;
    }
}