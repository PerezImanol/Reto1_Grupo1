<?php

use App\Enums\EnumCategoria;
use App\Models\Equipamiento;

require_once("../dao/EquipamientoDAO.php");
require_once("../model/Equipamiento.php");
require_once("../model/EnumCategoria.php");

class EquipamientoController
{
    private EquipamientoDAO $dao;

    public function __construct()
    {
        $this->dao = new EquipamientoDAO();
    }

    public function listar(): array
    {
        return $this->dao->buscarTodos();
    }

    public function buscarPorId(int $id): ?Equipamiento
    {
        return $this->dao->buscarPorId($id);
    }

    public function insertar(string $nombre, string $descripcion, string $marca, string $modelo, EnumCategoria $categoria, int $idUbicacionActual): bool
    {
        $Equipamiento = new Equipamiento(
            0,
            $nombre,
            $descripcion,
            $marca,
            $modelo,
            $categoria,
            $idUbicacionActual
        );

        return $this->dao->insertar($Equipamiento);
    }

    public function actualizar(int $id, string $nombre, string $descripcion, string $marca, string $modelo, EnumCategoria $categoria, int $idUbicacionActual): bool
    {
        $Equipamiento = new Equipamiento(
            $id,
            $nombre,
            $descripcion,
            $marca,
            $modelo,
            $categoria,
            $idUbicacionActual
        );

        return $this->dao->actualizar($Equipamiento);
    }

    public function eliminar(int $id): bool
    {
        return $this->dao->eliminar($id);
    }
}

// ==============================================================================
// ENRUTADOR Y CONTROLADOR HTTP PARA POSTMAN
// ==============================================================================

header('Content-Type: application/json; charset=utf-8');

// Permite peticiones desde cualquier origen (resuelve el problema de CORS desde HTML)
header('Access-Control-Allow-Origin: *');

// Métodos HTTP permitidos para la API
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Cabeceras permitidas en las peticiones
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Formato de respuesta JSON
header('Content-Type: application/json; charset=utf-8');

// Oculta advertencias/notificaciones de PHP para no romper la salida JSON

ini_set('display_errors', 0); // Evita que los avisos de PHP rompan el JSON en Postman

$controller = new EquipamientoController();
$metodo = $_SERVER['REQUEST_METHOD'];

// Función auxiliar para obtener el Enum desde una cadena de texto
function obtenerEnumCategoria(?string $strCategoria): EnumCategoria {
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
        if (isset($_GET['id'])) {
            $resultado = $controller->buscarPorId((int)$_GET['id']);
        } else {
            $resultado = $controller->listar();
        }
        echo json_encode($resultado);
        exit;

    case 'POST':
        $datos = json_decode(file_get_contents('php://input'), true);

        $categoriaEnum = obtenerEnumCategoria($datos['categoria'] ?? null);

        $exito = $controller->insertar(
            $datos['nombre'] ?? '',
            $datos['descripcion'] ?? '',
            $datos['marca'] ?? '',
            $datos['modelo'] ?? '',
            $categoriaEnum,
            (int)($datos['id_ubicacion_actual'] ?? 0)
        );

        if ($exito) {
            http_response_code(201);
            echo json_encode(["status" => "success", "mensaje" => "Equipamiento creado con éxito"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "mensaje" => "No se pudo insertar en la base de datos"]);
        }
        exit;

    case 'PUT':
        $datos = json_decode(file_get_contents('php://input'), true);

        $categoriaEnum = obtenerEnumCategoria($datos['categoria'] ?? null);

        $exito = $controller->actualizar(
            (int)($datos['id'] ?? $datos['id_equipamiento'] ?? 0),
            $datos['nombre'] ?? '',
            $datos['descripcion'] ?? '',
            $datos['marca'] ?? '',
            $datos['modelo'] ?? '',
            $categoriaEnum,
            (int)($datos['id_ubicacion_actual'] ?? 0)
        );

        if ($exito) {
            echo json_encode(["status" => "success", "mensaje" => "Equipamiento actualizado con éxito"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "mensaje" => "No se pudo actualizar en la base de datos"]);
        }
        exit;

    case 'DELETE':
        $id = $_GET['id'] ?? null;

        if ($id && $controller->eliminar((int)$id)) {
            echo json_encode(["status" => "success", "mensaje" => "Equipamiento eliminado correctamente"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "mensaje" => "ID inválido o no se pudo eliminar"]);
        }
        exit;

    default:
        http_response_code(405);
        echo json_encode(["mensaje" => "Método no permitido"]);
        exit;
}