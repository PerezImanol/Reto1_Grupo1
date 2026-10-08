<?php

use App\Models\Equipamiento;

    require_once("../model/Equipamiento.php");
    require_once("../model/ConexionDB.php");

    class EquipamientoDAO{
        private mysqli $conexion;
        public function __construct()
        {
            $this->conexion = ConexionDB::conexion();
        }

        public function buscarTodos(): array {
            $equipamientos = array();
            $sql = "SELECT * 
                    FROM equipamientos";
            $resultado = $this->conexion->query($sql);

            while ($fila = $resultado->fetch_assoc()) {
                $equipamiento = new Equipamiento(
                    $fila["id_equipamiento"], 
                    $fila["nombre"],
                    $fila["descripcion"],
                    $fila["marca"],
                    $fila["modelo"],
                    $fila["categoria"],
                    $fila["id_ubicacion_actual"]
                );
                
                $equipamientos[] = $equipamiento;
            }
            return $equipamientos;
        }

        public function buscarPorId(int $idEquipamiento): ?Equipamiento
        {
            $sql = "SELECT *
                    FROM equipamientos
                    WHERE id_equipamiento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idEquipamiento);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($fila = $resultado->fetch_assoc()) {
                $equipamiento = new Equipamiento(
                    $fila["id_equipamiento"], 
                    $fila["nombre"],
                    $fila["descripcion"],
                    $fila["marca"],
                    $fila["modelo"],
                    $fila["categoria"],
                    $fila["id_ubicacion_actual"]
                );
                return $equipamiento;
            }else{
                return null;
            }
            
        }

         public function insertar(Equipamiento $equipamiento): bool
        {
            $sql = "INSERT INTO equipamientos (nombre, descripcion, marca, modelo, categoria, id_ubicacion_actual)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conexion->prepare($sql);
            $nombre = $equipamiento->getNombre();
            $descripcion = $equipamiento->getDescripcion();
            $marca = $equipamiento->getMarca();
            $modelo = $equipamiento->getModelo();
            $categoria = $equipamiento->getCategoria();
            $idUbicacionActual = $equipamiento->getIdUbicacionActual();

            $stmt->bind_param(
                "ssssei",
                $nombre,
                $descripcion,
                $marca,
                $modelo,
                $categoria,
                $idUbicacionActual
            );
            return $stmt->execute();
        }

        public function actualizar(Equipamiento $equipamiento): bool
        {
            $sql = "UPDATE equipamnientos
                    SET nombre = ?,
                        descripcion = ?,
                        marca = ?,
                        modelo = ?,
                        categoria = ?,
                        id_ubicacion_actual = ?;
                    WHERE id = ?";

            $stmt = $this->conexion->prepare($sql);
            $nombre = $equipamiento->getNombre();
            $descripcion = $equipamiento->getDescripcion();
            $marca = $equipamiento->getMarca();
            $modelo = $equipamiento->getModelo();
            $categoria = $equipamiento->getCategoria();
            $idUbicacionActual = $equipamiento->getIdUbicacionActual();
            $id = $equipamiento-> getIdEquipamiento();

            $stmt->bind_param(
                "sssssii",
                $nombre,
                $descripcion,
                $marca,
                $modelo,
                $categoria,
                $idUbicacionActual,
                $id
            );
            return $stmt->execute();
        }
        // ELIMINAR
        public function eliminar(int $idEquipamiento): bool
        {
            $sql = "DELETE FROM equipamientos
                    WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idEquipamiento);
            return $stmt->execute();
        }
    }
    
?>