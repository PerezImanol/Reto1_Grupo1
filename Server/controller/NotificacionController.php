<?php

require_once("../dao/ConexionDB.php");
require_once("../dao/NotificacionDAO.php");

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

ini_set('display_errors', 0);

class NotificacionController
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

$controller = new NotificacionController();
$metodo = $_SERVER['REQUEST_METHOD'];


?>