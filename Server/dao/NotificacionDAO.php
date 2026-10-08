<?php

require_once "model/Notificacion.php";
require_once "dao/ConexionDB.php";

class NotificacionDAO
{
    private mysqli $conexion;
    public function __construct()
    {
        $this->conexion = ConexionDB::conexion();
    }

    public function getAllNotifsUser(int $id_user): array
    {
        $notifs = [];
        $sql = "SELECT * FROM notificaciones; WHERE id_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_user);
        $resultado = $this->conexion->query($sql);

        while ($fila = $resultado->fetch_assoc()) {
            $notif = new Notificacion(
                $fila["id_notificacion"],
                $fila["titulo"],
                $fila["descripcion"],
                $fila["fecha_creacion"],
                $fila["estado"],
                $fila["id_usuario"]
            );

            $notifs[] = $notif;
        }
        return $notifs;
    }

    public function buscarPorId(int $idUser): ?Notificacion
    {
        $sql = "SELECT *
                    FROM notificaciones
                    WHERE id_notificacion = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($fila = $resultado->fetch_assoc()) {
            $notif = new Notificacion(
                $fila["id_notificacion"],
                $fila["titulo"],
                $fila["descripcion"],
                $fila["fecha_creacion"],
                $fila["estado"],
                $fila["id_usuario"]
            );
            return $notif;
        } else {
            return null;
        }

    }

    public function insertar(Notificacion $notif): bool
    {
        $sql = "INSERT INTO notificaciones (titulo, descripcion, fecha_creacion, estado, id_usuario)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $titulo = $notif->getTitulo();
        $descripcion = $notif->getDescripcion();
        $fecha_creacion = $notif->getFechaCreacion();
        $estado = $notif->getEstado();
        $id_usuario = $notif->getIdUser();

        $stmt->bind_param(
            "ssssi",
            $titulo,
            $descripcion,
            $fecha_creacion,
            $estado,
            $id_usuario
        );
        return $stmt->execute();
    }

    public function actualizar(Notificacion $notif): bool
    {
        $sql = "UPDATE notificaciones
                    SET titulo = ?,
                    descripcion = ?,
                    fecha_creacion = ?,
                    estado = ?;
                    WHERE id_notificacion = ?";

        $stmt = $this->conexion->prepare($sql);
        $titulo = $notif->getTitulo();
        $descripcion = $notif->getDescripcion();
        $fecha_creacion = $notif->getFechaCreacion();
        $estado = $notif->getEstado();
        $id = $notif->getIdNotificacion();

        $stmt->bind_param(
            "ssssi",
            $titulo,
            $descripcion,
            $fecha_creacion,
            $estado,
            $id
        );
        return $stmt->execute();
    }

    // ELIMINAR
    public function eliminar(int $idNotif): bool
    {
        $sql = "DELETE FROM notificaciones
                    WHERE id_notificacion = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idNotif);
        return $stmt->execute();
    }
}
?>