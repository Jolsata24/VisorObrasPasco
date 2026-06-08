<!DOCTYPE html>
<html>

<head>

<title>Nueva Obra</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Registrar Obra</h2>

<hr>

<form action="guardar_obra.php" method="POST">

<div class="mb-3">

<label>Nombre de la Obra</label>

<input
type="text"
name="nombre_obra"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Presupuesto Total</label>

<input
type="number"
step="0.01"
name="presupuesto_total"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Fecha Inicio</label>

<input
type="date"
name="fecha_inicio"
class="form-control">

</div>

<div class="mb-3">

<label>Fecha Fin</label>

<input
type="date"
name="fecha_fin"
class="form-control">

</div>

<div class="mb-3">
    <label>Latitud</label>
    <input type="text"
           name="latitud"
           class="form-control">
</div>

<div class="mb-3">
    <label>Longitud</label>
    <input type="text"
           name="longitud"
           class="form-control">
</div>


<div class="mb-3">

<label>Estado</label>

<select name="estado" class="form-control">
    <option>ACTIVO</option>
    <option>CERRADO</option>
    <option>INACTIVO</option>
    <option>VIABLE</option>
</select>

</div>

<button class="btn btn-success">

Guardar

</button>

<a href="obras.php" class="btn btn-secondary">

Volver

</a>

</form>

</div>

</body>

</html>