<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "Xblaster16", "servicio_social");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener todos los usuarios con sus promedios de calificación
$sql = "
    SELECT u.id_usuarios, u.nombre, u.apellido, u.ocupacion,
           IFNULL(AVG(e.calificacion), 'Sin evaluaciones') AS promedio_calificacion
    FROM usuarios u
    LEFT JOIN evaluaciones e ON u.id_usuarios = e.id_usuario
    GROUP BY u.id_usuarios
";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Crear un nuevo archivo Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Todos los Empleados");

// Encabezados de la tabla
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Nombre');
$sheet->setCellValue('C1', 'Apellido');
$sheet->setCellValue('D1', 'Puesto');
$sheet->setCellValue('E1', 'Promedio Calificaciones');

// Rellenar el archivo con los datos
$fila = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $fila, $row['id_usuarios']);
    $sheet->setCellValue('B' . $fila, $row['nombre']);
    $sheet->setCellValue('C' . $fila, $row['apellido']);
    $sheet->setCellValue('D' . $fila, $row['ocupacion'] ?? 'No especificado');
    $sheet->setCellValue('E' . $fila, $row['promedio_calificacion']);
    $fila++;
}

// Descargar el archivo Excel
$writer = new Xlsx($spreadsheet);
$nombreArchivo = "Todos_Empleados.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
$writer->save('php://output');
exit;

?>
