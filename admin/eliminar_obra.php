<?php

require_once("../config/conexion.php");

$id = $_GET['id'];

$sql = $conexion->prepare("
DELETE FROM obras
WHERE id_obra=?
");

$sql->execute([$id]);

header("Location: obras.php");