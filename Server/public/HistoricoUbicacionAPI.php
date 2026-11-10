<?php
/*
GET
http://localhost/Server/public/HistoricoUbicacionAPI.php

POST
{
    "id_equipamiento": 10,
    "id_ubicacion": 1,
    "fecha_inicio": "2026-10-16 09:18:08",
    "fecha_fin": "2026-10-18 09:18:08"
}

PUT
{
    "id_historico": 37,
    "id_equipamiento": 1,
    "id_ubicacion": 2,
    "fecha_inicio": "2026-10-16 09:18:08",
    "fecha_fin": null
}

DELETE
http://localhost/Server/public/HistoricoUbicacionAPI.php?id=37
*/

require_once("../controller/HistoricoUbicacionController.php");

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

ini_set('display_errors', 0);

$controller = new HistoricoUbicacionesController();
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        // Buscar un histórico concreto por su ID.
        if (isset($_GET['id'])) {
            $resultado = $controller->buscarPorId((int) $_GET['id']);
        }
        // Buscar todos los históricos de un equipamiento.
        elseif (isset($_GET['id_equipamiento'])) {
            $resultado = $controller->listarPorEquipamiento(
                (int) $_GET['id_equipamiento']
            );
        }
        // Mostrar todos los históricos.
        else {
            $resultado = $controller->listar();
        }

        echo json_encode([
            'result' => $resultado,
            'status' => http_response_code(200),
            'exito' => true
        ]);
        exit;

    case 'POST':
        // Leemos los datos JSON enviados en el cuerpo de la petición.
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!is_array($datos)) {
            echo json_encode([
                "mensaje" => "JSON inválido",
                'status' => http_response_code(400),
                'exito' => false
            ]);
            exit;
        }

        try {
            // Convertimos las fechas recibidas en objetos DateTime.
            $fechaInicio = new DateTime($datos['fecha_inicio'] ?? '');

            // La fecha de fin puede ser null si el histórico sigue activo.
            $fechaFin = !empty($datos['fecha_fin'])
                ? new DateTime($datos['fecha_fin'])
                : null;
        } catch (Exception $e) {
            echo json_encode([
                "mensaje" => "Formato de fecha inválido",
                'status' => http_response_code(400),
                'exito' => false
            ]);
            exit;
        }

        $exito = $controller->insertar(
            (int) ($datos['id_equipamiento'] ?? 0),
            (int) ($datos['id_ubicacion'] ?? 0),
            $fechaInicio,
            $fechaFin
        );

        if ($exito) {
            echo json_encode([
                "mensaje" => "Histórico de ubicación creado con éxito",
                'status' => http_response_code(201),
                'exito' => true
            ]);
        } else {
            echo json_encode([
                "mensaje" => "No se pudo insertar el histórico en la base de datos",
                'status' => http_response_code(500),
                'exito' => false
            ]);
        }
        exit;

    case 'PUT':
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!is_array($datos)) {
            echo json_encode([
                "mensaje" => "JSON inválido",
                'status' => http_response_code(400),
                'exito' => false
            ]);
            exit;
        }

        $idHistorico = (int) ($datos['id_historico'] ?? 0);
        $idEquipamiento = (int) ($datos['id_equipamiento'] ?? 0);
        $idUbicacion = (int) ($datos['id_ubicacion'] ?? 0);

        // Comprobamos que estén los IDs y la fecha de inicio antes de actualizar.
        if (
            $idHistorico <= 0 ||
            $idEquipamiento <= 0 ||
            $idUbicacion <= 0 ||
            empty($datos['fecha_inicio'])
        ) {
            echo json_encode([
                "mensaje" => "Faltan datos obligatorios o algún ID no es válido",
                'status' => http_response_code(400),
                'exito' => false
            ]);
            exit;
        }

        try {
            $fechaInicio = new DateTime($datos['fecha_inicio']);

            $fechaFin = !empty($datos['fecha_fin'])
                ? new DateTime($datos['fecha_fin'])
                : null;
        } catch (Exception $e) {
            echo json_encode([
                "mensaje" => "Formato de fecha inválido",
                'status' => http_response_code(400),
                'exito' => false
            ]);
            exit;
        }

        $exito = $controller->actualizar(
            $idHistorico,
            $idEquipamiento,
            $idUbicacion,
            $fechaInicio,
            $fechaFin
        );

        if ($exito) {
            echo json_encode([
                "mensaje" => "Histórico de ubicación actualizado correctamente",
                'status' => http_response_code(200),
                'exito' => true
            ]);
        } else {
            echo json_encode([
                "mensaje" => "No se pudo actualizar el histórico en la base de datos",
                'status' => http_response_code(500),
                'exito' => false
            ]);
        }
        exit;

    case 'DELETE':
        // El ID del registro que queremos eliminar se recibe en la URL.
        $id = $_GET['id'] ?? null;

        if (
            $id !== null &&
            (int) $id > 0 &&
            $controller->eliminar((int) $id)
        ) {
            echo json_encode([
                "mensaje" => "Histórico de ubicación eliminado correctamente",
                'exito' => true
            ]);
        } else {
            echo json_encode([
                "mensaje" => "ID inválido",
                'status' => http_response_code(404),
                'exito' => false
            ]);
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