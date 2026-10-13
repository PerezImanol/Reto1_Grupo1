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
    private NotificacionDAO $NotificacionDAO;

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

function obtenerRol(?string $strEstado): Estado
{
    return match (strtoupper($strEstado ?? '')) {
        "EN_PROCESO" => Estado::EN_PROCESO,
        "PENDIENTE" => Estado::PENDIENTE,
        "REALIZADA" => Estado::REALIZADA
    };
}

switch ($metodo) {
    case 'GET':
        if (isset($_GET['notifId']) && isset($_GET['userId'])) {
            $resultado = $controller->buscarPorId((int) $_GET['notifId']);
        } else if (isset($_GET['userId'])) {
            $resultado = $controller->getAllNotifsUser((int) $_GET['userId']);
        }

        if (isset($resultado)) {
            echo json_encode($resultado);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "mensaje" => "Faltan uno o mas IDs."]);
        }
        exit;

    case 'POST':
        $datos = json_decode(file_get_contents('php://input'), true);

        $estado = obtenerRol($datos['categoria'] ?? null);

        $exito = $controller->insertar(new Notificacion(
            (int) ($datos['notifId'] ?? 0),
            $datos['titulo'] ?? '',
            $datos['descripcion'] ?? '',
            $datos['fecha_creacion'] ?? '',
            $estado,
            (int) ($datos['userId'] ?? 0)
        ));

        if ($exito) {
            http_response_code(201);
            echo json_encode(["status" => "success", "mensaje" => "Notificacion creada con éxito."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "mensaje" => "No se pudo insertar la notificacion en la base de datos."]);
        }
        exit;

    case 'PUT':
        $datos = json_decode(file_get_contents('php://input'), true);

        $estado = obtenerRol($datos['categoria'] ?? null);

        $exito = $controller->actualizar(new Notificacion(
            (int) ($datos['notifId'] ?? 0),
            $datos['titulo'] ?? '',
            $datos['descripcion'] ?? '',
            $datos['fecha_creacion'] ?? '',
            $estado,
            (int) ($datos['userId'] ?? 0)
        ));

        if ($exito) {
            echo json_encode(["status" => "success", "mensaje" => "Notificacion actualizada con éxito"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "mensaje" => "No se pudo actualizar la notificacion en la base de datos."]);
        }
        exit;

    case 'DELETE':
        $id = $_GET['id'] ?? null;

        if ($id && $controller->eliminar((int) $id)) {
            echo json_encode(["status" => "success", "mensaje" => "Notificacion eliminada correctamente"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "mensaje" => "ID inválido o no se pudo eliminar la notificacion."]);
        }
        exit;

    default:
        http_response_code(405);
        echo json_encode(["mensaje" => "Método no permitido"]);
        exit;
}

?>