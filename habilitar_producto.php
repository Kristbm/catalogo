<?php
require 'conn.php'; // Reemplaza 'conn.php' con el nombre correcto de tu archivo de conexión

// Inicializamos las variables
$success = false;
$message = '';

// Verificamos si se recibió el código del producto y el estado de habilitación
if(isset($_POST['codigo']) && isset($_POST['estado'])) {
    // Obtenemos los valores enviados por AJAX
    $codigoProducto = $_POST['codigo'];
    $nuevoEstado = $_POST['estado'];

    // Realizamos la actualización en la base de datos
    $sql = "UPDATE tbadmproducto SET vista = :nuevoEstado WHERE codigo = :codigo";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nuevoEstado', $nuevoEstado);
    $stmt->bindParam(':codigo', $codigoProducto);

    if ($stmt->execute()) {
        // Devolvemos una respuesta de éxito si la actualización se realizó correctamente
        $success = true;
        $message = 'Producto actualizado correctamente.';
    } else {
        $message = 'Error al actualizar el producto.';
    }
} else {
    $message = 'No se proporcionó el código del producto o el estado de habilitación.';
}

// Devolvemos la respuesta como JSON
echo json_encode(array('success' => $success, 'message' => $message));
?>
