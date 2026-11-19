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

    // Devuelve todas las ubicaciones registradas.
    public function listar(): array
    {
        return $this->dao->buscarUbicaciones();
    }

    // Busca una ubicación por su ID.
    public function buscarPorId(int $id): ?Ubicacion
    {
        return $this->dao->buscarUbicacionPorId($id);
    }

    // Crea una ubicación nueva y la guarda en la base de datos.
    public function insertar(string $nombre, string $descripcion): bool
    {
        $ubicacion = new Ubicacion(
            0,
            $nombre,
            $descripcion
        );

        return $this->dao->insertarUbicacion($ubicacion);
    }

    // Actualiza los datos de una ubicación existente.
    public function actualizar(int $id, string $nombre, string $descripcion): bool
    {
        $ubicacion = new Ubicacion(
            $id,
            $nombre,
            $descripcion
        );

        return $this->dao->actualizarUbicacion($ubicacion);
    }

    // Elimina una ubicación por su ID.
    public function eliminar(int $id): bool
    {
        return $this->dao->eliminarUbicacion($id);
    }
}