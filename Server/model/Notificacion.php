<?php

require_once "Estado.php";
class Notificacion
{
    private int $id_notificacion;
    private string $titulo;
    private string $descripcion;
    private DateTime $fecha_creacion;
    private Estado $estado;
    private int $id_user;


    public function __construct(
        int $id_notificacion,
        string $titulo,
        string $descripcion,
        DateTime $fecha_creacion,
        Estado $estado,
        int $id_user
    ) {
        $this->id_notificacion = $id_notificacion;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->fecha_creacion = $fecha_creacion;
        $this->estado = $estado;
        $this->id_user = $id_user;
    }

    /**
     * Get the value of id_notificacion
     *
     * @return int
     */
    public function getIdNotificacion(): int
    {
        return $this->id_notificacion;
    }

    /**
     * Set the value of id_notificacion
     *
     * @param int $id_notificacion
     *
     * @return self
     */
    public function setIdNotificacion(int $id_notificacion): self
    {
        $this->id_notificacion = $id_notificacion;
        return $this;
    }

    /**
     * Get the value of titulo
     *
     * @return string
     */
    public function getTitulo(): string
    {
        return $this->titulo;
    }

    /**
     * Set the value of titulo
     *
     * @param string $titulo
     *
     * @return self
     */
    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;
        return $this;
    }

    /**
     * Get the value of descripcion
     *
     * @return string
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * Set the value of descripcion
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
     * Get the value of fecha_creacion
     *
     * @return DateTime
     */
    public function getFechaCreacion(): DateTime
    {
        return $this->fecha_creacion;
    }

    /**
     * Set the value of fecha_creacion
     *
     * @param DateTime $fecha_creacion
     *
     * @return self
     */
    public function setFechaCreacion(DateTime $fecha_creacion): self
    {
        $this->fecha_creacion = $fecha_creacion;
        return $this;
    }

    /**
     * Get the value of estado
     *
     * @return Estado
     */
    public function getEstado(): Estado
    {
        return $this->estado;
    }

    /**
     * Set the value of estado
     *
     * @param Estado $estado
     *
     * @return self
     */
    public function setEstado(Estado $estado): self
    {
        $this->estado = $estado;
        return $this;
    }

    /**
     * Get the value of id_user
     *
     * @return int
     */
    public function getIdUser(): int
    {
        return $this->id_user;
    }

    /**
     * Set the value of id_user
     *
     * @param int $id_user
     *
     * @return self
     */
    public function setIdUser(int $id_user): self
    {
        $this->id_user = $id_user;
        return $this;
    }
}

?>