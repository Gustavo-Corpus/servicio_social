<?php
session_start();

if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    http_response_code(401);
    echo json_encode(['autenticado' => false]);
    exit;
}

echo json_encode(['autenticado' => true]);
?>