<?php

use App\Models\Equipamiento;
use App\Enums\EnumCategoria;

require_once("../model/Equipamiento.php");
require_once("../dao/ConexionDB.php");
require_once("../model/EnumCategoria.php");
require_once("../dao/EquipamientoDAO.php");

class EquipamientoDAO
{
    //Variable de la conexion.
    private mysqli $conexion;

    //Initializador de la conexion.
    public function __construct()
    {
        $this->conexion = ConexionDB::conexion();
    }

    //Devuelve todos los equipamientos en la base de datos.
    public function buscarTodos(): array
    {
        //Preparacion de query
        $sql = "SELECT e.*, u.nombre AS nombre_ubicacion
            FROM equipamientos e
            LEFT JOIN ubicaciones u
            ON e.id_ubicacion_actual = u.id_ubicacion";

        $resultado = $this->conexion->query($sql);
        $equipamientos = [];
        
        //Resultados de query
        while ($fila = $resultado->fetch_assoc()) {
            $categoria = match ($fila["categoria"]) {
                "PORTATIL" => EnumCategoria::PORTATIL,
                "SOBREMESA" => EnumCategoria::SOBREMESA,
                "PERIFERICO" => EnumCategoria::PERIFERICO,
                "AUDIOVISUAL" => EnumCategoria::AUDIOVISUAL,
                "OTROS" => EnumCategoria::OTROS
            };

            $equipamiento = new Equipamiento(
                $fila["id_equipamiento"],
                $fila["nombre"],
                $fila["descripcion"],
                $fila["marca"],
                $fila["modelo"],
                $categoria,
                $fila["id_ubicacion_actual"]
            );
            $equipamiento->setNombreUbicacion($fila["nombre_ubicacion"]);
            
            $equipamientos[] = $equipamiento;
        }
        return $equipamientos;
    }

    //Busca un equipamiento en concreto por ID en la base de datos y la devuelve.
    public function buscarPorId(int $idEquipamiento): ?Equipamiento
    {
        //Preparacion de query
        $sql = "SELECT *
                    FROM equipamientos
                    WHERE id_equipamiento = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idEquipamiento);
        $stmt->execute();
        $resultado = $stmt->get_result();

        //Resultados de query
        if ($fila = $resultado->fetch_assoc()) {
            $categoria = match ($fila["categoria"]) {
                "PORTATIL" => EnumCategoria::PORTATIL,
                "SOBREMESA" => EnumCategoria::SOBREMESA,
                "PERIFERICO" => EnumCategoria::PERIFERICO,
                "AUDIOVISUAL" => EnumCategoria::AUDIOVISUAL,
                "OTROS" => EnumCategoria::OTROS
            };

            $equipamiento = new Equipamiento(
                $fila["id_equipamiento"],
                $fila["nombre"],
                $fila["descripcion"],
                $fila["marca"],
                $fila["modelo"],
                $categoria,
                $fila["id_ubicacion_actual"]
            );
            return $equipamiento;
        } else {
            return null;
        }
    }

    //Inserta un nuevo equipamiento a la base de datos.
    public function insertar(Equipamiento $equipamiento): bool
    {
        //Preparacion de query
        $sql = "INSERT INTO equipamientos (nombre, descripcion, marca, modelo, categoria, id_ubicacion_actual)
                    VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);
        $nombre = $equipamiento->getNombre();
        $descripcion = $equipamiento->getDescripcion();
        $marca = $equipamiento->getMarca();
        $modelo = $equipamiento->getModelo();
        $enumCategoria = $equipamiento->getCategoria();
        $categoria = isset($enumCategoria->value) ? $enumCategoria->value : $enumCategoria->name;
        $idUbicacionActual = $equipamiento->getIdUbicacionActual();
        $stmt->bind_param(
            "sssssi",
            $nombre,
            $descripcion,
            $marca,
            $modelo,
            $categoria,
            $idUbicacionActual
        );

        //Resultados de query
        return $stmt->execute();
    }

    //Actualiza un equipamiento en la base de datos.
    public function actualizar(Equipamiento $equipamiento): bool
    {
        //Preparacion de query
        $sql = "UPDATE equipamientos
                SET nombre = ?,
                    descripcion = ?,
                    marca = ?,
                    modelo = ?,
                    categoria = ?,
                    id_ubicacion_actual = ?
                WHERE id_equipamiento = ?";

        $stmt = $this->conexion->prepare($sql);
        $nombre = $equipamiento->getNombre();
        $descripcion = $equipamiento->getDescripcion();
        $marca = $equipamiento->getMarca();
        $modelo = $equipamiento->getModelo();
        $enumCategoria = $equipamiento->getCategoria();
        $categoriaStr = isset($enumCategoria->value) ? $enumCategoria->value : $enumCategoria->name;
        $idUbicacionActual = $equipamiento->getIdUbicacionActual();
        $id = $equipamiento->getIdEquipamiento();

        $stmt->bind_param(
            "sssssii",
            $nombre,
            $descripcion,
            $marca,
            $modelo,
            $categoriaStr,
            $idUbicacionActual,
            $id
        );

        //Resultados de query
        return $stmt->execute();
    }

    // Elimina un equipamiento en la base de datos.
    public function eliminar(int $idEquipamiento): bool
    {
        //Preparacion de query
        $sql = "DELETE FROM equipamientos
                    WHERE id_equipamiento = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idEquipamiento);

        //Resultados de query
        return $stmt->execute();
    }
}
