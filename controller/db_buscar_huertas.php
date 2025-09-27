<?php
include '../config/db.php'; // Incluir la conexión
// Validar conexión
if ($conexion->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Conexión fallida: ' . $conexion->connect_error]);
    exit;
}

$termino = $_GET['q'] ?? '';

$resultado = [];
try {
    if ($termino !== '') {
        $stmt = $conexion->prepare("SELECT huerta FROM huertas WHERE huerta LIKE CONCAT('%', ?, '%') LIMIT 10");
        $stmt->bind_param("s", $termino);
        $stmt->execute();
        $res = $stmt->get_result();
    } else {
        $res = $conexion->query("SELECT huerta FROM huertas ORDER BY huerta");
    }
    while ($fila = $res->fetch_assoc()) {
        $resultado[] = $fila['huerta'];
    }
    header('Content-Type: application/json');
    echo json_encode($resultado);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
$conexion->close();
?>