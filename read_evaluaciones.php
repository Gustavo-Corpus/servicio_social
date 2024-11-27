<?php
if (isset($_GET["id_evaluacion"]) && ctype_digit($_GET["id_evaluacion"])) {
    $param_id_evaluacion = (int) $_GET["id_evaluacion"];
} else {

    header("location: error.php");
    exit();
}


require_once "config.php";


$sql = "SELECT * FROM evaluaciones WHERE id_evaluacion = ?";

if ($stmt = mysqli_prepare($link, $sql)) {

    mysqli_stmt_bind_param($stmt, "i", $param_id_evaluacion);


    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $row = mysqli_fetch_assoc($result);


            $id_usuario = $row["id_usuario"];
            $mes = $row["mes"];
            $anio = $row["anio"];
            $calificacion = $row["calificacion"];
            $comentarios = $row["comentarios"];
        } else {

            header("location: error.php");
            exit();
        }
    } else {
        echo "Error al ejecutar la consulta. Intenta de nuevo más tarde.";
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
    <title>Ver Evaluación</title>
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
                        <h1>Detalles de la Evaluación</h1>
                    </div>
                    <div class="form-group">
                        <label>ID Usuario</label>
                        <p class="form-control-static"><?php echo $id_usuario; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Mes</label>
                        <p class="form-control-static"><?php echo $mes; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Año</label>
                        <p class="form-control-static"><?php echo $anio; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Calificación</label>
                        <p class="form-control-static"><?php echo $calificacion; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Comentarios</label>
                        <p class="form-control-static"><?php echo $comentarios; ?></p>
                    </div>
                    <p><a href="index.php" class="btn btn-primary">Volver</a></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>