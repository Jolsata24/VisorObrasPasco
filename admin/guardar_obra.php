<?php

require_once("../config/conexion.php");

$nombre_obra = $_POST['nombre_obra'];
$presupuesto_total = $_POST['presupuesto_total'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];
$estado = $_POST['estado'];

$sql = $conexion->prepare("

INSERT INTO obras
(
nombre_obra,
presupuesto_total,
fecha_inicio,
fecha_fin,
estado,
latitud,
longitud
)

VALUES
(
?,
?,
?,
?,
?
)

");

$sql->execute([
$nombre_obra,
$presupuesto_total,
$fecha_inicio,
$fecha_fin,
$estado,
$_POST['latitud'],
$_POST['longitud']
]);

header("Location: obras.php");