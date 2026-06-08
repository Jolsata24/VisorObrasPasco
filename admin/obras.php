<?php

require_once("../config/conexion.php");
require_once("validar.php");

$sql = $conexion->prepare("
SELECT *
FROM obras
ORDER BY id_obra DESC
");

$sql->execute();

$obras = $sql->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

<title>Obras</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Gestión de Obras</h2>

<hr>

<a href="registrar_obra.php" class="btn btn-primary">
Nueva Obra
</a>

<br><br>

<table class="table table-bordered">

<thead>

<tr>

<th>ID</th>
<th>Nombre</th>
<th>Presupuesto</th>
<th>Estado</th>
<th>Acciones</th>
<th>Avance</th>
</tr>

</thead>

<tbody>

<?php foreach($obras as $obra){ ?>

<tr>

<td><?= $obra['id_obra'] ?></td>

<td><?= $obra['nombre_obra'] ?></td>

<td>S/ <?= number_format($obra['presupuesto_total'],2) ?></td>

<td><?= $obra['estado'] ?></td>

<td>

<?= $obra['ultimo_avance_fisico'] ?>%

</td>
<td>

<td>

<a href="avances.php?id=<?= $obra['id_obra'] ?>"
class="btn btn-info btn-sm">
Avances
</a>

<a href="evidencias.php?id=<?= $obra['id_obra'] ?>"
class="btn btn-success btn-sm">
Evidencias
</a>

<a href="editar_obra.php?id=<?= $obra['id_obra'] ?>"
class="btn btn-warning btn-sm">
Editar
</a>

<a href="eliminar_obra.php?id=<?= $obra['id_obra'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('¿Eliminar obra?')">
Eliminar
</a>

</td>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</body>

</html>