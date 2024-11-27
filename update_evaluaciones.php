<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once "config.php";


$id_usuario = $mes = $anio = $calificacion = $comentarios = "";
$id_usuario_err = $mes_err = $anio_err = $calificacion_err = $comentarios_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_evaluacion"]) && !empty($_POST["id_evaluacion"])) {
    $id_evaluacion = $_POST["id_evaluacion"];

    $id_usuario = trim($_POST["id_usuario"]);
    $mes = trim($_POST["mes"]);
    $anio = trim($_POST["anio"]);
    $calificacion = trim($_POST["calificacion"]);
    $comentarios = trim($_POST["comentarios"]);


    if (empty($id_usuario) || !ctype_digit($id_usuario))
        $id_usuario_err = "Por favor, ingrese un ID de usuario válido.";
    if (empty($mes) || !ctype_digit($mes) || $mes < 1 || $mes > 12)
        $mes_err = "Por favor, ingrese un mes válido (1-12).";
    if (empty($anio) || !ctype_digit($anio))
        $anio_err = "Por favor, ingrese un año válido.";
    if (empty($calificacion) || !is_numeric($calificacion) || $calificacion < 0 || $calificacion > 10)
        $calificacion_err = "Por favor, ingrese una calificación válida (0-10).";
    if (empty($comentarios))
        $comentarios_err = "Por favor, ingrese un comentario.";


    if (empty($id_usuario_err) && empty($mes_err) && empty($anio_err) && empty($calificacion_err) && empty($comentarios_err)) {
        $sql = "UPDATE evaluaciones SET id_usuario=?, mes=?, anio=?, calificacion=?, comentarios=? WHERE id_evaluacion=?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiidis", $id_usuario, $mes, $anio, $calificacion, $comentarios, $id_evaluacion);
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
    if (isset($_GET["id_evaluacion"]) && !empty(trim($_GET["id_evaluacion"]))) {
        $id_evaluacion = trim($_GET["id_evaluacion"]);
        $sql = "SELECT * FROM evaluaciones WHERE id_evaluacion = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id_evaluacion);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
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
    <title>Actualizar Evaluación</title>
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
                    <h2>Actualizar Evaluación</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($id_usuario_err)) ? 'has-error' : ''; ?>">
                            <label>ID Usuario</label>
                            <input type="text" name="id_usuario" class="form-control"
                                value="<?php echo htmlspecialchars($id_usuario); ?>">
                            <span class="help-block"><?php echo $id_usuario_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($mes_err)) ? 'has-error' : ''; ?>">
                            <label>Mes</label>
                            <input type="text" name="mes" class="form-control"
                                value="<?php echo htmlspecialchars($mes); ?>">
                            <span class="help-block"><?php echo $mes_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($anio_err)) ? 'has-error' : ''; ?>">
                            <label>Año</label>
                            <input type="text" name="anio" class="form-control"
                                value="<?php echo htmlspecialchars($anio); ?>">
                            <span class="help-block"><?php echo $anio_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($calificacion_err)) ? 'has-error' : ''; ?>">
                            <label>Calificación</label>
                            <input type="text" name="calificacion" class="form-control"
                                value="<?php echo htmlspecialchars($calificacion); ?>">
                            <span class="help-block"><?php echo $calificacion_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($comentarios_err)) ? 'has-error' : ''; ?>">
                            <label>Comentarios</label>
                            <textarea name="comentarios"
                                class="form-control"><?php echo htmlspecialchars($comentarios); ?></textarea>
                            <span class="help-block"><?php echo $comentarios_err; ?></span>
                        </div>
                        <input type="hidden" name="id_evaluacion"
                            value="<?php echo htmlspecialchars($id_evaluacion); ?>" />
                        <input type="submit" class="btn btn-primary" value="Actualizar">
                        <a href="index.php" class="btn btn-default">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>