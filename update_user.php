<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once "config.php";


$username = $password = $created_at = "";
$username_err = $password_err = $created_at_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && !empty($_POST["id"])) {
    $id = $_POST["id"];

    
    $username = trim($_POST["username"]);
    if (empty($username)) {
        $username_err = "Por favor, ingrese un nombre de usuario.";
    }

    
    $password = trim($_POST["password"]);
    if (empty($password)) {
        $password_err = "Por favor, ingrese una contraseña.";
    }

    
    $created_at = trim($_POST["created_at"]);
    if (empty($created_at)) {
        $created_at_err = "Por favor, ingrese una fecha de creación.";
    } elseif (!DateTime::createFromFormat('Y-m-d H:i:s', $created_at)) {
        $created_at_err = "Por favor, ingrese una fecha válida en formato 'YYYY-MM-DD HH:MM:SS'.";
    }

   
    if (empty($username_err) && empty($password_err) && empty($created_at_err)) {
     
        $sql = "UPDATE users SET username = ?, password = ?, created_at = ? WHERE id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            
            mysqli_stmt_bind_param($stmt, "sssi", $param_username, $param_password, $param_created_at, $param_id);

            
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Encriptar contraseña
            $param_created_at = $created_at;
            $param_id = $id;

          
            if (mysqli_stmt_execute($stmt)) {
                /
                header("location: index.php");
                exit();
            } else {
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }

           
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
} else {
    
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id = trim($_GET["id"]);

        
        $sql = "SELECT * FROM users WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            $param_id = $id;

            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    
                    $username = $row["username"];
                    $password = $row["password"]; // No se muestra la contraseña por seguridad
                    $created_at = $row["created_at"];
                } else {
                    
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }

            
            mysqli_stmt_close($stmt);
        }
    } else {
        
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Actualizar Usuario</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style>
        .wrapper {
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Actualizar Usuario</h2>
        <p>Edite los valores necesarios y envíe el formulario para actualizar el registro.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Nombre de Usuario</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" value="">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($created_at_err)) ? 'has-error' : ''; ?>">
                <label>Fecha de Creación</label>
                <input type="text" name="created_at" class="form-control" value="<?php echo $created_at; ?>">
                <span class="help-block"><?php echo $created_at_err; ?></span>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="submit" class="btn btn-primary" value="Actualizar">
            <a href="index.php" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</body>

</html>