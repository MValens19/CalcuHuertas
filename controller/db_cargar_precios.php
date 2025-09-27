<?php
// db_cargar_precios.php
header('Content-Type: application/json');
include '../config/db_precios.php'; // Conexión a la BD

if ($conexion->connect_error) {
    echo json_encode(['error' => 'Conexión fallida: ' . $conexion->connect_error]);
    exit;
}

function limpiarDatos($arr, $excluir = [], $sufijo = '') {
    if (!$arr) return [];
    $resultado = [];
    foreach ($arr as $clave => $valor) {
        if (in_array($clave, $excluir)) continue;
        $key = trim(strtolower($clave));
        if ($sufijo !== '') {
            $key .= "_$sufijo";
        }
        $resultado[$key] = is_numeric($valor) ? floatval($valor) : $valor;
    }
    return $resultado;
}

function obtenerUltimoRegistro($conexion, $tabla, $idCampo = 'id') {
    // Verificar si la tabla existe
    $checkTable = mysqli_query($conexion, "SHOW TABLES LIKE '$tabla'");
    if (mysqli_num_rows($checkTable) === 0) {
        return null;
    }

    $sql = "SELECT * FROM $tabla ORDER BY fecha DESC LIMIT 1";
    $res = mysqli_query($conexion, $sql);
    if (!$res) {
        return null;
    }
    return mysqli_fetch_assoc($res);
}

$excluir = ['id', 'idnacional', 'idjapon', 'idcanada', 'fecha', 'tipo'];

$precios = [
    'usa' => [],
    'asia' => []
];

// USA: precios_global y nacional
$preciosGlobal = obtenerUltimoRegistro($conexion, 'precios_global');
$nacional = obtenerUltimoRegistro($conexion, 'nacional', 'idnacional');
if ($preciosGlobal) {
    $precios['usa'] = array_merge($precios['usa'], limpiarDatos($preciosGlobal, $excluir));
}
if ($nacional) {
    $precios['usa'] = array_merge($precios['usa'], limpiarDatos($nacional, $excluir));
}

// ASIA: japon, canada, nacional
$japon = obtenerUltimoRegistro($conexion, 'japon', 'idjapon');
$canada = obtenerUltimoRegistro($conexion, 'canada', 'idcanada');
$nacional = obtenerUltimoRegistro($conexion, 'nacional', 'idnacional');
if ($japon) {
    $precios['asia'] = array_merge($precios['asia'], limpiarDatos($japon, $excluir));
}
if ($canada) {
    $precios['asia'] = array_merge($precios['asia'], limpiarDatos($canada, $excluir, 'canada'));
}
if ($nacional) {
    $precios['asia'] = array_merge($precios['asia'], limpiarDatos($nacional, $excluir));
}

// Validar si los precios están vacíos
if (empty($precios['usa']) && empty($precios['asia'])) {
    echo json_encode(['error' => 'No se encontraron precios en la base de datos']);
    exit;
}

echo json_encode($precios, JSON_UNESCAPED_UNICODE);
$conexion->close();
?>