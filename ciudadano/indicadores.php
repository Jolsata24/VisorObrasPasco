<?php

require_once("../config/conexion.php");

/*
------------------------------------
OBRAS POR ESTADO
------------------------------------
*/

$sqlEstados = $conexion->query("
SELECT estado, COUNT(*) cantidad
FROM obras
GROUP BY estado
");

$estados = [];
$cantidadesEstados = [];

while($fila = $sqlEstados->fetch(PDO::FETCH_ASSOC)){

    $estados[] = $fila['estado'];
    $cantidadesEstados[] = $fila['cantidad'];

}

/*
------------------------------------
INVERSIÓN POR ESTADO
------------------------------------
*/

$sqlInversion = $conexion->query("
SELECT estado,
SUM(presupuesto_total) total
FROM obras
GROUP BY estado
");

$estadosInv = [];
$montosInv = [];

while($fila = $sqlInversion->fetch(PDO::FETCH_ASSOC)){

    $estadosInv[] = $fila['estado'];
    $montosInv[] = $fila['total'];

}

/*
------------------------------------
TOP 10 OBRAS
------------------------------------
*/

$sqlTop = $conexion->query("
SELECT
nombre_obra,
presupuesto_total
FROM obras
ORDER BY presupuesto_total DESC
LIMIT 10
");

$nombresTop = [];
$montosTop = [];

while($fila = $sqlTop->fetch(PDO::FETCH_ASSOC)){

    $nombresTop[] = $fila['nombre_obra'];
    $montosTop[] = $fila['presupuesto_total'];

}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Indicadores Ciudadanos</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<div class="container mt-4">

<h2 class="text-center">

📊 Indicadores de Obras Públicas

</h2>

<hr>

<div class="row">

<div class="col-md-6">

<div class="card">

<div class="card-header">

Obras por Estado

</div>

<div class="card-body">

<canvas id="graficoEstados"></canvas>

</div>

</div>

</div>

<div class="col-md-6">

<div class="card">

<div class="card-header">

Inversión por Estado

</div>

<div class="card-body">

<canvas id="graficoInversion"></canvas>

</div>

</div>

</div>

</div>

<br>

<div class="card">

<div class="card-header">

Top 10 Obras con Mayor Presupuesto

</div>

<div class="card-body">

<canvas id="graficoTop"></canvas>

</div>

</div>

</div>

<script>

new Chart(

document.getElementById('graficoEstados'),

{
type:'pie',

data:{

labels:
<?= json_encode($estados) ?>,

datasets:[{

data:
<?= json_encode($cantidadesEstados) ?>

}]

}

});

</script>

<script>

new Chart(

document.getElementById('graficoInversion'),

{
type:'bar',

data:{

labels:
<?= json_encode($estadosInv) ?>,

datasets:[{

label:'Monto S/',

data:
<?= json_encode($montosInv) ?>

}]

}

});

</script>

<script>

new Chart(

document.getElementById('graficoTop'),

{
type:'bar',

data:{

labels:
<?= json_encode($nombresTop) ?>,

datasets:[{

label:'Presupuesto',

data:
<?= json_encode($montosTop) ?>

}]

},

options:{

indexAxis:'y'

}

});

</script>

</body>
</html>