<?php
require_once 'conexion.php';

// Habilitar visualización de errores para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Log los datos recibidos
    $input = file_get_contents('php://input');
    error_log("Datos raw recibidos: " . $input);

    $data = json_decode($input, true);
    $evaluacionId = isset($_GET['id']) ? intval($_GET['id']) : null;

    error_log("ID de evaluación recibido: " . $evaluacionId);
    error_log("Datos decodificados: " . print_r($data, true));

    // Verificar ID de evaluación
    if (!$evaluacionId) {
        throw new Exception('ID de evaluación no proporcionado o inválido');
    }

    // Verificar existencia de la evaluación antes de actualizar
    $checkQuery = "SELECT id_evaluacion FROM evaluaciones WHERE id_evaluacion = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['id' => $evaluacionId]);

    if (!$checkStmt->fetch()) {
        throw new Exception('La evaluación con ID ' . $evaluacionId . ' no existe');
    }

    // Validar y convertir datos
    $mes = isset($data['mes']) ? intval($data['mes']) : null;
    $anio = isset($data['anio']) ? intval($data['anio']) : null;
    $calificacion = isset($data['calificacion']) ? floatval($data['calificacion']) : null;
    $comentarios = isset($data['comentarios']) ? trim($data['comentarios']) : '';

    // Validaciones de datos
    if (!$mes || $mes < 1 || $mes > 12) {
        throw new Exception('Mes inválido: ' . $mes);
    }
    if (!$anio || $anio < 2000 || $anio > 2100) {
        throw new Exception('Año inválido: ' . $anio);
    }
    if ($calificacion === null || $calificacion < 0 || $calificacion > 10) {
        throw new Exception('Calificación inválida: ' . $calificacion);
    }

    // Construir y ejecutar la consulta de actualización
    $query = "UPDATE evaluaciones
              SET mes = :mes,
                  anio = :anio,
                  calificacion = :calificacion,
                  comentarios = :comentarios
              WHERE id_evaluacion = :id";

    $stmt = $pdo->prepare($query);
    $params = [
        'mes' => $mes,
        'anio' => $anio,
        'calificacion' => $calificacion,
        'comentarios' => $comentarios,
        'id' => $evaluacionId
    ];

    error_log("Ejecutando query con parámetros: " . print_r($params, true));

    $result = $stmt->execute($params);
    $rowsAffected = $stmt->rowCount();

    error_log("Resultado de la actualización: " . ($result ? 'true' : 'false'));
    error_log("Filas afectadas: " . $rowsAffected);

    if ($result && $rowsAffected > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Evaluación actualizada exitosamente',
            'rowsAffected' => $rowsAffected
        ]);
    } else {
        throw new Exception('No se realizó ninguna actualización en la base de datos');
    }

} catch (Exception $e) {
    error_log("Error en actualización: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>