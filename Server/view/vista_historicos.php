<?php

require_once("../controller/HistoricoUbicacionController.php");

$controlador = new HistoricoUbicacionesController();

$historicos = $controlador->listar();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Histórico de ubicaciones</title>
</head>

<body>

<h1>Histórico de ubicaciones</h1>

<table border="1">
    <tr>
        <th>ID Histórico</th>
        <th>ID Equipamiento</th>
        <th>ID Ubicación</th>
        <th>Fecha inicio</th>
        <th>Fecha fin</th>
    </tr>

    <?php foreach ($historicos as $historico): ?>

        <tr>
            <td><?= $historico->getIdHistorico() ?></td>

            <td><?= $historico->getIdEquipamiento() ?></td>

            <td><?= $historico->getIdUbicacion() ?></td>

            <td>
                <?= $historico->getFechaInicio()->format('Y-m-d H:i:s') ?>
            </td>

            <td>
                <?php
                if ($historico->getFechaFin() !== null) {
                    echo $historico->getFechaFin()->format('Y-m-d H:i:s');
                } else {
                    echo "Actual";
                }
                ?>
            </td>
        </tr>

    <?php endforeach; ?>

</table>

</body>
</html>
