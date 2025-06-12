<?php
include '../config/db.php'; // Incluir la conexión  
// Validar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$response = [
    'gramajes' => [],
    'tipos' => []
];

// Obtener gramajes
$gramajeQuery = $conexion->query("SELECT DISTINCT gramaje FROM gramaje ORDER BY gramaje");
while ($row = $gramajeQuery->fetch_assoc()) {
    $response['gramajes'][] = $row['gramaje'];
}

// Obtener tipos de corte
$tipoQuery = $conexion->query("SELECT DISTINCT tipo_corte FROM tipo_corte ORDER BY tipo_corte");
while ($row = $tipoQuery->fetch_assoc()) {
    $response['tipos'][] = $row['tipo_corte'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
