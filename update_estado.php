<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once "config.php";


$estado = "";
$estado_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_estado"]) && !empty($_POST["id_estado"])) {
    $id_estado = $_POST["id_estado"];


    $estado = trim($_POST["estado"]);
    if (empty($estado)) {
        $estado_err = "Por favor, ingrese un estado.";
    }


    if (empty($estado_err)) {
        $sql = "UPDATE estados SET estado=? WHERE id_estado=?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $estado, $id_estado);
            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                echo "Error al actualizar el registro.";
            }
        }
        mysqli_stmt_close($stmt);
    }
} else {
    if (isset($_GET["id_estado"]) && !empty(trim($_GET["id_estado"]))) {
        $id_estado = trim($_GET["id_estado"]);
        $sql = "SELECT * FROM estados WHERE id_estado = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id_estado);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    $estado = $row["estado"];
                } else {
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Error al cargar los datos.";
            }
        }
        mysqli_stmt_close($stmt);
    } else {
        header("location: error.php");
        exit();
    }
}
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Actualizar Estado</title>
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
                    <h2>Actualizar Estado</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($estado_err)) ? 'has-error' : ''; ?>">
                            <label>Estado</label>
                            <input type="text" name="estado" class="form-control"
                                value="<?php echo htmlspecialchars($estado); ?>">
                            <span class="help-block"><?php echo $estado_err; ?></span>
                        </div>
                        <input type="hidden" name="id_estado" value="<?php echo htmlspecialchars($id_estado); ?>" />
                        <input type="submit" class="btn btn-primary" value="Actualizar">
                        <a href="index.php" class="btn btn-default">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>