<?php

require_once("../config/conexion.php");

$sql = $conexion->prepare("
    SELECT
        id_obra,
        nombre_obra,
        estado,
        ultimo_avance_fisico,
        latitud,
        longitud
    FROM obras
    WHERE latitud IS NOT NULL
    AND longitud IS NOT NULL
");

$sql->execute();

$obras = $sql->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Mapa de Obras - Pasco Transparente</title>

<link rel="stylesheet"
href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f5f5f5;
}

#mapa{
    height:700px;
    width:100%;
    border:1px solid #ccc;
}

.titulo{
    text-align:center;
    margin-top:20px;
    margin-bottom:20px;
}

</style>

</head>

<body>

<div class="container-fluid">

    <div class="titulo">

        <h2>🗺️ Mapa de Obras Públicas</h2>

        <p>
            Gobierno Regional de Pasco - Pasco Transparente
        </p>

        <a href="index.php" class="btn btn-secondary">
            Inicio
        </a>

        <a href="obras.php" class="btn btn-primary">
            Consultar Obras
        </a>

    </div>

    <div id="mapa"></div>

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>

// Centro aproximado de Pasco

var mapa = L.map('mapa').setView(
    [-10.6833, -76.2567],
    8
);

L.tileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    {
        attribution:'© OpenStreetMap'
    }
).addTo(mapa);

<?php foreach($obras as $obra){ ?>

L.marker([
    <?= $obra['latitud'] ?>,
    <?= $obra['longitud'] ?>
])

.addTo(mapa)

.bindPopup(`

    <div style="min-width:250px;">

        <h6>
            <?= htmlspecialchars($obra['nombre_obra']) ?>
        </h6>

        <hr>

        <strong>Estado:</strong>

        <?= htmlspecialchars($obra['estado']) ?>

        <br>

        <strong>Avance:</strong>

        <?= $obra['ultimo_avance_fisico'] ?>%

        <br><br>

        <a
        href="detalle_obra.php?id=<?= $obra['id_obra'] ?>"
        class="btn btn-sm btn-primary">

        Ver detalle

        </a>

    </div>

`);

<?php } ?>

</script>

</body>

</html>