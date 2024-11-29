<?php
require_once 'conexion.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validar datos requeridos
    if (!isset($data['id_usuario'], $data['mes'], $data['anio'], $data['calificacion'])) {
        throw new Exception('Faltan datos requeridos');
    }

    // Validar si ya existe una evaluación para ese mes y año
    $queryCheck = "
        SELECT id_evaluacion
        FROM evaluaciones
        WHERE id_usuario = :id_usuario
        AND mes = :mes
        AND anio = :anio
    ";
    $stmt = $pdo->prepare($queryCheck);
    $stmt->execute([
        'id_usuario' => $data['id_usuario'],
        'mes' => $data['mes'],
        'anio' => $data['anio']
    ]);

    if ($stmt->fetch()) {
        throw new Exception('Ya existe una evaluación para este mes y año');
    }

    // Insertar nueva evaluación
    $query = "
        INSERT INTO evaluaciones (
            id_usuario, mes, anio, calificacion, comentarios
        ) VALUES (
            :id_usuario, :mes, :anio, :calificacion, :comentarios
        )
    ";

    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([
        'id_usuario' => $data['id_usuario'],
        'mes' => $data['mes'],
        'anio' => $data['anio'],
        'calificacion' => $data['calificacion'],
        'comentarios' => $data['comentarios'] ?? null
    ]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Evaluación guardada exitosamente'
        ]);
    } else {
        throw new Exception('Error al guardar la evaluación');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>