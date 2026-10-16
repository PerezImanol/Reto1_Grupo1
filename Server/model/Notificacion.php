<?php

require_once "Estado.php";
class Notificacion implements JsonSerializable
{
    //El ID de la notificacion.
    private int $id_notificacion;

    //El titulo de la notificacion.
    private string $titulo;

    //La descripcion de la notificacion.
    private string $descripcion;

    //La fecha de la notificacion.
    private DateTime $fecha_creacion;

    //El estado de la notificacion.
    private Estado $estado;

    //El ID del usuario.
    private int $id_user;

    //El constructor de clase.
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

    //Metodo implementado de JsonSerializable, necesario para poder usar postman y el enrutador.
    public function jsonSerialize(): mixed
    {
        return [
            'id_notificacion' => $this->id_notificacion,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'fecha_creacion' => $this->fecha_creacion,
            'estado' => isset($this->estado->value) ? $this->estado->value : $this->estado->name,
            'id_user' => $this->id_user
        ];
    }

    /**
     * Devuelve el ID de la notificacion.
     *
     * @return int
     */
    public function getIdNotificacion(): int
    {
        return $this->id_notificacion;
    }

    /**
     * Cambia el ID de la notificacion.
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
     * Devuelve el titulo de la notificacion.
     *
     * @return string
     */
    public function getTitulo(): string
    {
        return $this->titulo;
    }

    /**
     * Cambia el titulo de la notificacion.
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
     * Devuelve el ID de la notificacion.
     *
     * @return string
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * Cambia el ID de la notificacion.
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
     * Devuelve la fecha de creacion de la notificacion.
     *
     * @return DateTime
     */
    public function getFechaCreacion(): DateTime
    {
        return $this->fecha_creacion;
    }

    /**
     * Cambia la fecha de creacion de la notificacion.
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
     * Devuelve el estado de la notificacion.
     *
     * @return Estado
     */
    public function getEstado(): Estado
    {
        return $this->estado;
    }

    /**
     * Cambia el estado de la notificacion.
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
     * Devuelve el ID del usuario.
     *
     * @return int
     */
    public function getIdUser(): int
    {
        return $this->id_user;
    }

    /**
     * Cambia el ID del usuario.
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