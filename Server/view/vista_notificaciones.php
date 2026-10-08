<?php

require_once("../controller/NotificacionController.php");

$controller = new controller();

$id_usuario = 2;

$notificaciones = $controller->getAllNotifsUser($id_usuario);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Notificaciones</title>
</head>

<body>

<h1>Mis notificaciones</h1>

<?php if (empty($notificaciones)): ?>

    <p>No tienes notificaciones.</p>

<?php else: ?>

    <table border="1">

        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Usuario</th>
        </tr>

        <?php foreach ($notificaciones as $notificacion): ?>

            <tr>
                <td>
                    <?= $notificacion->getIdNotificacion() ?>
                </td>

                <td>
                    <?= $notificacion->getTitulo() ?>
                </td>

                <td>
                    <?= $notificacion->getDescripcion() ?>
                </td>

                <td>
                    <?= $notificacion->getFechaCreacion()->format("Y-m-d H:i:s") ?>
                </td>

                <td>
                    <?= $notificacion->getEstado()->name ?>
                </td>

                <td>
                    <?= $notificacion->getIdUser() ?>
                </td>
            </tr>

        <?php endforeach; ?>

    </table>

<?php endif; ?>

</body>

</html>