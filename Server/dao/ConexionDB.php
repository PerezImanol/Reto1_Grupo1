<?php
    class ConexionDB
    {
        public static function conexion(): mysqli
        {
            $conexion = new mysqli(
                "localhost",
                "root",
                "",
                "reto_2daw"
            );
            // Indicamos que utilizaremos UTF-8.
            // Permite trabajar correctamente con caracteres como á, é, ñ...
            $conexion->query("SET NAMES 'utf8'");
            // Devolvemos la conexión.
            return $conexion;
        }
    }
?>