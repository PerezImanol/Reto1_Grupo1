<?php
class ConexionDB
{
    public static function conexion(): mysqli
    {
        try {
            $conexion = new mysqli(
                "localhost",
                "root",
                "",
                "reto_2daw"
            );
            $conexion->query("SET NAMES 'utf8'");
            // Devolvemos la conexión.
            return $conexion;
        } catch (\Throwable $th) {
            // Embohasa header JSON
        header('Content-Type: application/json; charset=utf-8');
        
        // Embohasa código HTTP 500 (Internal Server Error)
        http_response_code(500);

        // Envío del json del error
        echo json_encode([
            "status" => "error",
            "mensaje" => "Internal Server Error: Could not reach the database"
        ]);
        }

        exit;

    }
}
