<?php
/*
GET
http://localhost/Server/public/UsuarioAPI.php

POST !!!! el user es unico
{
    "nombre": "Jihann",
    "apellido": "Prueba",
    "username": "jihanP",
    "password": "password",
    "role": "USER"

}

PUT !! hay dos formas , o sin cambiar la contraseña:
{
    "id": 10,
    "nombre": "Jihannnn",
    "apellido": "Pruebiiiiiiiita",
    "username": "jihanP",
    "role": "USER"
}

PUT CAMBIANDO LA CONTRASEÑA: // el problema esq deberiamos saber 
cual es la contraseña actual NO HASHEADA para poder cambiarla

{
    "id": 10,
    "nombre": "Jihann",
    "apellido": "Prueba",
    "username": "jihanP",
    "role": "USER",
    "password": "password",
    "newPassword": "MiPasswordNueva456"
}


DELETE
http://localhost/Server/public/UsuarioAPI.php?id=8
*/

require_once("../controller/UsuarioController.php");



header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

ini_set('display_errors', 0);

$controller = new UsuarioController();
$metodo = $_SERVER['REQUEST_METHOD'];

function obtenerRol(?string $strRol): Rol
{
    return match (strtoupper($strRol ?? '')) {
        "USER" => Rol::USER,
        "ADMIN" => Rol::ADMIN,
        default => throw new InvalidArgumentException("Rol inválido. Debe ser USER o ADMIN.")
    };
}

switch ($metodo) {
    case 'GET':
        try {
            if (isset($_GET['id'])) {
                $resultado = $controller->buscarPorId((int) $_GET['id']);
            } else {
                $resultado = $controller->getAllUsers();
            }
            http_response_code(200);
            echo json_encode($resultado);
        } catch (\Throwable $th) {
            echo json_encode([
                'mensaje' => $th->getMessage(),
                'status' => http_response_code(500),
                'exito' => false
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'POST':
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!is_array($datos)) {
            echo json_encode([
                "mensaje" => "JSON inválido.",
                'status' => http_response_code(400),
                'exito' => false
            ]);
            exit;
        }

        try {
            $rol = obtenerRol($datos['role'] ?? null);
            $exito = $controller->insertar(new Usuario(
                (int) ($datos['id'] ?? 0),
                $datos['nombre'] ?? '',
                $datos['apellido'] ?? '',
                $datos['username'] ?? '',
                $datos['password'] ?? '',
                $rol,
            ));

            if ($exito) {
                echo json_encode([
                    "mensaje" => "Usuario creado con éxito.",
                    'status' => http_response_code(201),
                    'exito' => true
                ]);
            } else {
                echo json_encode([
                    "mensaje" => "No se pudo insertar el usuario en la base de datos, puede que el nombre de usuario este cogido.",
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
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!is_array($datos)) {
            echo json_encode([
                "mensaje" => "JSON inválido.",
                'status' => http_response_code(400),
                'exito' => false
            ]);
            exit;
        }

        $id = (int) ($datos['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode([
                "mensaje" => "ID de usuario inválido.",
                "status" => http_response_code(400),
                "exito" => false
            ]);
            exit;
        }

        try {
            $rol = obtenerRol($datos['role'] ?? null);
            // Cambiar contraseña
            if (isset($datos['password']) && isset($datos['newPassword'])) {

                $passCheck = $controller->login(
                    $datos['username'] ?? '',
                    $datos['password']
                );

                if (!$passCheck) {
                    echo json_encode([
                        "status" => http_response_code(400),
                        "mensaje" => "La contraseña actual es incorrecta.",
                        "exito" => false
                    ]);
                    exit;
                }

                $exito = $controller->actualizar_password(new Usuario(
                    $id,
                    $datos['nombre'] ?? '',
                    $datos['apellido'] ?? '',
                    $datos['username'] ?? '',
                    $datos['newPassword'],
                    $rol
                ));
            } elseif (isset($datos['password']) || isset($datos['newPassword'])) {
                // Si solo se envía una de las dos contraseñas, hay un error
                echo json_encode([
                    "status" => http_response_code(400),
                    "mensaje" => "Para cambiar la contraseña, introduce la contraseña actual y la nueva.",
                    "exito" => false
                ]);
                exit;
            } else {
                // Editar los datos sin cambiar la contraseña
                $exito = $controller->actualizar(new Usuario(
                    $id,
                    $datos['nombre'] ?? '',
                    $datos['apellido'] ?? '',
                    $datos['username'] ?? '',
                    '',
                    $rol
                ));
            }

            if ($exito) {
                echo json_encode([
                    "status" => http_response_code(200),
                    "mensaje" => "Usuario actualizado con éxito.",
                    "exito" => true
                ]);
            } else {
                echo json_encode([
                    "status" => http_response_code(500),
                    "mensaje" => "No se pudo actualizar el usuario.",
                    "exito" => false
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
        $id = $_GET['id'] ?? null;

        try {
            if ($id && $controller->eliminar((int) $id)) {
                echo json_encode([
                    "mensaje" => "Usuario eliminado correctamente.",
                    "exito" => true
                ]);
            } else {
                echo json_encode([
                    "status" => http_response_code(400),
                    "mensaje" => "ID inválido o no se pudo eliminar el usuario.",
                    "exito" => false
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
        echo json_encode([
            "mensaje" => "Método no permitido",
            'status' => http_response_code(405),
            'exito' => false
        ]);
        exit;
}

?>