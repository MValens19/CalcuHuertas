<?php
require '../config/db.php';

// Validar conexión
if ($conexion->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Conexión fallida: ' . $conexion->connect_error]);
    exit;
}

// Crear directorio para subir fotos si no existe
$uploadDir = '../Uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Procesar FormData
if (!isset($_POST['data'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No se recibió el campo data']);
    exit;
}

$input = json_decode($_POST['data'], true);
if (json_last_error() !== JSON_ERROR_NONE) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error al decodificar JSON: ' . json_last_error_msg()]);
    exit;
}

// Validar campos requeridos
$requiredFields = ['fecha', 'productor', 'huerta', 'municipio', 'gramaje', 'tipo_corte', 'jefe_acopio', 'exportacion', 'promedio_estimado'];
foreach ($requiredFields as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        header('Content-Type: application/json');
        echo json_encode(['error' => "El campo $field es requerido"]);
        exit;
    }
}

// Manejar la foto si se subió
$photoPath = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['foto']['tmp_name'];
    $fileName = uniqid('foto_') . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $destPath = $uploadDir . $fileName;

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['foto']['type'], $allowedTypes) && $_FILES['foto']['size'] <= 5 * 1024 * 1024) {
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $photoPath = 'Uploads/' . $fileName;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al mover la foto']);
            exit;
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Archivo no válido o demasiado grande']);
        exit;
    }
}

try {
    $stmt = $conexion->prepare(
        "INSERT INTO estimaciones_precio 
        (fecha, productor, huerta, estimacion_cosecha, municipio, gramaje, tipo_corte, jefe_acopio, exportacion, observaciones, promedio_estimado, detalles, latitud, longitud, foto) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $detalles = json_encode($input['detalles']);
    $latitud = isset($input['latitud']) && is_numeric($input['latitud']) ? $input['latitud'] : null;
    $longitud = isset($input['longitud']) && is_numeric($input['longitud']) ? $input['longitud'] : null;

    $stmt->bind_param(
        "ssssssssssdssds",
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
        $detalles,
        $latitud,
        $longitud,
        $photoPath
    );

    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'OK']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Error al guardar la estimación: ' . $stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Excepción: ' . $e->getMessage()]);
}

$conexion->close();
?>