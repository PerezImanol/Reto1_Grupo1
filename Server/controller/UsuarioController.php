<?php

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
        if (isset($_GET['notifId'])) {
            $resultado = $controller->buscarPorId((int) $_GET['notifId']);
        } else {
            $resultado = $controller->getAllUsers();
        }

        echo json_encode($resultado);
        exit;

    case 'POST':
        $datos = json_decode(file_get_contents('php://input'), true);

        $rol = obtenerRol($datos['categoria'] ?? null);
        $exito = $controller->insertar(new Usuario(
            (int) ($datos['id'] ?? 0),
            $datos['nombre'] ?? '',
            $datos['apellidos'] ?? '',
            $datos['username'] ?? '',
            $datos['password'] ?? '',
            $rol,
        ));

        if ($exito) {
            http_response_code(201);
            echo json_encode(["status" => "success", "mensaje" => "Usuario creado con éxito."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "mensaje" => "No se pudo insertar el usuario en la base de datos."]);
        }
        exit;

    case 'PUT':
        $datos = json_decode(file_get_contents('php://input'), true);

        $rol = obtenerRol($datos['categoria'] ?? null);

        if (isset($datos['password'])) {
            $exito = $controller->actualizar_password(new Usuario(
                (int) ($datos['id'] ?? 0),
                $datos['nombre'] ?? '',
                $datos['apellidos'] ?? '',
                $datos['username'] ?? '',
                $datos['password'] ?? '',
                $rol,
            ));

            if ($exito) {
                $exito = $controller->actualizar(new Usuario(
                    (int) ($datos['id'] ?? 0),
                    $datos['nombre'] ?? '',
                    $datos['apellidos'] ?? '',
                    $datos['username'] ?? '',
                    $datos['password'] ?? '',
                    $rol,
                ));

                if ($exito) {
                    http_response_code(201);
                    echo json_encode(["status" => "success", "mensaje" => "Usuario actualizado con éxito."]);
                } else {
                    http_response_code(500);
                    echo json_encode(["status" => "error", "mensaje" => "No se pudo actualizar el usuario en la base de datos."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "mensaje" => "La contraseña introducida es incorrecta, o hubo un problema al actualizar la contraseña."]);
            }
        } else {
            $exito = $controller->actualizar(new Usuario(
                (int) ($datos['id'] ?? 0),
                $datos['nombre'] ?? '',
                $datos['apellidos'] ?? '',
                $datos['username'] ?? '',
                $datos['password'] ?? '',
                $rol,
            ));

            if ($exito) {
                http_response_code(201);
                echo json_encode(["status" => "success", "mensaje" => "Usuario creado con éxito."]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "mensaje" => "No se pudo insertar el usuario en la base de datos."]);
            }
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