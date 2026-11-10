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

//Setup de headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

ini_set('display_errors', 0);

//Setup de controlador y metodo.
$controller = new UbicacionController();
$metodo = $_SERVER['REQUEST_METHOD'];

//Maneja los metodos disponibles.
switch ($metodo) {
    case 'GET':
        // Si recibimos un ID, buscamos una ubicación, y si no, mostramos todas.
        try {
            if (isset($_GET['id'])) {
                $resultado = $controller->buscarPorId((int) $_GET['id']);
            } else {
                $resultado = $controller->listar();
            }

            //Devielve el resultado.
            echo json_encode([
                'result' => $resultado,
                'status' => http_response_code(200),
                'exito' => true
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            echo json_encode([
                'mensaje' => $th->getMessage(),
                'status' => http_response_code(500),
                'exito' => false
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'POST':
        // Leemos los datos JSON enviados en el cuerpo de la petición.
        $datos = json_decode(file_get_contents('php://input'), true);

        //Si los datos no son un array, se invalida la peticion.
        if (!is_array($datos)) {
            echo json_encode([
                "mensaje" => "JSON inválido.",
                'status' => http_response_code(400),
                'exito' => false
            ]);
            exit;
        }

        //Intenta la insercion.
        try {
            $exito = $controller->insertar(
                $datos['nombre'] ?? '',
                $datos['descripcion'] ?? ''
            );

            if ($exito) {
                echo json_encode([
                    "mensaje" => "Ubicación creada con éxito.",
                    'status' => http_response_code(201),
                    'exito' => true
                ]);
            } else {
                echo json_encode([
                    "mensaje" => "No se pudo insertar la ubicacion en la base de datos.",
                    'status' => http_response_code(500),
                    'exito' => false
                ]);
            }
        } catch (\Throwable $th) {
            echo json_encode([
                'mensaje' => $th->getMessage(),
                'status' => http_response_code(500),
                'exito' => false
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'PUT':
        // Leemos los datos JSON enviados en el cuerpo de la petición.
        $datos = json_decode(file_get_contents('php://input'), true);

        //Si los datos no son un array, se invalida la peticion.
        if (!is_array($datos)) {
            echo json_encode([
                "mensaje" => "JSON inválido.",
                'status' => http_response_code(400),
                'exito' => false
            ]);
            exit;
        }

        try {
            // Aceptamos id o id_ubicacion para identificar el registro que se actualizará.
            $exito = $controller->actualizar(
                (int) ($datos['id'] ?? $datos['id_ubicacion'] ?? 0),
                $datos['nombre'] ?? '',
                $datos['descripcion'] ?? ''
            );

            if ($exito) {
                echo json_encode([
                    "mensaje" => "Ubicación actualizada con éxito.",
                    'status' => http_response_code(200),
                    'exito' => true
                ]);
            } else {
                echo json_encode([
                    "mensaje" => "No se pudo actualizar en la base de datos.",
                    'status' => http_response_code(500),
                    'exito' => false
                ]);
            }
        } catch (\Throwable $th) {
            echo json_encode([
                'mensaje' => $th->getMessage(),
                'status' => http_response_code(500),
                'exito' => false
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'DELETE':
        $id = $_GET['id'] ?? $_GET['id_ubicacion'] ?? null;

        //Intenta borrar la ubicacion.
        try {
            if ($id !== null && (int) $id > 0 && $controller->eliminar((int) $id)) {
                echo json_encode([
                    "mensaje" => "Ubicación eliminada correctamente.",
                    'exito' => true
                ]);
            } else {
                echo json_encode([
                    "mensaje" => "No se ha encontrado una ubicacion con la ID proveida.",
                    'status' => http_response_code(404),
                    'exito' => false
                ]);
            }
        } catch (\Throwable $th) {
            echo json_encode([
                'mensaje' => $th->getMessage(),
                'status' => http_response_code(500),
                'exito' => false
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;

    default:
        // Indicamos que el método HTTP utilizado no está permitido.
        echo json_encode([
            "mensaje" => "Método no permitido",
            'status' => http_response_code(405),
            'exito' => false
        ]);
        exit;
}
