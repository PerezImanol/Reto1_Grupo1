<?php

require_once("../model/Notificacion.php");
require_once("../dao/ConexionDB.php");

class NotificacionDAO
{
    //Variable de la conexion.
    private mysqli $conexion;

    //Initializador de la conexion.
    public function __construct()
    {
        $this->conexion = ConexionDB::conexion();
    }

    //Devuelve todas las notificaciones de un usuario en concreto en la base de datos.
    public function getAllNotifsUser(int $id_user): array
    {
        $notifs = [];

        $sql = "SELECT * FROM notificaciones WHERE id_usuario = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_user);
        $stmt->execute();

        $resultado = $stmt->get_result();

        while ($fila = $resultado->fetch_assoc()) {

            $estado = match ($fila["estado"]) {
                "PENDIENTE" => Estado::PENDIENTE,
                "EN_PROCESO" => Estado::EN_PROCESO,
                "REALIZADA" => Estado::REALIZADA
            };

            $notif = new Notificacion(
                $fila["id_notificacion"],
                $fila["titulo"],
                $fila["descripcion"],
                new DateTime($fila["fecha_creacion"]),
                $estado,
                $fila["id_usuario"]
            );

            $notifs[] = $notif;
        }

        return $notifs;
    }

    //Devuelve una notificacion de un usuario en concreto en la base de datos.
    public function buscarPorId(int $idNotif): ?Notificacion
    {
        $sql = "SELECT *
                    FROM notificaciones
                    WHERE id_notificacion = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idNotif);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($fila = $resultado->fetch_assoc()) {
            $estado = match ($fila["estado"]) {
                "PENDIENTE" => Estado::PENDIENTE,
                "EN_PROCESO" => Estado::EN_PROCESO,
                "REALIZADA" => Estado::REALIZADA
            };

            $notif = new Notificacion(
                $fila["id_notificacion"],
                $fila["titulo"],
                $fila["descripcion"],
                new DateTime($fila["fecha_creacion"]),
                $estado,
                $fila["id_usuario"]
            );
            return $notif;
        } else {
            return null;
        }

    }

    //Inserta una nueva notificacion a la base de datos.
    public function insertar(Notificacion $notif): bool
    {
        $sql = "INSERT INTO notificaciones (titulo, descripcion, fecha_creacion, estado, id_usuario)
                    VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $titulo = $notif->getTitulo();
        $descripcion = $notif->getDescripcion();
        $fecha_creacion = $notif->getFechaCreacion()->format('Y-m-d H:i:s');
        $estadoEnum = $notif->getEstado();
        $estado = isset($estadoEnum->value) ? $estadoEnum->value : $estadoEnum->name;
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

    //Actualiza una notificacion a la base de datos.
    public function actualizar(Notificacion $notif): bool
    {
        $sql = "UPDATE notificaciones
                SET titulo = ?,
                    descripcion = ?,
                    fecha_creacion = ?,
                    estado = ?
                WHERE id_notificacion = ? AND id_usuario = ?";

        $stmt = $this->conexion->prepare($sql);

        $titulo = $notif->getTitulo();
        $descripcion = $notif->getDescripcion();
        $fecha_creacion = $notif->getFechaCreacion()->format('Y-m-d H:i:s');

        $estadoEnum = $notif->getEstado();
        $estado = isset($estadoEnum->value)
            ? $estadoEnum->value
            : $estadoEnum->name;

        $id = $notif->getIdNotificacion();
        $id_user = $notif->getIdUser();

        $stmt->bind_param(
            "ssssii",
            $titulo,
            $descripcion,
            $fecha_creacion,
            $estado,
            $id,
            $id_user
        );

        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    // Elimina una notificacion en la base de datos.
    public function eliminar(int $idNotif, int $idUser): bool
    {
        $sql = "DELETE FROM notificaciones
                    WHERE id_notificacion = ? AND id_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idNotif, $idUser);
        return $stmt->execute();
    }
}
?>