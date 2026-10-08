<?php

use App\Models\Ubicacion;

require_once("../dao/UbicacionDAO.php");
require_once("../model/Ubicacion.php");

class UbicacionController
{
    private UbicacionDAO $dao;

    public function __construct()
    {
        $this->dao = new UbicacionDAO();
    }

    public function listar(): array
    {
        return $this->dao->buscarUbicaciones();
    }

    public function buscarPorId(int $id): ?Ubicacion
    {
        return $this->dao->buscarUbicacionPorId($id);
    }

    public function insertar(string $nombre, string $descripcion): bool
    {
        $ubicacion = new Ubicacion(
            0,
            $nombre,
            $descripcion
        );

        return $this->dao->insertarUbicacion($ubicacion);
    }

    public function actualizar(int $id, string $nombre, string $descripcion): bool {
        $ubicacion = new Ubicacion(
            $id,
            $nombre,
            $descripcion
        );

        return $this->dao->actualizarUbicacion($ubicacion);
    }

    public function eliminar(int $id): bool
    {
        return $this->dao->eliminarUbicacion($id);
    }
}