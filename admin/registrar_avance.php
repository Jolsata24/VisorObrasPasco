<?php

$id_obra = $_GET['id'];

?>

<!DOCTYPE html>
<html>

<head>

<title>Registrar Avance</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Registrar Avance</h2>

<hr>

<form action="guardar_avance.php" method="POST">

<input
type="hidden"
name="id_obra"
value="<?= $id_obra ?>">

<div class="mb-3">

<label>Fecha</label>

<input
type="date"
name="fecha_avance"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Avance Físico (%)</label>

<input
type="number"
step="0.01"
name="avance_fisico"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Avance Financiero (%)</label>

<input
type="number"
step="0.01"
name="avance_financiero"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Observaciones</label>

<textarea
name="observaciones"
class="form-control">
</textarea>

</div>

<button class="btn btn-primary">

Guardar

</button>

</form>

</div>

</body>
</html>