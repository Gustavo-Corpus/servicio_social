<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once "config.php";


$nombre = $apellido = $edad = $sexo = $estatus = $correo = $direccion = $ocupacion = "";
$nombre_err = $apellido_err = $edad_err = $sexo_err = $estatus_err = $correo_err = $direccion_err = $ocupacion_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_usuarios"]) && !empty($_POST["id_usuarios"])) {
    $id = $_POST["id_usuarios"];

    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $edad = trim($_POST["edad"]);
    $sexo = trim($_POST["sexo"]);
    $estatus = trim($_POST["estatus"]);
    $correo = trim($_POST["correo"]);
    $direccion = trim($_POST["direccion"]);
    $ocupacion = trim($_POST["ocupacion"]);


    if (empty($nombre))
        $nombre_err = "Por favor, ingrese un nombre.";
    if (empty($apellido))
        $apellido_err = "Por favor, ingrese un apellido.";
    if (empty($edad) || !ctype_digit($edad))
        $edad_err = "Por favor, ingrese una edad válida.";
    if (empty($sexo))
        $sexo_err = "Por favor, seleccione un sexo.";
    if (empty($estatus))
        $estatus_err = "Por favor, seleccione un estatus.";
    if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL))
        $correo_err = "Por favor, ingrese un correo válido.";
    if (empty($direccion))
        $direccion_err = "Por favor, ingrese una dirección.";
    if (empty($ocupacion))
        $ocupacion_err = "Por favor, ingrese una ocupación.";


    if (empty($nombre_err) && empty($apellido_err) && empty($edad_err) && empty($sexo_err) && empty($estatus_err) && empty($correo_err) && empty($direccion_err) && empty($ocupacion_err)) {
        $sql = "UPDATE usuarios SET nombre=?, apellido=?, edad=?, sexo=?, estatus=?, correo=?, direccion=?, ocupacion=? WHERE id_usuarios=?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssisssssi", $nombre, $apellido, $edad, $sexo, $estatus, $correo, $direccion, $ocupacion, $id);
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
    if (isset($_GET["id_usuarios"]) && !empty(trim($_GET["id_usuarios"]))) {
        $id = trim($_GET["id_usuarios"]);
        $sql = "SELECT * FROM usuarios WHERE id_usuarios = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
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
    <title>Actualizar Registro</title>
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
                    <h2>Actualizar Registro</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($nombre_err)) ? 'has-error' : ''; ?>">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control"
                                value="<?php echo htmlspecialchars($nombre); ?>">
                            <span class="help-block"><?php echo $nombre_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($apellido_err)) ? 'has-error' : ''; ?>">
                            <label>Apellido</label>
                            <input type="text" name="apellido" class="form-control"
                                value="<?php echo htmlspecialchars($apellido); ?>">
                            <span class="help-block"><?php echo $apellido_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($edad_err)) ? 'has-error' : ''; ?>">
                            <label>Edad</label>
                            <input type="text" name="edad" class="form-control"
                                value="<?php echo htmlspecialchars($edad); ?>">
                            <span class="help-block"><?php echo $edad_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($sexo_err)) ? 'has-error' : ''; ?>">
                            <label>Sexo</label>
                            <select name="sexo" class="form-control">
                                <option value="Masculino" <?php echo ($sexo == "Masculino") ? "selected" : ""; ?>>
                                    Masculino</option>
                                <option value="Femenino" <?php echo ($sexo == "Femenino") ? "selected" : ""; ?>>Femenino
                                </option>
                            </select>
                            <span class="help-block"><?php echo $sexo_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($estatus_err)) ? 'has-error' : ''; ?>">
                            <label>Estatus</label>
                            <input type="text" name="estatus" class="form-control"
                                value="<?php echo htmlspecialchars($estatus); ?>">
                            <span class="help-block"><?php echo $estatus_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($correo_err)) ? 'has-error' : ''; ?>">
                            <label>Correo</label>
                            <input type="email" name="correo" class="form-control"
                                value="<?php echo htmlspecialchars($correo); ?>">
                            <span class="help-block"><?php echo $correo_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($direccion_err)) ? 'has-error' : ''; ?>">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-control"
                                value="<?php echo htmlspecialchars($direccion); ?>">
                            <span class="help-block"><?php echo $direccion_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($ocupacion_err)) ? 'has-error' : ''; ?>">
                            <label>Ocupación</label>
                            <input type="text" name="ocupacion" class="form-control"
                                value="<?php echo htmlspecialchars($ocupacion); ?>">
                            <span class="help-block"><?php echo $ocupacion_err; ?></span>
                        </div>
                        <input type="hidden" name="id_usuarios" value="<?php echo htmlspecialchars($id); ?>" />
                        <input type="submit" class="btn btn-primary" value="Actualizar">
                        <a href="index.php" class="btn btn-default">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>