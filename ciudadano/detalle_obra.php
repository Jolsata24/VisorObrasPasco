<?php

require_once("../config/conexion.php");

$id = $_GET['id'];

$sql = $conexion->prepare("
SELECT *
FROM obras
WHERE id_obra=?
");

$sql->execute([$id]);

$obra = $sql->fetch();

$sqlAvances = $conexion->prepare("
SELECT *
FROM avances
WHERE id_obra=?
ORDER BY fecha_avance DESC
");

$sqlAvances->execute([$id]);

$avances = $sqlAvances->fetchAll();

$sqlFotos = $conexion->prepare("
SELECT *
FROM evidencias
WHERE id_obra=?
ORDER BY fecha_evidencia DESC
");

$sqlFotos->execute([$id]);

$fotos = $sqlFotos->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>

<title><?= $obra['nombre_obra'] ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2><?= $obra['nombre_obra'] ?></h2>

<hr>

<p>

<strong>Estado:</strong>
<?= $obra['estado'] ?>

</p>

<p>

<strong>Presupuesto:</strong>

S/
<?= number_format($obra['presupuesto_total'],2) ?>

</p>

<p>

<strong>Avance Actual:</strong>

<?= $obra['ultimo_avance_fisico'] ?>%

</p>

<hr>

<h4>Historial de Avances</h4>

<table class="table table-bordered">

<tr>

<th>Fecha</th>
<th>Físico</th>
<th>Financiero</th>

</tr>

<?php foreach($avances as $a){ ?>

<tr>

<td><?= $a['fecha_avance'] ?></td>

<td><?= $a['avance_fisico'] ?>%</td>

<td><?= $a['avance_financiero'] ?>%</td>

</tr>

<?php } ?>

</table>

<hr>

<h4>Evidencias Fotográficas</h4>

<div class="row">

<?php foreach($fotos as $foto){ ?>

<div class="col-md-4">

<div class="card mb-3">

<img
src="../uploads/evidencias/<?= $foto['foto'] ?>"
class="card-img-top">

<div class="card-body">

<h6><?= $foto['titulo'] ?></h6>

<p><?= $foto['descripcion'] ?></p>

</div>

</div>

</div>

<?php } ?>

</div>

</div>

</body>
</html>