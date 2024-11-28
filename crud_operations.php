<?php
require_once 'conexion.php';

// Asegurarse de que siempre enviamos JSON
header('Content-Type: application/json');

function crearEmpleado($datos)
{
    global $pdo;
    try {
        // Validar campos requeridos
        $camposRequeridos = [
            'nombre',
            'apellido',
            'sexo',
            'correo',
            'edad',
            'direccion',
            'ocupacion',
            'id_departamento',
            'id_estado'
        ];
        foreach ($camposRequeridos as $campo) {
            if (!isset($datos[$campo]) || empty($datos[$campo])) {
                throw new Exception("El campo $campo es requerido");
            }
        }

        // Convertir el valor de sexo a minúsculas
        $sexo = ($datos['sexo'] === 'M') ? 'masculino' : 'femenino';

        // Asegurar que estatus esté en minúsculas
        $estatus = strtolower($datos['estatus'] ?? 'activo');

        $query = "INSERT INTO usuarios (
                  nombre, apellido, sexo, estatus, correo, edad, direccion,
                  ocupacion, id_departamento, id_estado
              ) VALUES (
                  :nombre, :apellido, :sexo, :estatus, :correo, :edad, :direccion,
                  :ocupacion, :id_departamento, :id_estado
              )";

        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'sexo' => $sexo,
            'estatus' => $estatus,
            'correo' => $datos['correo'],
            'edad' => $datos['edad'],
            'direccion' => $datos['direccion'],
            'ocupacion' => $datos['ocupacion'],
            'id_departamento' => $datos['id_departamento'],
            'id_estado' => $datos['id_estado']
        ]);

        if ($result) {
            return [
                'success' => true,
                'id' => $pdo->lastInsertId(),
                'message' => 'Empleado creado exitosamente'
            ];
        } else {
            throw new Exception("Error al ejecutar la consulta");
        }
    } catch (PDOException $e) {
        error_log("Error al crear empleado: " . $e->getMessage());
        return [
            'success' => false,
            'error' => "Error al crear el empleado: " . $e->getMessage()
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function actualizarEmpleado($id, $datos)
{
    // Tu código actual de actualizarEmpleado
}

function eliminarEmpleado($id)
{
    // Tu código actual de eliminarEmpleado
}

function obtenerDepartamentos()
{
    global $pdo;
    try {
        $query = "SELECT id_departamento, nombre_departamento FROM departamentos ORDER BY nombre_departamento";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return [
            'success' => true,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    } catch (PDOException $e) {
        error_log("Error al obtener departamentos: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error al obtener departamentos'
        ];
    }
}

// Manejar las solicitudes GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'getDepartamentos':
            echo json_encode(obtenerDepartamentos());
            break;
        default:
            echo json_encode([
                'success' => false,
                'error' => 'Acción GET no válida'
            ]);
    }
    exit;
}

// Manejar las solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        switch ($_GET['action']) {
            case 'create':
                echo json_encode(crearEmpleado($data));
                break;

            case 'update':
                if (!isset($_GET['id'])) {
                    throw new Exception("ID de empleado no proporcionado");
                }
                echo json_encode(actualizarEmpleado($_GET['id'], $data));
                break;

            case 'delete':
                if (!isset($_GET['id'])) {
                    throw new Exception("ID de empleado no proporcionado");
                }
                echo json_encode(eliminarEmpleado($_GET['id']));
                break;

            default:
                throw new Exception("Acción POST no válida");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// Si llegamos aquí, significa que no se proporcionó una acción válida
echo json_encode([
    'success' => false,
    'error' => 'Método no permitido o acción no especificada'
]);
?>