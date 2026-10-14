<?php

require_once "../model/Usuario.php";
require_once "../dao/ConexionDB.php";

class UsuarioDAO
{
    private mysqli $conexion;
    public function __construct()
    {
        $this->conexion = ConexionDB::conexion();
    }

    public function login(string $username, string $password)
    {
        $sql = "SELECT *
                    FROM usuarios
                    WHERE nombre_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();

        $role = $row["rol"] === "ADMIN" ? Rol::ADMIN : Rol::USER;

        if ($row && password_verify($password, $row["password_hash"])) {
            return new Usuario(
                $row["id_usuario"],
                $row["nombre"],
                $row["apellidos"],
                $row["nombre_usuario"],
                $row["password_hash"],
                $role
            );
        } else {
            return null;
        }
    }

    public function getAllUsers(): array
    {
        $users = [];
        $sql = "SELECT * 
                    FROM usuarios";
        $resultado = $this->conexion->query($sql);

        while ($fila = $resultado->fetch_assoc()) {
            $role = $fila["rol"] === "ADMIN" ? Rol::ADMIN : Rol::USER;

            $user = new Usuario(
                $fila["id_usuario"],
                $fila["nombre"],
                $fila["apellidos"],
                $fila["nombre_usuario"],
                $fila["password_hash"],
                $role
            );

            $users[] = $user;
        }
        return $users;
    }

    public function buscarPorId(int $idUser): ?Usuario
    {
        $sql = "SELECT *
                    FROM usuarios
                    WHERE id_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($fila = $resultado->fetch_assoc()) {
            $role = $fila["rol"] === "ADMIN" ? Rol::ADMIN : Rol::USER;

            $user = new Usuario(
                $fila["id_usuario"],
                $fila["nombre"],
                $fila["apellidos"],
                $fila["nombre_usuario"],
                $fila["password_hash"],
                $role
            );
            return $user;
        } else {
            return null;
        }

    }

    public function insertar(Usuario $user): bool
    {
        $sql = "INSERT INTO usuarios (nombre, apellidos, nombre_usuario, password_hash, rol)
                    VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $nombre = $user->getNombre();
        $apellido = $user->getApellido();
        $username = $user->getUsername();
        $password = password_hash($user->getPassword(), PASSWORD_DEFAULT);
        $roleEnum = $user->getRole();
        $role = isset($enumCategoria->value) ? $roleEnum->value : $roleEnum->name;


        $stmt->bind_param(
            "sssss",
            $nombre,
            $apellido,
            $username,
            $password,
            $role
        );
        return $stmt->execute();
    }

    public function actualizar(Usuario $user): bool
    {
        $sql = "UPDATE usuarios
                    SET nombre = ?,
                    apellidos = ?,
                    nombre_usuario = ?,
                    rol = ?
                    WHERE id_usuario = ?";

        $stmt = $this->conexion->prepare($sql);
        $nombre = $user->getNombre();
        $apellido = $user->getApellido();
        $username = $user->getUsername();
        $roleEnum = $user->getRole();
        $role = isset($enumCategoria->value) ? $roleEnum->value : $roleEnum->name;
        $id = $user->getIdUsuario();

        $stmt->bind_param(
            "ssssi",
            $nombre,
            $apellido,
            $username,
            $role,
            $id
        );
        return $stmt->execute();
    }

    public function actualizar_password(Usuario $user): bool
    {
        $sql = "UPDATE usuarios
                    SET password_hash = ?
                    WHERE id_usuario = ?";

        $stmt = $this->conexion->prepare($sql);
        $hashed_password = password_hash($user->getPassword(), PASSWORD_DEFAULT);
        $id = $user->getIdUsuario();

        $stmt->bind_param(
            "si",
            $hashed_password,
            $id
        );
        return $stmt->execute();
    }
    // ELIMINAR
    public function eliminar(int $idUser): bool
    {
        $sql = "DELETE FROM usuarios
                    WHERE id_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idUser);
        return $stmt->execute();
    }
}
?>