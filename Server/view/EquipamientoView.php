<?php

require_once("../controller/EquipamientoController.php");

$controlador = new EquipamientoController();

$equipamientos = $controlador->listar();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Equipamientos</title>
</head>

<body>

<h1>Equipamientos</h1>

<table border="1">
    <tr>
        <th>ID Equipamiento</th>
        <th>Nombre</th>
        <th>Descripcion</th>
        <th>Marca</th>
        <th>Modelo</th>
        <th>Categoria</th>
        <th>ID Ubicacion Actual</th>
    </tr>

    <?php foreach ($equipamientos as $equipamiento): ?>

        <tr>
            <td><?= $equipamiento->getIdEquipamiento() ?></td>

            <td><?= $equipamiento->getNombre() ?></td>

            <td><?= $equipamiento->getIdUbicacion() ?></td>

            <td><?= $equipamiento->getDescripcion() ?></td>

            <td><?= $equipamiento->getMarca() ?></td>

            <td><?= $equipamiento->getModelo() ?></td>

            <td><?= $equipamiento->getCategoria() ?></td>

            <td><?= $equipamiento->getIdUbicacionActual() ?></td>
        </tr>

    <?php endforeach; ?>

</table>

</body>
</html>