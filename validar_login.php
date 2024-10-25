<?php
session_start();

// Datos de conexión (manteniendo tus credenciales)
$servidor = "localhost";
$usuario = "root";
$password = "Xblaster16";
$baseDatos = "servicio_social";

// Crear conexión
$conn = new mysqli($servidor, $usuario, $password, $baseDatos);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'mensaje' => "Conexión fallida: " . $conn->connect_error]));
}

// Obtener los datos del formulario
$username = $_POST['username'];
$password = $_POST['password'];

// Prevenir inyección SQL
$username = $conn->real_escape_string($username);

// Consulta SQL para obtener el usuario
$sql = "SELECT * FROM users WHERE username = '$username'";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    // Verificar la contraseña
    if (password_verify($password, $fila['password'])) {
        // Establecer variables de sesión
        $_SESSION['usuario_id'] = $fila['id']; // Asegúrate de que tu tabla tenga una columna 'id'
        $_SESSION['username'] = $username;
        $_SESSION['autenticado'] = true;
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'mensaje' => 'Contraseña incorrecta']);
    }
} else {
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no encontrado']);
}

$conn->close();
?>