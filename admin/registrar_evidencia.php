<?php

$id_obra = $_GET['id'];

?>

<!DOCTYPE html>
<html>

<head>

<title>Registrar Evidencia</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Nueva Evidencia</h2>

<hr>

<form
action="guardar_evidencia.php"
method="POST"
enctype="multipart/form-data">

<input
type="hidden"
name="id_obra"
value="<?= $id_obra ?>">

<div class="mb-3">

<label>Título</label>

<input
type="text"
name="titulo"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Descripción</label>

<textarea
name="descripcion"
class="form-control">
</textarea>

</div>

<div class="mb-3">

<label>Fecha</label>

<input
type="date"
name="fecha_evidencia"
class="form-control">

</div>

<div class="mb-3">

<label>Fotografía</label>

<input
type="file"
name="foto"
class="form-control"
required>

</div>

<button class="btn btn-success">

Guardar

</button>

</form>

</div>

</body>

</html>