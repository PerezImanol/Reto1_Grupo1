<?php
/*
GET
http://localhost/Server/Controller/UsuarioController.php

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
http://localhost/Server/Controller/UsuarioController.php?id=8
*/
require_once("../dao/ConexionDB.php");
require_once("../dao/UsuarioDAO.php");

class UsuarioController
{
    private UsuarioDAO $UsuarioDAO;

    public function __construct()
    {
        $this->UsuarioDAO = new UsuarioDAO();
    }

    public function getAllUsers()
    {
        return $this->UsuarioDAO->getAllUsers();
    }

    public function buscarPorId(int $idUser)
    {
        return $this->UsuarioDAO->buscarPorId($idUser);
    }

    public function insertar(Usuario $user)
    {
        return $this->UsuarioDAO->insertar($user);
    }

    public function actualizar(Usuario $user)
    {
        return $this->UsuarioDAO->actualizar($user);
    }

    public function actualizar_password(Usuario $user)
    {
        return $this->UsuarioDAO->actualizar_password($user);
    }

    public function eliminar(int $idUser)
    {
        return $this->UsuarioDAO->eliminar($idUser);
    }

    public function login(string $username, string $password)
    {
        return $this->UsuarioDAO->login($username, $password);
    }
}

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
    };
}

switch ($metodo) {
    case 'GET':
        if (isset($_GET['id'])) {
            $resultado = $controller->buscarPorId((int) $_GET['id']);
        } else {
            $resultado = $controller->getAllUsers();
        }

        http_response_code(200);
        echo json_encode($resultado);
        exit;

    case 'POST':
        $datos = json_decode(file_get_contents('php://input'), true);

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
            http_response_code(201);
            echo json_encode(["status" => "success", "mensaje" => "Usuario creado con éxito."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "mensaje" => "No se pudo insertar el usuario en la base de datos, puede que el nombre de usuario este cogido."]);
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

        $id = (int) ($datos['id'] ?? 0);
        $rol = obtenerRol($datos['role'] ?? null);

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "ID de usuario inválido"
            ]);
            exit;
        }

        // Cambiar contraseña
        if (isset($datos['password']) && isset($datos['newPassword'])) {

            $passCheck = $controller->login(
                $datos['username'] ?? '',
                $datos['password']
            );

            if (!$passCheck) {
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "mensaje" => "La contraseña actual es incorrecta"
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

        // Si solo se envía una de las dos contraseñas, hay un error
        } elseif (isset($datos['password']) || isset($datos['newPassword'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "Para cambiar la contraseña, introduce la contraseña actual y la nueva"
            ]);
            exit;

        // Editar los datos sin cambiar la contraseña
        } else {
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
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "mensaje" => "Usuario actualizado con éxito"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "mensaje" => "No se pudo actualizar el usuario"
            ]);
        }

        exit;


    case 'DELETE':
        $id = $_GET['id'] ?? null;

        if ($id && $controller->eliminar((int) $id)) {
            echo json_encode(["status" => "success", "mensaje" => "Usuario eliminado correctamente"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "mensaje" => "ID inválido o no se pudo eliminar el usuario."]);
        }
        exit;

    default:
        http_response_code(405);
        echo json_encode(["mensaje" => "Método no permitido"]);
        exit;
}

?>