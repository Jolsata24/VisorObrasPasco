<?php

require_once("../config/conexion.php");
require_once("validar.php");

$totalObras = $conexion->query("
SELECT COUNT(*) FROM obras
")->fetchColumn();

$ejecucion = $conexion->query("
SELECT COUNT(*) FROM obras
WHERE estado='ACTIVO'
")->fetchColumn();

$culminadas = $conexion->query("
SELECT COUNT(*) FROM obras
WHERE estado='CERRADO' 
")->fetchColumn();

$paralizadas = $conexion->query("
SELECT COUNT(*) FROM obras
WHERE estado='Paralizada'
")->fetchColumn();

?>

<!DOCTYPE html>
<html>
<head>

<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>
Dashboard - Pasco Transparente
</h2>

<hr>

<div class="row">

<div class="col-md-3">
<div class="card text-center">
<div class="card-body">
<h5>Total Obras</h5>
<h1><?= $totalObras ?></h1>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card text-center">
<div class="card-body">
<h5>En Ejecución</h5>
<h1><?= $ejecucion ?></h1>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card text-center">
<div class="card-body">
<h5>Culminadas</h5>
<h1><?= $culminadas ?></h1>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card text-center">
<div class="card-body">
<h5>Paralizadas</h5>
<h1><?= $paralizadas ?></h1>
</div>
</div>
</div>

<hr>

<div class="mt-3">

    <a href="obras.php" class="btn btn-success">
        Gestionar Obras
    </a>

    <a href="../logout.php" class="btn btn-danger">
        Cerrar Sesión
    </a>

</div>

</body>
</html>