<?php
// Datos de conexión (ajusta con tus credenciales)
$servidor = "localhost";
$usuario = "root";
$password = "Xblaster16"; // Cambia a la contraseña que usas para conectar a la base de datos
$baseDatos = "servicio_social"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servidor, $usuario, $password, $baseDatos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario
$username = $_POST['username'];
$password = $_POST['password'];

// Prevenir inyección SQL
$username = $conn->real_escape_string($username);

// Encriptar la contraseña
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Verificar si el usuario ya existe
$sql_check = "SELECT * FROM users WHERE username = '$username'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows > 0) {
    $mensaje = "El nombre de usuario ya está en uso. Inténtelo con otro nombre.";
    $ruta = "login.html";
    $textoBoton = "Volver a Registrarse";
} else {
    // Insertar el nuevo usuario en la base de datos
    $sql_insert = "INSERT INTO users (username, password, created_at) VALUES ('$username', '$passwordHash', NOW())";

    if ($conn->query($sql_insert) === TRUE) {
        $mensaje = "Usuario registrado exitosamente. Ahora puede iniciar sesión.";
        $ruta = "login.html";
        $textoBoton = "Iniciar Sesión";
    } else {
        $mensaje = "Error al registrar el usuario: " . $conn->error;
        $ruta = "login.html";
        $textoBoton = "Volver";
    }
}

// Cerrar conexión
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de Registro</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="mensaje-container">
        <h2><?php echo $mensaje; ?></h2>
        <a href="<?php echo $ruta; ?>" class="boton"><?php echo $textoBoton; ?></a>
    </div>
</body>

</html>