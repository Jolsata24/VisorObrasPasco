<?php

require_once("../config/conexion.php");

$id = $_POST['id_obra'];

$sql = $conexion->prepare("

UPDATE obras

SET

nombre_obra=?,
presupuesto_total=?,
fecha_inicio=?,
fecha_fin=?,
estado=?

WHERE id_obra=?

");

$sql->execute([

$_POST['nombre_obra'],
$_POST['presupuesto_total'],
$_POST['fecha_inicio'],
$_POST['fecha_fin'],
$_POST['estado'],
$id

]);

header("Location: obras.php");