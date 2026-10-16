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



use App\Enums\EnumCategoria;

require_once( "../controller/EquipamientoController.php");

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

// Respondemos a las peticiones previas del navegador sin ejecutar ninguna operación.
if ($metodo === 'OPTIONS') {
    http_response_code(204);
    exit;
}

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
        
        // Si recibimos un ID, buscamos un equipamiento; si no, los mostramos todos.
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

        $categoriaEnum = obtenerCategoria($datos['categoria'] ?? null);

        $exito = $controller->insertar(
            $datos['nombre'] ?? '',
            $datos['descripcion'] ?? '',
            $datos['marca'] ?? '',
            $datos['modelo'] ?? '',
            $categoriaEnum,
            (int) ($datos['id_ubicacion_actual'] ?? 0)
        );

        // Devolvemos el resultado de la inserción.
        if ($exito) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "mensaje" => "Equipamiento creado con éxito"
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

        $categoriaEnum = obtenerCategoria($datos['categoria'] ?? null);

        // Aceptamos ambos nombres para el ID del equipamiento.
        $id = (int) (
            $datos['id_equipamiento'] ?? $datos['id'] ?? 0
        );

        $exito = $controller->actualizar(
            $id,
            $datos['nombre'] ?? '',
            $datos['descripcion'] ?? '',
            $datos['marca'] ?? '',
            $datos['modelo'] ?? '',
            $categoriaEnum,
            (int) ($datos['id_ubicacion_actual'] ?? 0)
        );

        if ($exito) {
            echo json_encode([
                "status" => "success",
                "mensaje" => "Equipamiento actualizado con éxito"
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
        // El ID se recibe como parámetro en la URL.
        $id = (int) ($_GET['id'] ?? 0);

        if ($id > 0 && $controller->eliminar($id)) {
            echo json_encode([
                "status" => "success",
                "mensaje" => "Equipamiento eliminado correctamente"
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
        // Respondemos con un error si se utiliza un método HTTP no permitido.
        http_response_code(405);
        echo json_encode([
            "status" => "error",
            "mensaje" => "Método no permitido"
        ]);

        exit;
}
