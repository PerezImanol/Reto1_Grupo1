<?php

require_once ("../dao/ConexionDB.php");
require_once ("../dao/UsuarioDAO.php");

class controller
{
    private $NotificacionDAO;

    public function __construct()
    {
        $this->NotificacionDAO = new UsuarioDAO();
    }

    public function getAllUsers()
    {
        return $this->NotificacionDAO->getAllUsers();
    }

    public function buscarPorId(int $idUser)
    {
        return $this->NotificacionDAO->buscarPorId($idUser);
    }

    public function insertar(Usuario $user)
    {
        return $this->NotificacionDAO->insertar($user);
    }

    public function actualizar(Usuario $user)
    {
        return $this->NotificacionDAO->actualizar($user);
    }

    public function actualizar_password(Usuario $user)
    {
        return $this->NotificacionDAO->actualizar_password($user);
    }

    public function eliminar(int $idUser)
    {
        return $this->NotificacionDAO->eliminar($idUser);
    }

    public function login(string $username, string $password)
    {
        return $this->NotificacionDAO->login($username, $password);
    }
}

?>