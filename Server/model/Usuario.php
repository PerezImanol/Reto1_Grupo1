<?php
class Usuario
{
    private string $nombre;
    private string $apellido;
    private string $username;
    private string $password;
    private Rol $role;

    /**
     * Get the value of nombre
     *
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
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
     * Get the value of apellido
     *
     * @return string
     */
    public function getApellido(): string
    {
        return $this->apellido;
    }

    /**
     * Set the value of apellido
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
     * Get the value of username
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Set the value of username
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
     * Get the value of password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the value of password
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
     * Get the value of role
     *
     * @return Rol
     */
    public function getRole(): Rol
    {
        return $this->role;
    }

    /**
     * Set the value of role
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