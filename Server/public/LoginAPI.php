<?php
session_start();

require_once ("../controller/UsuarioController.php");

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
                http_response_code(400);
                echo json_encode(["error" => "Introduce el usuario y la contraseña"]);
                break;
            }

            $usuario = $controller->login($username, $password);

            if ($usuario === null) {
                http_response_code(401);
                echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
                break;
            }

            session_regenerate_id(true);

            $_SESSION["id_usuario"] = $usuario->getIdUsuario();
            $_SESSION["role"] = $usuario->getRole()->name;

            echo json_encode([
                "mensaje" => "Inicio de sesión correcto",
                "usuario" => [
                    "id_usuario" => $usuario->getIdUsuario(),
                    "nombre" => $usuario->getNombre(),
                    "apellido" => $usuario->getApellido(),
                    "username" => $usuario->getUsername(),
                    "role" => $usuario->getRole()->name
                ]
            ]);
            break;

        case "GET":
            if (!isset($_SESSION["id_usuario"])) {
                http_response_code(401);
                echo json_encode(["error" => "No hay una sesión iniciada"]);
                break;
            }

            $usuario = $controller->buscarPorId((int) $_SESSION["id_usuario"]);

            if ($usuario === null) {
                session_unset();
                session_destroy();

                http_response_code(401);
                echo json_encode(["error" => "La sesión ya no es válida"]);
                break;
            }

            echo json_encode([
                "usuario" => [
                    "id_usuario" => $usuario->getIdUsuario(),
                    "nombre" => $usuario->getNombre(),
                    "apellido" => $usuario->getApellido(),
                    "username" => $usuario->getUsername(),
                    "role" => $usuario->getRole()->name
                ]
            ]);
            break;

        case "DELETE":
            $_SESSION = [];
            session_destroy();

            echo json_encode(["mensaje" => "Sesión cerrada"]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error interno del servidor"]);
}
