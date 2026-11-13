<?php
class ConexionDB
{
    //Inicializa la conexion a la base de datos.
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

            // Envío del json del error
            echo json_encode([
                "mensaje" => "Internal Server Error: Could not reach the database",
                'status' => http_response_code(500),
                'exito' => false
            ]);
        }
        exit;
    }
}
