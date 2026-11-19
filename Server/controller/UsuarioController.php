<?php

require_once("../dao/ConexionDB.php");
require_once("../dao/UsuarioDAO.php");
require_once("../model/Usuario.php");

class UsuarioController
{
    private UsuarioDAO $dao;

    public function __construct()
    {
        $this->dao = new UsuarioDAO();
    }

    // Devuelve todos los usuarios registrados.
    public function getAllUsers()
    {
        return $this->dao->getAllUsers();
    }

    // Busca un usuario por su ID.
    public function buscarPorId(int $idUser)
    {
        return $this->dao->buscarPorId($idUser);
    }

    // Registra un usuario nuevo.
    public function insertar(Usuario $user)
    {
        return $this->dao->insertar($user);
    }

    // Actualiza los datos de un usuario sin cambiar su contraseña.
    public function actualizar(Usuario $user)
    {
        return $this->dao->actualizar($user);
    }

    // Actualiza la contraseña de un usuario.
    public function actualizar_password(Usuario $user)
    {
        return $this->dao->actualizar_password($user);
    }

    // Elimina un usuario por su ID.
    public function eliminar(int $idUser)
    {
        return $this->dao->eliminar($idUser);
    }

    // Comprueba las credenciales de acceso de un usuario.
    public function login(string $username, string $password)
    {
        return $this->dao->login($username, $password);
    }
}
