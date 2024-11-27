<?php

if (isset($_GET["id_usuarios"]) && ctype_digit($_GET["id_usuarios"])) {
    $param_id_usuarios = (int) trim($_GET["id_usuarios"]);
} else {
    header("location: error.php");
    exit();
}


require_once "config.php";


$sql = "SELECT * FROM usuarios WHERE id_usuarios = ?";

if ($stmt = mysqli_prepare($link, $sql)) {

    mysqli_stmt_bind_param($stmt, "i", $param_id_usuarios);


    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

            $nombre = $row["nombre"];
            $apellido = $row["apellido"];
            $edad = $row["edad"];
            $sexo = $row["sexo"];
            $estatus = $row["estatus"];
            $correo = $row["correo"];
            $direccion = $row["direccion"];
            $ocupacion = $row["ocupacion"];
        } else {

            header("location: error.php");
            exit();
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit();
    }


    mysqli_stmt_close($stmt);
} else {
    echo "Error al preparar la consulta.";
    exit();
}


mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ver Usuario</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper {
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Ver Usuario</h1>
                    </div>
                    <div class="form-group">
                        <label>Nombre</label>
                        <p class="form-control-static"><?php echo $nombre; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Apellido</label>
                        <p class="form-control-static"><?php echo $apellido; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Edad</label>
                        <p class="form-control-static"><?php echo $edad; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Sexo</label>
                        <p class="form-control-static"><?php echo $sexo; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Estatus</label>
                        <p class="form-control-static"><?php echo $estatus; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Correo</label>
                        <p class="form-control-static"><?php echo $correo; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <p class="form-control-static"><?php echo $direccion; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Ocupación</label>
                        <p class="form-control-static"><?php echo $ocupacion; ?></p>
                    </div>
                    <p><a href="index.php" class="btn btn-primary">Volver</a></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>