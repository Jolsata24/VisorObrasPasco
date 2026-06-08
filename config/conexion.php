<?php

$host = "localhost";
$dbname = "ObrasPublicas";
$user = "root";
$password = "123456";

try {

    $conexion = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $password
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch(PDOException $e){

    die("Error de conexión: " . $e->getMessage());

}

?>