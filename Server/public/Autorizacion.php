<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requiereSesion(): void
{
    if (!isset($_SESSION["id_usuario"], $_SESSION["role"])) {
        echo json_encode([
            "mensaje" => "Debes iniciar sesión.",
            'status' => http_response_code(401),
            'exito' => false
        ]);
        exit;
    }
}

function requiereAdmin(): void
{
    requiereSesion();

    if ($_SESSION["role"] !== "ADMIN") {
        echo json_encode([
            "mensaje" => "No tienes permisos para realizar esta operación.",
            'status' => http_response_code(403),
            'exito' => false
        ]);
        exit;
    }
}

function obtenerIdUsuarioSesion(): int
{
    requiereSesion();
    return (int) $_SESSION["id_usuario"];
}