
<?php
/*
GET
http://localhost/Server/public/UbicacionAPI.php

POST
{
    "nombre": "PRUEBA",
    "descripcion": "PRUEBA"
}

PUT
{
    "id_ubicacion": 12,
    "nombre": "PRUEBA EDITADA",
    "descripcion": "PRUEBA EDITED"
}

DELETE
http://localhost/Server/public/UbicacionAPI.php?id=12
*/

require_once("../controller/UbicacionController.php");

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

ini_set('display_errors', 0);

// Respondemos a las peticiones previas del navegador sin ejecutar ninguna operación.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$controller = new UbicacionController();
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {

    case 'GET':

        // Si recibimos un ID, buscamos una ubicación; si no, mostramos todas.
        if (isset($_GET['id'])) {
            $resultado = $controller->buscarPorId((int) $_GET['id']);
        } else {
            $resultado = $controller->listar();
        }

        echo json_encode($resultado);
        exit;

    case 'POST':

        // Leemos los datos JSON enviados en el cuerpo de la petición.
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!is_array($datos)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "JSON inválido"
            ]);
            exit;
        }

        $exito = $controller->insertar(
            $datos['nombre'] ?? '',
            $datos['descripcion'] ?? ''
        );

        if ($exito) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "mensaje" => "Ubicación creada con éxito"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "mensaje" => "No se pudo insertar en la base de datos"
            ]);
        }

        exit;

    case 'PUT':

        $datos = json_decode(file_get_contents('php://input'), true);

        if (!is_array($datos)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "JSON inválido"
            ]);
            exit;
        }

        // Aceptamos id o id_ubicacion para identificar el registro que se actualizará.
        $exito = $controller->actualizar(
            (int) ($datos['id'] ?? $datos['id_ubicacion'] ?? 0),
            $datos['nombre'] ?? '',
            $datos['descripcion'] ?? ''
        );

        if ($exito) {
            echo json_encode([
                "status" => "success",
                "mensaje" => "Ubicación actualizada con éxito"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "mensaje" => "No se pudo actualizar en la base de datos"
            ]);
        }

        exit;

    case 'DELETE':

        // El ID de la ubicación que queremos eliminar se recibe en la URL.
        $id = $_GET['id'] ?? $_GET['id_ubicacion'] ?? null;

        if ($id !== null && (int) $id > 0 && $controller->eliminar((int) $id)) {
            echo json_encode([
                "status" => "success",
                "mensaje" => "Ubicación eliminada correctamente"
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "ID inválido o no se pudo eliminar"
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
