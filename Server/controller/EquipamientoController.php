<?php

use App\Enums\EnumCategoria;
use App\Models\Equipamiento;

require_once("../dao/EquipamientoDAO.php");
require_once("../model/Equipamiento.php");
require_once("../model/EnumCategoria.php");

class EquipamientoController
{
    private EquipamientoDAO $dao;

    public function __construct()
    {
        $this->dao = new EquipamientoDAO();
    }

    // Devuelve todos los equipamientos registrados.
    public function listar(): array
    {
        return $this->dao->buscarTodos();
    }

    // Busca un equipamiento por su ID.
    public function buscarPorId(int $id): ?Equipamiento
    {
        return $this->dao->buscarPorId($id);
    }

    // Crea un equipamiento nuevo y lo guarda en la base de datos.
    public function insertar(string $nombre, string $descripcion, string $marca, string $modelo, EnumCategoria $categoria, int $idUbicacionActual): bool
    {
        $Equipamiento = new Equipamiento(
            0,
            $nombre,
            $descripcion,
            $marca,
            $modelo,
            $categoria,
            $idUbicacionActual
        );

        return $this->dao->insertar($Equipamiento);
    }

    // Actualiza los datos de un equipamiento existente.
    public function actualizar(int $id, string $nombre, string $descripcion, string $marca, string $modelo, EnumCategoria $categoria, int $idUbicacionActual): bool
    {
        $Equipamiento = new Equipamiento(
            $id,
            $nombre,
            $descripcion,
            $marca,
            $modelo,
            $categoria,
            $idUbicacionActual
        );

        return $this->dao->actualizar($Equipamiento);
    }

    // Elimina un equipamiento por su ID.
    public function eliminar(int $id): bool
    {
        return $this->dao->eliminar($id);
    }
}
