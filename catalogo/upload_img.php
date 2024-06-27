<?php
// Ruta donde se guardarán las imágenes
$uploadDir = 'img/';

// Nombre del archivo
$fileName = basename($_FILES['image']['name']);
$uploadFile = $uploadDir . $fileName;

// Mover el archivo cargado al directorio de imágenes
if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
    echo json_encode(array('status' => 'success', 'message' => 'Imagen cargada exitosamente.'));
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Error al cargar la imagen.'));
}
?>
