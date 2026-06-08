<?php

require_once("../config/conexion.php");
require_once("validar.php");

$id_obra = $_GET['id'];

$sqlObra = $conexion->prepare("
SELECT * FROM obras
WHERE id_obra=?
");

$sqlObra->execute([$id_obra]);

$obra = $sqlObra->fetch();

$sql = $conexion->prepare("
SELECT *
FROM evidencias
WHERE id_obra=?
ORDER BY fecha_evidencia DESC
");

$sql->execute([$id_obra]);

$evidencias = $sql->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

<title>Evidencias</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2><?= $obra['nombre_obra'] ?></h2>

<hr>

<a
href="registrar_evidencia.php?id=<?= $id_obra ?>"
class="btn btn-primary">

Nueva Evidencia

</a>

<a href="obras.php"
class="btn btn-secondary">

Volver

</a>

<hr>

<div class="row">

<?php foreach($evidencias as $e){ ?>

<div class="col-md-4 mb-4">

<div class="card">

<img
src="../uploads/evidencias/<?= $e['foto'] ?>"
class="card-img-top"
style="height:250px; object-fit:cover;">

<div class="card-body">

<h5><?= $e['titulo'] ?></h5>

<p><?= $e['descripcion'] ?></p>

<small>

<?= $e['fecha_evidencia'] ?>

</small>

</div>

</div>

</div>

<?php } ?>

</div>

</div>

</body>

</html>