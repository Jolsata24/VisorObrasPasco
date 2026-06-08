<?php

require_once("../config/conexion.php");

$buscar = $_GET['buscar'] ?? '';

$sql = $conexion->prepare("
SELECT *
FROM obras
WHERE nombre_obra LIKE ?
ORDER BY nombre_obra
");

$sql->execute([
"%$buscar%"
]);

$obras = $sql->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

<title>Consulta de Obras</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Consulta Ciudadana de Obras</h2>

<form method="GET">

<input
type="text"
name="buscar"
class="form-control"
placeholder="Buscar obra">

<br>

<button class="btn btn-primary">
Buscar
</button>

</form>

<hr>

<table class="table table-bordered">

<thead>

<tr>

<th>Obra</th>
<th>Estado</th>
<th>Avance</th>
<th>Detalle</th>

</tr>

</thead>

<tbody>

<?php foreach($obras as $obra){ ?>
<tr>
    <td><?= htmlspecialchars($obra['nombre_obra']) ?></td>
    <td><?= htmlspecialchars($obra['estado']) ?></td>
    <td><?= htmlspecialchars($obra['ultimo_avance_fisico']) ?>%</td>
    <td>
        <a href="detalle_obra.php?id=<?= $obra['id_obra'] ?>" class="btn btn-info">
    Ver
</a>
    </td>
</tr>
<?php } ?>

</tbody>

</table>

</div>

</body>

</html>