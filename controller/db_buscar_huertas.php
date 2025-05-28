<?php
include '../config/db.php'; // Incluir la conexión          
// Validar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$termino = $_GET['q'] ?? '';

$resultado = [];
if ($termino !== '') {
    $stmt = $conexion->prepare("SELECT huerta FROM huertas WHERE huerta LIKE CONCAT('%', ?, '%') LIMIT 10");
    $stmt->bind_param("s", $termino);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($fila = $res->fetch_assoc()) {
        $resultado[] = $fila['huerta'];
    }
}

echo json_encode($resultado);
?>
