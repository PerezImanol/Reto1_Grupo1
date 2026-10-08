<?php
require_once '../dao/ConexionDB.php';
require_once '../model/NotificacionDAO.php';


class controller
{
    private $NotificacionDAO;

    public function __construct()
    {
        $this->NotificacionDAO = new NotificacionDAO();
    }

    public function getAllNotifsUser(int $id_user)
    {
        return $this->NotificacionDAO->getAllNotifsUser($id_user);
    }

    public function buscarPorId(int $idUser)
    {
        return $this->NotificacionDAO->buscarPorId($idUser);
    }

    public function insertar(Notificacion $notif)
    {
        return $this->NotificacionDAO->insertar($notif);
    }

    public function actualizar(Notificacion $notif)
    {
        return $this->NotificacionDAO->actualizar($notif);
    }

    public function eliminar(int $idUser)
    {
        return $this->NotificacionDAO->eliminar($idUser);
    }
}

?>