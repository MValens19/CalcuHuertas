<?php
require '../config/db.php'; // Incluir la conexión
// Validar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$input = json_decode(file_get_contents('php://input'), true);

$stmt = $conexion->prepare("INSERT INTO estimaciones_precio 
    (fecha, productor, huerta, estimacion_cosecha, municipio, gramaje, tipo_corte, jefe_acopio, exportacion, observaciones, promedio_estimado, detalles) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "ssssssssssds",
    $input['fecha'],
    $input['productor'],
    $input['huerta'],
    $input['estimacion_cosecha'],
    $input['municipio'],
    $input['gramaje'],
    $input['tipo_corte'],
    $input['jefe_acopio'],
    $input['exportacion'],
    $input['observaciones'],
    $input['promedio_estimado'],
    json_encode($input['detalles'])
);

echo $stmt->execute() ? 'OK' : 'ERROR';

$stmt->close();
$conexion->close();
?>
