<?php

use App\Models\Ubicacion;

require_once("../dao/UbicacionDAO.php");
require_once("../model/Ubicacion.php");

class UbicacionController
{
    private UbicacionDAO $dao;

    public function __construct()
    {
        $this->dao = new UbicacionDAO();
    }

    public function listar(): array
    {
        return $this->dao->buscarUbicaciones();
    }

    public function buscarPorId(int $id): ?Ubicacion
    {
        return $this->dao->buscarUbicacionPorId($id);
    }

    public function insertar(string $nombre, string $descripcion): bool
    {
        $ubicacion = new Ubicacion(
            0,
            $nombre,
            $descripcion
        );

        return $this->dao->insertarUbicacion($ubicacion);
    }

    public function actualizar(int $id, string $nombre, string $descripcion): bool {
        $ubicacion = new Ubicacion(
            $id,
            $nombre,
            $descripcion
        );

        return $this->dao->actualizarUbicacion($ubicacion);
    }

    public function eliminar(int $id): bool
    {
        return $this->dao->eliminarUbicacion($id);
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

$controller = new UbicacionController();
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
        http_response_code(405);
        echo json_encode([
            "mensaje" => "Método no permitido"
        ]);
        exit;
}