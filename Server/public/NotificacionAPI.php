<?php
/*
GET
http://localhost/Server/public/NotificacionAPI.php?id_user=2

POST
{
    "titulo": "PRUEBA",
    "descripcion": "PRUEBA",
    "fecha_creacion": "2026-09-19 08:45:00.000000",
    "estado": "PENDIENTE",
    "id_user": 2
}

PUT
{
    "id": 6,
    "titulo": "PRUEBAAAAAA",
    "descripcion": "PRUEBA",
    "fecha_creacion": "2026-10-16 11:00:00",
    "estado": "PENDIENTE",
    "id_user": 2
}

DELETE
http://localhost/Server/public/NotificacionAPI.php?id_user=2&id=6
*/

require_once("../controller/NotificacionController.php");

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

ini_set('display_errors', 0);
error_reporting(E_ALL);

ini_set('log_errors', 1);
ini_set('error_log', 'php_error.log');

$controller = new NotificacionController();
$metodo = $_SERVER['REQUEST_METHOD'];

// Convertimos el estado recibido en texto al Enum que necesita el modelo.
function obtenerEstado(?string $strEstado): Estado
{
    return match (strtoupper($strEstado ?? '')) {
        "EN_PROCESO" => Estado::EN_PROCESO,
        "PENDIENTE" => Estado::PENDIENTE,
        "REALIZADA" => Estado::REALIZADA,
        default => throw new InvalidArgumentException("Estado inválido. Debe ser PENDIENTE, EN_PROCESO o REALIZADA.")
    };
}

switch ($metodo) {

    case 'GET':

        // Buscamos una notificación concreta o todas las de un usuario.
        if (isset($_GET['id']) && isset($_GET['id_user'])) {
            $resultado = $controller->buscarPorId((int) $_GET['id']);
        } else if (isset($_GET['id_user'])) {
            $resultado = $controller->getAllNotifsUser((int) $_GET['id_user']);
        }

        if (isset($resultado)) {
            echo json_encode($resultado);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "Faltan uno o más IDs."
            ]);
        }

        exit;

    case 'POST':

        // Leemos los datos JSON enviados en el cuerpo de la petición.
        $datos = json_decode(file_get_contents('php://input'), true);
        $estado = obtenerEstado($datos['estado'] ?? null);

        $exito = $controller->insertar(new Notificacion(
            (int) ($datos['id'] ?? 0),
            $datos['titulo'] ?? '',
            $datos['descripcion'] ?? '',
            new DateTime($datos['fecha_creacion']),
            $estado,
            (int) ($datos['id_user'] ?? 0)
        ));

        if ($exito) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "mensaje" => "Notificación creada con éxito."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "mensaje" => "No se pudo insertar la notificación en la base de datos."
            ]);
        }

        exit;

    case 'PUT':

        $datos = json_decode(file_get_contents('php://input'), true);
        $estado = obtenerEstado($datos['estado'] ?? null);

        $exito = $controller->actualizar(new Notificacion(
            (int) ($datos['id'] ?? 0),
            $datos['titulo'] ?? '',
            $datos['descripcion'] ?? '',
            new DateTime($datos['fecha_creacion']),
            $estado,
            (int) ($datos['id_user'] ?? 0)
        ));

        if ($exito) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "mensaje" => "Notificación actualizada con éxito"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "mensaje" => "No se pudo actualizar la notificación en la base de datos."
            ]);
        }

        exit;

    case 'DELETE':

        // Necesitamos el ID de la notificación y el del usuario al que pertenece.
        $id = $_GET['id'] ?? null;
        $id_user = $_GET['id_user'] ?? null;

        if ($id && $controller->eliminar((int) $id, (int) $id_user)) {
            echo json_encode([
                "status" => "success",
                "mensaje" => "Notificación eliminada correctamente"
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "ID inválido o no se pudo eliminar la notificación."
            ]);
        }

        exit;

    default:

        // Indicamos que el método HTTP utilizado no está permitido.
        http_response_code(405);
        echo json_encode([
            "mensaje" => "Método no permitido"
        ]);

        exit;
}
