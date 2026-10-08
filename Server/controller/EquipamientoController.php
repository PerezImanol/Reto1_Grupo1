<?php

use App\Enums\EnumCategoria;
use App\Models\Equipamiento;

require_once("../dao/EquipamientoDAO.php");
require_once("../model/Equipamiento.php");

class EquipamientoController
{
    private EquipamientoDAO $dao;

    public function __construct()
    {
        $this->dao = new EquipamientoDAO();
    }

    public function listar(): array
    {
        return $this->dao->buscarTodos();
    }

    public function buscarPorId(int $id): ?Equipamiento
    {
        return $this->dao->buscarPorId($id);
    }

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

    public function eliminar(int $id): bool
    {
        return $this->dao->eliminar($id);
    }
}
