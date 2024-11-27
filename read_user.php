<?php

if (isset($_GET["id"]) && ctype_digit($_GET["id"])) {
    $param_id = (int) $_GET["id"];
} else {

    header("location: error.php");
    exit();
}


require_once "config.php";


$sql = "SELECT * FROM users WHERE id = ?";

if ($stmt = mysqli_prepare($link, $sql)) {

    mysqli_stmt_bind_param($stmt, "i", $param_id);


    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $row = mysqli_fetch_assoc($result);

            $username = $row["username"];
            $password = $row["password"];
            $created_at = $row["created_at"];
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
                        <h1>Detalles del Usuario</h1>
                    </div>
                    <div class="form-group">
                        <label>Nombre de Usuario</label>
                        <p class="form-control-static"><?php echo htmlspecialchars($username); ?></p>
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <p class="form-control-static"><?php echo htmlspecialchars($password); ?></p>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Creación</label>
                        <p class="form-control-static"><?php echo htmlspecialchars($created_at); ?></p>
                    </div>
                    <p><a href="index.php" class="btn btn-primary">Volver</a></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>