<?php
include '../config/db.php';
if ($conexion->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Conexión fallida: ' . $conexion->connect_error]);
    exit;
}
$result = $conexion->query("SELECT fecha, productor, promedio_estimado, latitud, longitud FROM estimaciones ORDER BY fecha DESC");
$estimaciones = [];
while ($row = $result->fetch_assoc()) {
    $estimaciones[] = $row;
}
header('Content-Type: application/json');
echo json_encode($estimaciones);
$conexion->close();
?>