<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requiereSesion(): void
{
    if (!isset($_SESSION["id_usuario"], $_SESSION["role"])) {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "mensaje" => "Debes iniciar sesión."
        ]);
        exit;
    }
}

function requiereAdmin(): void
{
    requiereSesion();

    if ($_SESSION["role"] !== "ADMIN") {
        http_response_code(403);
        echo json_encode([
            "status" => "error",
            "mensaje" => "No tienes permisos para realizar esta operación."
        ]);
        exit;
    }
}

function obtenerIdUsuarioSesion(): int
{
    requiereSesion();

    return (int) $_SESSION["id_usuario"];
}