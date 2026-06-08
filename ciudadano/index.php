<?php

require_once("../config/conexion.php");

$totalObras = $conexion->query("
SELECT COUNT(*) FROM obras
")->fetchColumn();

$ejecucion = $conexion->query("
SELECT COUNT(*) FROM obras
WHERE estado='En Ejecucion'
")->fetchColumn();

$culminadas = $conexion->query("
SELECT COUNT(*) FROM obras
WHERE estado='Culminada'
")->fetchColumn();

$inversion = $conexion->query("
SELECT SUM(presupuesto_total)
FROM obras
")->fetchColumn();

?>
<!DOCTYPE html>
<html>
<head>

<title>Pasco Transparente</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<nav class="navbar navbar-dark bg-dark">
<div class="container">

<a class="navbar-brand" href="#">
PASCO TRANSPARENTE
</a>

<a href="obras.php" class="btn btn-light">
Consultar Obras
</a>

</div>
</nav>

<div class="container mt-5">

<h1 class="text-center">
Observatorio Ciudadano de Obras Públicas
</h1>

<a href="mapa.php"
class="btn btn-success">

Mapa de Obras

</a>

<a href="indicadores.php"
class="btn btn-success">

Indicadores

</a>

<hr>

<div class="row">

<div class="col-md-3">
<div class="card text-center">
<div class="card-body">
<h5>Obras Totales</h5>
<h2><?= $totalObras ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card text-center">
<div class="card-body">
<h5>En Ejecución</h5>
<h2><?= $ejecucion ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card text-center">
<div class="card-body">
<h5>Culminadas</h5>
<h2><?= $culminadas ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card text-center">
<div class="card-body">
<h5>Inversión Total</h5>
<h6>
S/
<?= number_format($inversion,2) ?>
</h6>
</div>
</div>
</div>

</div>

</div>

</body>
</html>