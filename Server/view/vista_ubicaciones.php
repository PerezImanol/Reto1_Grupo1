<?php

require_once("../controller/UbicacionController.php");

$controlador = new UbicacionController();

$ubicaciones = $controlador->listar();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ubicaciones</title>
</head>
<body>

<h1>Lista de ubicaciones</h1>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
    </tr>

    <?php foreach ($ubicaciones as $ubicacion): ?>

        <tr>
            <td><?= $ubicacion->getIdUbicacion() ?></td>
            <td><?= $ubicacion->getNombre() ?></td>
            <td><?= $ubicacion->getDescripcion() ?></td>
        </tr>

    <?php endforeach; ?>

</table>

</body>
</html>
