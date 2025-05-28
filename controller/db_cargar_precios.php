<?php
// obtener_precios.php
header('Content-Type: application/json');
include '../config/db_precios.php'; // Conexión a la BD

if ($conexion->connect_error) {
    echo json_encode(['error' => 'Conexión fallida']);
    exit;
}

function limpiarDatos($arr, $excluir = [], $sufijo = '') {
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
    $sql = "SELECT * FROM $tabla ORDER BY fecha DESC LIMIT 1";
    $res = mysqli_query($conexion, $sql);
    return mysqli_fetch_assoc($res);
}

$excluir = ['idnacional', 'fecha', 'tipo','idcanada','idjapon','id']; // ← Excluir también 'tipo'

$precios = [
    'usa' => [],
    'asia' => []
];

// USA: precios_global y nacional
$preciosGlobal = obtenerUltimoRegistro($conexion, 'precios_global');
$nacional = obtenerUltimoRegistro($conexion, 'nacional', 'idnacional');
if ($preciosGlobal) {
    $precios['usa'] += limpiarDatos($preciosGlobal, $excluir);
}
if ($nacional) {
    $precios['usa'] += limpiarDatos($nacional, $excluir);
}

// ASIA: japon, canada, nacional
$japon = obtenerUltimoRegistro($conexion, 'japon', 'idjapon');
$canada = obtenerUltimoRegistro($conexion, 'canada', 'idcanada');
$nacional = obtenerUltimoRegistro($conexion, 'nacional', 'idnacional'); // reutilizada

if ($japon) {
    $precios['asia'] += limpiarDatos($japon, $excluir);
}
if ($canada) {
    $precios['asia'] += limpiarDatos($canada, $excluir, 'canada');
}
if ($nacional) {
    $precios['asia'] += limpiarDatos($nacional, $excluir);
}

echo json_encode($precios, JSON_UNESCAPED_UNICODE);
