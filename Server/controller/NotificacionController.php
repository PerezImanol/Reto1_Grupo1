<?php

require_once("../dao/ConexionDB.php");
require_once("../dao/NotificacionDAO.php");
require_once("../model/Notificacion.php");

class NotificacionController
{
    private NotificacionDAO $NotificacionDAO;

    public function __construct()
    {
        $this->NotificacionDAO = new NotificacionDAO();
    }

    // Devuelve todas las notificaciones de un usuario.
    public function getAllNotifsUser(int $id_user)
    {
        return $this->NotificacionDAO->getAllNotifsUser($id_user);
    }

    // Busca una notificación por su ID.
    public function buscarPorId(int $idUser)
    {
        return $this->NotificacionDAO->buscarPorId($idUser);
    }

    // Guarda una nueva notificación.
    public function insertar(Notificacion $notif)
    {
        return $this->NotificacionDAO->insertar($notif);
    }

    // Actualiza los datos de una notificación existente.
    public function actualizar(Notificacion $notif)
    {
        return $this->NotificacionDAO->actualizar($notif);
    }

    // Elimina una notificación comprobando también el ID del usuario.
    public function eliminar(int $idNotif, int $idUser)
    {
        return $this->NotificacionDAO->eliminar($idNotif, $idUser);
    }
}