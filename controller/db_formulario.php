<?php
// Conexión a la base de datos (ajusta con tus datos reales)
include '../config/db.php'; // Incluir la conexión

// Validar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener municipios
$municipios = [];
$consultaMunicipios = $conexion->query("SELECT DISTINCT Municipio FROM municipios ORDER BY Municipio");
while ($fila = $consultaMunicipios->fetch_assoc()) {
    $municipios[] = $fila['Municipio'];
}

// Obtener jefes de acopio
$jefes = [];
$consultaJefes = $conexion->query("SELECT idjefes, Nombre FROM jefes_de_acopio ORDER BY Nombre");
while ($fila = $consultaJefes->fetch_assoc()) {
    $jefes[] = [
        'id' => $fila['idjefes'],
        'nombre' => $fila['Nombre']
    ];
}

// Salida JSON
echo json_encode([
    'municipios' => $municipios,
    'jefes' => $jefes
]);
?>

