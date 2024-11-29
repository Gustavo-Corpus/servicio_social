<?php
require_once 'conexion.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id_usuario'])) {
        throw new Exception('ID de usuario no proporcionado');
    }

    $query = "
        SELECT
            id_evaluacion,
            mes,
            anio,
            calificacion,
            comentarios
        FROM evaluaciones
        WHERE id_usuario = :id_usuario
        ORDER BY anio DESC, mes DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['id_usuario' => $_GET['id_usuario']]);
    $evaluaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($evaluaciones);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>