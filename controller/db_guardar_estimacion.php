<?php
require '../config/db.php'; // Incluir la conexión

// Validar conexión
if ($conexion->connect_error) {
    die(json_encode(['error' => 'Conexión fallida: ' . $conexion->connect_error]));
}

// Crear directorio para subir fotos si no existe
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Procesar FormData
$input = json_decode($_POST['data'], true); // Obtener el JSON desde FormData
$photoPath = null;

// Manejar la foto si se subió
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['foto']['tmp_name'];
    $fileName = uniqid('foto_') . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $destPath = $uploadDir . $fileName;

    // Validar tipo y tamaño (máximo 5MB, solo imágenes)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['foto']['type'], $allowedTypes) && $_FILES['foto']['size'] <= 5 * 1024 * 1024) {
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $photoPath = 'uploads/' . $fileName; // Path relativo para la DB
        } else {
            echo json_encode(['error' => 'Error al mover la foto']);
            exit;
        }
    } else {
        echo json_encode(['error' => 'Archivo no válido o demasiado grande']);
        exit;
    }
}

// Preparar la consulta con las nuevas columnas
$stmt = $conexion->prepare("INSERT INTO estimaciones_precio 
    (fecha, productor, huerta, estimacion_cosecha, municipio, gramaje, tipo_corte, jefe_acopio, exportacion, observaciones, promedio_estimado, detalles, latitud, longitud, foto) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Vincular parámetros
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
    json_encode($input['detalles']),
    $input['latitud'],
    $input['longitud'],
    $photoPath
);

// Ejecutar y responder
if ($stmt->execute()) {
    echo json_encode(['status' => 'OK']);
} else {
    echo json_encode(['error' => 'Error al guardar la estimación: ' . $stmt->error]);
}

$stmt->close();
$conexion->close();
?>