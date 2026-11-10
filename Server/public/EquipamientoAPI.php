<?php
/*
GET
http://localhost/Server/public/EquipamientoAPI.php

POST
{
    "nombre": "PRUEBA",
    "descripcion": "PRUEBA",
    "marca": "Varios",
    "modelo": "Lote 01",
    "categoria": "OTROS",
    "id_ubicacion_actual": 1
}

PUT
{
    "id_equipamiento": 15,
    "nombre": "PRUEBA EDITADA",
    "descripcion": "PRUEBITA",
    "marca": "Logitech",
    "modelo": "Lote 01",
    "categoria": "PERIFERICO",
    "id_ubicacion_actual": 8
}

DELETE
http://localhost/Server/public/EquipamientoAPI.phpid=15
*/


require_once("Autorizacion.php");
use App\Enums\EnumCategoria;

require_once("../controller/EquipamientoController.php");

// Indicamos que las respuestas serán JSON y permitimos las peticiones necesarias.
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

ini_set('display_errors', 0);

// Crear el controlador
$controller = new EquipamientoController();

// Obtener el método HTTP
$metodo = $_SERVER['REQUEST_METHOD'];

// Convertir el texto recibido a EnumCategoria
function obtenerCategoria(?string $strCategoria): EnumCategoria
{
    return match (strtoupper($strCategoria ?? '')) {
        "PORTATIL" => EnumCategoria::PORTATIL,
        "SOBREMESA" => EnumCategoria::SOBREMESA,
        "PERIFERICO" => EnumCategoria::PERIFERICO,
        "AUDIOVISUAL" => EnumCategoria::AUDIOVISUAL,
        default => EnumCategoria::OTROS
    };
}
switch ($metodo) {
    case 'GET':
        requiereSesion();

        if (isset($_GET['id'])) {
            $resultado = $controller->buscarPorId((int) $_GET['id']);
        } else {
            $resultado = $controller->listar();
        }

        echo json_encode([
            'result' => $resultado,
            'status' => http_response_code(200),
            'exito' => true
        ]);
        exit;

    case 'POST':
        requiereAdmin();

        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            echo json_encode([
                "mensaje" => "JSON inválido",
                'status' => http_response_code(400),
                'exito' => false
            ]);
            exit;
        }

        $categoriaEnum = obtenerCategoria($datos['categoria'] ?? null);

        $exito = $controller->insertar(
            $datos['nombre'] ?? '',
            $datos['descripcion'] ?? '',
            $datos['marca'] ?? '',
            $datos['modelo'] ?? '',
            $categoriaEnum,
            (int) ($datos['id_ubicacion_actual'] ?? 0)
        );

        if ($exito) {
            echo json_encode([
                "mensaje" => "Equipamiento creado con éxito",
                'status' => http_response_code(201),
                'exito' => true
            ]);
        } else {
            echo json_encode([
                "mensaje" => "No se pudo crear el equipamiento",
                'status' => http_response_code(500),
                'exito' => false
            ]);
        }
        exit;

    case 'PUT':
        requiereSesion();

        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            echo json_encode([
                "mensaje" => "JSON inválido",
                'status' => http_response_code(400),
                'exito' => false
            ]);
            exit;
        }

        $id = (int) ($datos['id_equipamiento'] ?? $datos['id'] ?? 0);

        // USER solamente puede cambiar la ubicación.
        if ($_SESSION["role"] === "USER") {

            $idUbicacion = (int) ($datos['id_ubicacion_actual'] ?? 0);
            if ($id <= 0 || $idUbicacion <= 0) {
                echo json_encode([
                    "mensaje" => "Datos inválidos",
                    'status' => http_response_code(400),
                    'exito' => false
                ]);
                exit;
            }

            $exito = $controller->actualizarUbicacion($id, $idUbicacion);

        } else {
            requiereAdmin();

            $categoriaEnum = obtenerCategoria($datos['categoria'] ?? null);
            $exito = $controller->actualizar(
                $id,
                $datos['nombre'] ?? '',
                $datos['descripcion'] ?? '',
                $datos['marca'] ?? '',
                $datos['modelo'] ?? '',
                $categoriaEnum,
                (int) ($datos['id_ubicacion_actual'] ?? 0)
            );
        }

        if ($exito) {
            echo json_encode([
                "mensaje" => "Equipamiento actualizado con éxito",
                "status" => http_response_code(200),
                "exito" => true
            ]);
        } else {
            echo json_encode([
                "mensaje" => "No se pudo actualizar el equipamiento",
                "status" => http_response_code(500),
                "exito" => false
            ]);
        }
        exit;

    case 'DELETE':
        requiereAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0 && $controller->eliminar($id)) {
            echo json_encode([
                "mensaje" => "Equipamiento eliminado correctamente",
                "exito" => true
            ]);
        } else {
            echo json_encode([
                "mensaje" => "No se pudo encontrar el equipamiento",
                "status" => http_response_code(404),
                "exito" => false
            ]);
        }
        exit;

    default:
        echo json_encode([
            "mensaje" => "Método no permitido",
            'status' => http_response_code(405),
            'exito' => false
        ]);
        exit;
}