<?php
session_start();
require_once("config/conexion.php");

if($_POST){

    $usuario = $_POST['usuario'];
    $password = md5($_POST['password']);

    $sql = $conexion->prepare("
        SELECT *
        FROM usuarios
        WHERE usuario = ?
        AND password = ?
        AND estado='Activo'
    ");

    $sql->execute([$usuario,$password]);

    $resultado = $sql->fetch();

    if($resultado){

        $_SESSION['id_usuario'] = $resultado['id_usuario'];
        $_SESSION['usuario'] = $resultado['usuario'];
        $_SESSION['rol'] = $resultado['rol'];

        header("Location: admin/dashboard.php");

    }else{

        $mensaje = "Usuario o contraseña incorrectos";

    }

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Pasco Transparente</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-4">

<div class="card">

<div class="card-header text-center">
<h3>Pasco Transparente</h3>
</div>

<div class="card-body">

<?php if(isset($mensaje)){ ?>
<div class="alert alert-danger">
<?= $mensaje ?>
</div>
<?php } ?>

<form method="POST">

<label>Usuario</label>
<input type="text" name="usuario" class="form-control">

<br>

<label>Contraseña</label>
<input type="password" name="password" class="form-control">

<br>

<button class="btn btn-primary w-100">
Ingresar
</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>
</html>