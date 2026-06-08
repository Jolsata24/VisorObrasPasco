<?php

require_once("../config/conexion.php");
require_once("validar.php");

$id_obra = $_GET['id'];

$sqlObra = $conexion->prepare("
SELECT *
FROM obras
WHERE id_obra=?
");

$sqlObra->execute([$id_obra]);

$obra = $sqlObra->fetch();

$sql = $conexion->prepare("
SELECT *
FROM avances
WHERE id_obra=?
ORDER BY fecha_avance DESC
");

$sql->execute([$id_obra]);

$avances = $sql->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

<title>Avances</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2><?= $obra['nombre_obra'] ?></h2>

<hr>

<a
href="registrar_avance.php?id=<?= $id_obra ?>"
class="btn btn-success">

Registrar Avance

</a>

<a href="obras.php" class="btn btn-secondary">

Volver

</a>

<br><br>

<table class="table table-bordered">

<thead>

<tr>

<th>Fecha</th>
<th>Físico %</th>
<th>Financiero %</th>
<th>Observación</th>

</tr>

</thead>

<tbody>

<?php foreach($avances as $avance){ ?>

<tr>

<td><?= $avance['fecha_avance'] ?></td>

<td><?= $avance['avance_fisico'] ?>%</td>

<td><?= $avance['avance_financiero'] ?>%</td>

<td><?= $avance['observaciones'] ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</body>
</html>