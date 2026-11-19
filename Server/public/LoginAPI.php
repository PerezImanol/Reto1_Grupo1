<?php
session_start();

require_once("../controller/UsuarioController.php");

header("Content-Type: application/json; charset=UTF-8");

$controller = new UsuarioController();
$metodo = $_SERVER["REQUEST_METHOD"];

try {
    switch ($metodo) {
        case "POST":
            $datos = json_decode(file_get_contents("php://input"), true);
            $username = $datos["username"] ?? "";
            $password = $datos["password"] ?? "";

            if ($username === "" || $password === "") {
                echo json_encode([
                    "error" => "Introduce el usuario y la contraseña",
                    'status' => http_response_code(400),
                    'exito' => false
                ]);
                break;
            }

            $usuario = $controller->login($username, $password);
            if ($usuario === null) {
                echo json_encode([
                    "error" => "Usuario o contraseña incorrectos",
                    'status' => http_response_code(401),
                    'exito' => false
                ]);
                break;
            }

            session_regenerate_id(true);
            $_SESSION["id_usuario"] = $usuario->getIdUsuario();
            $_SESSION["role"] = $usuario->getRole()->name;

            echo json_encode([
                "usuario" => [
                    "id_usuario" => $usuario->getIdUsuario(),
                    "nombre" => $usuario->getNombre(),
                    "apellido" => $usuario->getApellido(),
                    "username" => $usuario->getUsername(),
                    "role" => $usuario->getRole()->name
                ],
                'status' => http_response_code(200),
                'exito' => true
            ]);
            break;

        case "GET":
            if (!isset($_SESSION["id_usuario"])) {
                echo json_encode([
                    "error" => "No hay una sesión iniciada",
                    'status' => http_response_code(401),
                    'exito' => false
                ]);
                break;
            }

            $usuario = $controller->buscarPorId((int) $_SESSION["id_usuario"]);

            if ($usuario === null) {
                session_unset();
                session_destroy();

                echo json_encode([
                    "error" => "La sesión ya no es válida",
                    'status' => http_response_code(401),
                    'exito' => false
                ]);
                break;
            }

            echo json_encode([
                "usuario" => [
                    "id_usuario" => $usuario->getIdUsuario(),
                    "nombre" => $usuario->getNombre(),
                    "apellido" => $usuario->getApellido(),
                    "username" => $usuario->getUsername(),
                    "role" => $usuario->getRole()->name
                ],
                'status' => http_response_code(200),
                'exito' => true
            ]);
            break;

        case "DELETE":
            $_SESSION = [];
            session_destroy();

            echo json_encode([
                "mensaje" => "Sesión cerrada",
                'exito' => true
            ]);
            break;

        default:
            echo json_encode([
                "error" => "Método no permitido",
                'status' => http_response_code(405),
                'exito' => false
            ]);
    }
} catch (Throwable $e) {
    echo json_encode([
        "error" => "Error interno del servidor",
        'status' => http_response_code(500),
        'exito' => false
    ]);
}
