<?php

require_once("../config/conexion.php");
session_start();

$sql = $conexion->prepare("

INSERT INTO avances
(
id_obra,
fecha_avance,
avance_fisico,
avance_financiero,
observaciones,
usuario_registro
)

VALUES
(
?,
?,
?,
?,
?,
?
)

");

$sql->execute([

$_POST['id_obra'],
$_POST['fecha_avance'],
$_POST['avance_fisico'],
$_POST['avance_financiero'],
$_POST['observaciones'],
$_SESSION['id_usuario']

]);

header("Location: avances.php?id=".$_POST['id_obra']);