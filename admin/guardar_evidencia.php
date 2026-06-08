<?php

require_once("../config/conexion.php");

session_start();

$nombreFoto = time() . "_" . $_FILES['foto']['name'];

$rutaTemporal = $_FILES['foto']['tmp_name'];

move_uploaded_file(
    $rutaTemporal,
    "../uploads/evidencias/".$nombreFoto
);

$sql = $conexion->prepare("

INSERT INTO evidencias
(
id_obra,
titulo,
descripcion,
foto,
fecha_evidencia,
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
$_POST['titulo'],
$_POST['descripcion'],
$nombreFoto,
$_POST['fecha_evidencia'],
$_SESSION['id_usuario']

]);

header(
"Location: evidencias.php?id=".$_POST['id_obra']
);