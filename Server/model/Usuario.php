<?php

require_once "Rol.php";
class Usuario implements JsonSerializable
{
    //El ID del usuario.
    private int $id_usuario;

    //El nombre personal del usuario.
    private string $nombre;

    //El apellido del usuario.
    private string $apellido;

    //El nombre de usuario, necesario para login.
    private string $username;

    //La contraseña del usuario, necesario para login.
    private string $password;

    //El rol del usuario.
    private Rol $role;

    //El constructor de clase.
    public function __construct(
        int $id_usuario,
        string $nombre,
        string $apellido,
        string $username,
        string $password,
        Rol $role
    ) {
        $this->id_usuario = $id_usuario;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }

    //Metodo implementado de JsonSerializable, necesario para poder usar postman y el enrutador.
    public function jsonSerialize(): mixed
    {
        return [
            'id_usuario' => $this->id_usuario,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'username' => $this->username,
            'password' => $this->password,
            'role' => isset($this->role->value) ? $this->role->value : $this->role->name
        ];
    }

    /**
     * Devuelve el ID del usuario.
     *
     * @return int
     */
    public function getIdUsuario(): int
    {
        return $this->id_usuario;
    }

    /**
     * Cambia el ID del usuario.
     *
     * @param int $id_usuario
     *
     * @return self
     */
    public function setIdUsuario(int $id_usuario): self
    {
        $this->id_usuario = $id_usuario;
        return $this;
    }

    /**
     * Devuelve el nombre personal del usuario.
     *
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * Cambia el nombre personal del usuario.
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
     * Devuelve el apellido del usuario.
     *
     * @return string
     */
    public function getApellido(): string
    {
        return $this->apellido;
    }

    /**
     * Cambia el apellido del usuario.
     *
     * @param string $apellido
     *
     * @return self
     */
    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;
        return $this;
    }

    /**
     * Devuelve el nombre de usuario.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Cambia el nombre de usuario.
     *
     * @param string $username
     *
     * @return self
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Devuelve el la contraseña del usuario.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Cambia el la contraseña del usuario.
     *
     * @param string $password
     *
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Devuelve el rol del usuario.
     *
     * @return Rol
     */
    public function getRole(): Rol
    {
        return $this->role;
    }

    /**
     * Cambia el rol del usuario.
     *
     * @param Rol $role
     *
     * @return self
     */
    public function setRole(Rol $role): self
    {
        $this->role = $role;
        return $this;
    }
}
?>