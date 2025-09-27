<?php
include '../config/db.php'; // Incluir la conexión

// Validar conexión
if ($conexion->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Conexión fallida: ' . $conexion->connect_error]);
    exit;
}

$response = [
    'gramajes' => [],
    'tipos' => []
];

try {
    // Verificar si las tablas existen
    $tableCheck = $conexion->query("SHOW TABLES LIKE 'gramaje'");
    if ($tableCheck->num_rows === 0) {
        throw new Exception('La tabla gramaje no existe');
    }
    $tableCheck = $conexion->query("SHOW TABLES LIKE 'tipo_corte'");
    if ($tableCheck->num_rows === 0) {
        throw new Exception('La tabla tipo_corte no existe');
    }

    // Obtener gramajes
    $gramajeQuery = $conexion->query("SELECT DISTINCT gramaje FROM gramaje ORDER BY gramaje");
    if ($gramajeQuery) {
        while ($row = $gramajeQuery->fetch_assoc()) {
            $response['gramajes'][] = $row['gramaje'];
        }
    } else {
        throw new Exception('Error en la consulta de gramajes: ' . $conexion->error);
    }

    // Obtener tipos de corte
    $tipoQuery = $conexion->query("SELECT DISTINCT tipo_corte FROM tipo_corte ORDER BY tipo_corte");
    if ($tipoQuery) {
        while ($row = $tipoQuery->fetch_assoc()) {
            $response['tipos'][] = $row['tipo_corte'];
        }
    } else {
        throw new Exception('Error en la consulta de tipos de corte: ' . $conexion->error);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}

$conexion->close();
?>