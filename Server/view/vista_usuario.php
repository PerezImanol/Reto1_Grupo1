<?php

require_once("../controller/UsuarioController.php");

$controller = new controller();

$usuarios = $controller->getAllUsers();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
</head>

<body>

<h1>Lista de usuarios</h1>

<table border="1">

    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Username</th>
        <th>Password</th>
        <th>Rol</th>
    </tr>

    <?php foreach ($usuarios as $usuario): ?>

        <tr>

            <td>
                <?= $usuario->getIdUsuario() ?>
            </td>

            <td>
                <?= $usuario->getNombre() ?>
            </td>

            <td>
                <?= $usuario->getApellido() ?>
            </td>

            <td>
                <?= $usuario->getUsername() ?>
            </td>

            <td>
                <?= $usuario->getPassword() ?>
            </td>

            <td>
                <?= $usuario->getRole()->name ?>
            </td>

        </tr>

    <?php endforeach; ?>

</table>

</body>
</html>
