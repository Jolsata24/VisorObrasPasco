<?php

require_once("../config/conexion.php");

$id = $_GET['id'];

$sql = $conexion->prepare("
SELECT *
FROM obras
WHERE id_obra = ?
");

$sql->execute([$id]);

$obra = $sql->fetch(PDO::FETCH_ASSOC);

?>