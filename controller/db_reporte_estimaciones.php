<?php
include '../config/db.php';
if ($conexion->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Conexión fallida: ' . $conexion->connect_error]);
    exit;
}
$inicio = $_GET['inicio'] ?? '';
$fin = $_GET['fin'] ?? '';
$stmt = $conexion->prepare("SELECT fecha, AVG(promedio_estimado) as promedio_estimado FROM estimaciones WHERE fecha BETWEEN ? AND ? GROUP BY fecha");
$stmt->bind_param('ss', $inicio, $fin);
$stmt->execute();
$result = $stmt->get_result();
$datos = [];
while ($row = $result->fetch_assoc()) {
    $datos[] = $row;
}
header('Content-Type: application/json');
echo json_encode($datos);
$conexion->close();
?>