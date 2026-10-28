<?php
/*

GET
http://localhost/Server/Controller/HistoricoUbicacionController.php

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
http://localhost/Server/Controller/HistoricoUbicacionController.php?id=37
*/



use App\Models\HistoricoUbicaciones;

require_once("../dao/HistoricoUbicacionDAO.php");
require_once("../model/HistoricoUbicaciones.php");

class HistoricoUbicacionesController
{
    private HistoricoUbicacionesDAO $dao;

    public function __construct()
    {
        $this->dao = new HistoricoUbicacionesDAO();
    }

    public function listar(): array
    {
        return $this->dao->buscarHistoricos();
    }

    public function buscarPorId(int $id): ?HistoricoUbicaciones
    {
        return $this->dao->buscarHistoricoPorId($id);
    }

    public function insertar(
        int $idEquipamiento,
        int $idUbicacion,
        DateTime $fechaInicio,
        ?DateTime $fechaFin
    ): bool {
        $historico = new HistoricoUbicaciones(
            0,
            $idEquipamiento,
            $idUbicacion,
            $fechaInicio,
            $fechaFin
        );

        return $this->dao->insertarHistorico($historico);
    }

    public function actualizar(
        int $idHistorico,
        int $idEquipamiento,
        int $idUbicacion,
        DateTime $fechaInicio,
        ?DateTime $fechaFin
    ): bool {
        $historico = new HistoricoUbicaciones(
            $idHistorico,
            $idEquipamiento,
            $idUbicacion,
            $fechaInicio,
            $fechaFin
        );

        return $this->dao->actualizarHistorico($historico);
    }

    public function eliminar(int $id): bool
    {
        return $this->dao->eliminarHistorico($id);
    }
}

// ==============================================================
// ENRUTADOR Y CONTROLADOR HTTP PARA POSTMAN
// ==============================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$controller = new HistoricoUbicacionesController();
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {

    case 'GET':
        if (isset($_GET['id'])) {
            $resultado = $controller->buscarPorId((int) $_GET['id']);
        } else {
            $resultado = $controller->listar();
        }

        echo json_encode($resultado);
        exit;

    
    case 'POST':
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!is_array($datos)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "JSON inválido"
            ]);
            exit;
        }

        try {
            $fechaInicio = new DateTime($datos['fecha_inicio'] ?? '');

            $fechaFin = !empty($datos['fecha_fin'])
                ? new DateTime($datos['fecha_fin'])
                : null;

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "Formato de fecha inválido"
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
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "mensaje" => "Histórico de ubicación creado con éxito"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "mensaje" => "No se pudo insertar el histórico en la base de datos"
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

        $idHistorico = (int) ($datos['id_historico'] ?? 0);
        $idEquipamiento = (int) ($datos['id_equipamiento'] ?? 0);
        $idUbicacion = (int) ($datos['id_ubicacion'] ?? 0);

        if ($idHistorico <= 0 || $idEquipamiento <= 0 || $idUbicacion <= 0
            || empty($datos['fecha_inicio'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "Faltan datos obligatorios o algún ID no es válido"
            ]);
            exit;
        }

        try {
            $fechaInicio = new DateTime($datos['fecha_inicio']);

            $fechaFin = !empty($datos['fecha_fin'])
                ? new DateTime($datos['fecha_fin'])
                : null;
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "Formato de fecha inválido"
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
                "status" => "success",
                "mensaje" => "Histórico de ubicación actualizado correctamente"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "mensaje" => "No se pudo actualizar el histórico en la base de datos"
            ]);
        }

        exit;


    case 'DELETE':
        $id = $_GET['id'] ?? $_GET['id'] ?? null;

        if (
            $id !== null &&
            (int) $id > 0 &&
            $controller->eliminar((int) $id)
        ) {
            echo json_encode([
                "status" => "success",
                "mensaje" => "Histórico de ubicación eliminado correctamente"
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "ID inválido o no se pudo eliminar el histórico"
            ]);
        }
        exit;

    default:
        http_response_code(405);
        echo json_encode([
            "mensaje" => "Método no permitido"
        ]);
        exit;
}