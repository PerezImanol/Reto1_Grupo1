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
require_once("Autorizacion.php");

header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 0);
error_reporting(E_ALL);

$controller = new NotificacionController();
$metodo = $_SERVER['REQUEST_METHOD'];

// Convierte el estado recibido en un Enum.
function obtenerEstado(?string $strEstado): Estado
{
    return match (strtoupper($strEstado ?? '')) {
        "EN_PROCESO" => Estado::EN_PROCESO,
        "PENDIENTE" => Estado::PENDIENTE,
        "REALIZADA" => Estado::REALIZADA,
        default => throw new InvalidArgumentException("Estado inválido.")
    };
}

try {
    switch ($metodo) {
        // CONSULTAR NOTIFICACIONES
        case 'GET':
            requiereSesion();

            $idUsuarioSesion = obtenerIdUsuarioSesion();
            if ($_SESSION["role"] === "USER") {
                // USER solamente consulta sus propias notificaciones.
                $resultado = $controller->getAllNotifsUser($idUsuarioSesion);
                if (isset($_GET['id'])) {
                    $id = (int) $_GET['id'];
                    $resultado = array_values(array_filter(
                        $resultado,
                        fn($notif) => $notif->getIdNotificacion() === $id
                    ));
                }
            } else {
                // ADMIN puede consultar las notificaciones de cualquier usuario.
                if (isset($_GET['id'])) {
                    $resultado = $controller->buscarPorId((int) $_GET['id']);
                } elseif (isset($_GET['id_user'])) {
                    $resultado = $controller->getAllNotifsUser((int) $_GET['id_user']);
                } else {
                    echo json_encode([
                        "mensaje" => "Falta el ID del usuario.",
                        "status" => http_response_code(404),
                        "exito" => false
                    ]);
                    exit;
                }
            }

            echo json_encode([
                'result' => $resultado,
                'status' => http_response_code(200),
                'exito' => true
            ]);
            exit;

        // CREAR NOTIFICACIÓN
        case 'POST':
            requiereSesion();

            $datos = json_decode(file_get_contents('php://input'), true);
            if (
                !is_array($datos) || empty(trim($datos['titulo'] ?? '')) ||
                empty(trim($datos['descripcion'] ?? ''))
            ) {
                echo json_encode([
                    "mensaje" => "Debes introducir un título y una descripción.",
                    'status' => http_response_code(400),
                    'exito' => false
                ]);
                exit;
            }

            if ($_SESSION["role"] === "USER") {
                // USER no puede elegir el propietario, la fecha ni el estado.
                $idUsuario = obtenerIdUsuarioSesion();
                $estado = Estado::PENDIENTE;
                $fecha = new DateTime();
            } else {
                requiereAdmin();

                $idUsuario = (int) ($datos['id_user'] ?? 0);
                $estado = obtenerEstado($datos['estado'] ?? 'PENDIENTE');
                $fecha = new DateTime($datos['fecha_creacion'] ?? 'now');
            }

            if ($idUsuario <= 0) {
                echo json_encode([
                    "mensaje" => "Usuario inválido.",
                    'status' => http_response_code(400),
                    'exito' => false
                ]);
                exit;
            }

            $exito = $controller->insertar(new Notificacion(
                0,
                trim($datos['titulo']),
                trim($datos['descripcion']),
                $fecha,
                $estado,
                $idUsuario
            ));

            if ($exito) {
                echo json_encode([
                    "mensaje" => "Notificación creada correctamente.",
                    'status' => http_response_code(201),
                    'exito' => true
                ]);
            } else {
                echo json_encode([
                    "mensaje" => "No se pudo crear la notificación.",
                    'status' => http_response_code(500),
                    'exito' => false
                ]);
            }
            exit;


        // MODIFICAR NOTIFICACIÓN
        case 'PUT':
            requiereSesion();

            $datos = json_decode(file_get_contents('php://input'), true);
            if (!is_array($datos)) {
                echo json_encode([
                    "mensaje" => "Datos inválidos.",
                    'status' => http_response_code(400),
                    'exito' => false
                ]);
                exit;
            }

            $id = (int) ($datos['id'] ?? 0);
            $titulo = trim($datos['titulo'] ?? '');
            $descripcion = trim($datos['descripcion'] ?? '');

            if ($id <= 0 || $titulo === '' || $descripcion === '') {
                echo json_encode([
                    "mensaje" => "Debes introducir un título y una descripción válidos.",
                    'status' => http_response_code(400),
                    'exito' => false
                ]);
                exit;
            }

            if ($_SESSION["role"] === "USER") {
                // USER solo modifica título y descripción de sus notificaciones.
                $exito = $controller->actualizarContenido(
                    $id,
                    obtenerIdUsuarioSesion(),
                    $titulo,
                    $descripcion
                );
            } else {
                // ADMIN puede modificar también fecha y estado.
                requiereAdmin();

                $estado = obtenerEstado($datos['estado'] ?? null);
                $fecha = new DateTime($datos['fecha_creacion'] ?? 'now');
                $idUsuario = (int) ($datos['id_user'] ?? 0);

                $exito = $controller->actualizar(new Notificacion(
                    $id,
                    $titulo,
                    $descripcion,
                    $fecha,
                    $estado,
                    $idUsuario
                ));
            }

            if ($exito) {
                echo json_encode([
                    "mensaje" => "Notificación actualizada correctamente.",
                    "status" => http_response_code(200),
                    "exito" => true
                ]);
            } else {
                echo json_encode([
                    "mensaje" => "No se pudo modificar la notificación. Puede que esté realizada o no te pertenezca.",
                    'status' => http_response_code(403),
                    'exito' => false
                ]);
            }

            exit;


        // ELIMINAR NOTIFICACIÓN
        case 'DELETE':
            requiereSesion();

            $id = (int) ($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode([
                    "mensaje" => "ID de notificación inválido.",
                    "status" => http_response_code(400),
                    "exito" => false
                ]);
                exit;
            }

            if ($_SESSION["role"] === "USER") {
                // USER solo puede eliminar las suyas.
                $idUsuario = obtenerIdUsuarioSesion();
            } else {
                requiereAdmin();

                $idUsuario = (int) ($_GET['id_user'] ?? 0);
            }

            $exito = $controller->eliminar($id, $idUsuario);

            if ($exito) {
                echo json_encode([
                    "mensaje" => "Notificación eliminada correctamente.",
                    "exito" => true
                ]);
            } else {
                http_response_code(403);
                echo json_encode([
                    "mensaje" => "No se puede eliminar. Comprueba que te pertenece y que no está realizada.",
                    'status' => http_response_code(403),
                    'exito' => false
                ]);
            }
            exit;

        default:
            echo json_encode([
                "mensaje" => "Método no permitido.",
                'status' => http_response_code(405),
                'exito' => false
            ]);
            exit;
    }
} catch (Throwable $error) {
    error_log($error->getMessage());

    echo json_encode([
        "mensaje" => "Error interno al gestionar las notificaciones.",
        'status' => http_response_code(500),
        'exito' => false
    ]);
}
