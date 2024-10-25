<?php
require_once 'conexion.php';

function obtenerEstados() {
    global $pdo;
    try {
        $query = "SELECT id_estado, estado FROM estados ORDER BY estado";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error al obtener estados: " . $e->getMessage());
        throw new Exception("Error al obtener la lista de estados");
    }
}

function obtenerEmpleadosPorEstado($idEstado) {
  global $pdo;
  try {
      // Si el ID es "todos", obtener todos los empleados
      if ($idEstado === 'todos') {
          $query = "
              SELECT
                  u.id_usuarios,
                  u.nombre,
                  u.apellido,
                  u.ocupacion,
                  ROUND(AVG(e.calificacion), 1) as promedio_calificacion
              FROM
                  usuarios u
                  LEFT JOIN evaluaciones e ON u.id_usuarios = e.id_usuario
              GROUP BY
                  u.id_usuarios, u.nombre, u.apellido, u.ocupacion
              ORDER BY
                  u.apellido, u.nombre
          ";
          $stmt = $pdo->prepare($query);
          $stmt->execute();
      } else {
          // Consulta original para un estado específico
          $query = "
              SELECT
                  u.id_usuarios,
                  u.nombre,
                  u.apellido,
                  u.ocupacion,
                  ROUND(AVG(e.calificacion), 1) as promedio_calificacion
              FROM
                  usuarios u
                  LEFT JOIN evaluaciones e ON u.id_usuarios = e.id_usuario
              WHERE
                  u.id_estado = :idEstado
              GROUP BY
                  u.id_usuarios, u.nombre, u.apellido, u.ocupacion
              ORDER BY
                  u.apellido, u.nombre
          ";
          $stmt = $pdo->prepare($query);
          $stmt->execute(['idEstado' => $idEstado]);
      }
      return $stmt->fetchAll();
  } catch (PDOException $e) {
      error_log("Error al obtener empleados: " . $e->getMessage());
      throw new Exception("Error al obtener la lista de empleados");
  }
}

function obtenerDatosUsuario($username) {
    global $pdo;
    try {
        $query = "
            SELECT
                u.username,
                u.nombre,
                u.apellido,
                u.profile_pic
            FROM
                users u
            WHERE
                u.username = :username
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute(['username' => $username]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            return [
                'success' => true,
                'username' => $userData['nombre'] . ' ' . $userData['apellido'],
                'profile_pic' => $userData['profile_pic'] ?? null
            ];
        } else {
            throw new Exception("Usuario no encontrado");
        }
    } catch (PDOException $e) {
        error_log("Error al obtener datos del usuario: " . $e->getMessage());
        throw new Exception("Error al obtener datos del usuario");
    }
}

function obtenerEstadisticasEmpleados() {
  global $pdo;
  try {
      // Distribución de empleados por estado
      $queryDistribucion = "
          SELECT
              e.estado,
              COUNT(u.id_usuarios) as cantidad_empleados,
              ROUND(AVG(ev.calificacion), 2) as promedio_calificacion
          FROM estados e
          LEFT JOIN usuarios u ON e.id_estado = u.id_estado
          LEFT JOIN evaluaciones ev ON u.id_usuarios = ev.id_usuario
          GROUP BY e.estado
          ORDER BY cantidad_empleados DESC
      ";

      $stmt = $pdo->prepare($queryDistribucion);
      $stmt->execute();
      $distribucion = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Estadísticas generales
      $queryGenerales = "
          SELECT
              COUNT(DISTINCT u.id_usuarios) as total_empleados,
              ROUND(AVG(ev.calificacion), 2) as promedio_general,
              COUNT(DISTINCT e.id_estado) as total_estados
          FROM usuarios u
          LEFT JOIN evaluaciones ev ON u.id_usuarios = ev.id_usuario
          LEFT JOIN estados e ON u.id_estado = e.id_estado
      ";

      $stmt = $pdo->prepare($queryGenerales);
      $stmt->execute();
      $generales = $stmt->fetch(PDO::FETCH_ASSOC);

      return [
          'distribucion' => $distribucion,
          'generales' => $generales
      ];
  } catch (PDOException $e) {
      error_log("Error al obtener estadísticas: " . $e->getMessage());
      throw new Exception("Error al obtener estadísticas");
  }
}

// Manejar la solicitud AJAX
if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    try {
        switch ($_GET['action']) {
            case 'getEmployees':
                if (isset($_GET['estado'])) {
                    echo json_encode(obtenerEmpleadosPorEstado($_GET['estado']));
                } else {
                    throw new Exception("ID de estado no proporcionado");
                }
                break;

            case 'getUserData':
                session_start();
                if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
                    throw new Exception("Usuario no autenticado");
                }
                echo json_encode(obtenerDatosUsuario($_SESSION['username']));
                break;

            default:
                throw new Exception("Acción no válida");
                case 'getStats':
                  echo json_encode(obtenerEstadisticasEmpleados());
                  break;
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
  ?>