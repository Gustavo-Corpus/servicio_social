<?php
require_once 'conexion.php';
require_once 'obtener_datos.php';

header('Content-Type: application/json');

try {
    $estados = obtenerEstados();
    echo json_encode($estados);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>