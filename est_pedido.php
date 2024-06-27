<?php
// Incluir el archivo de conexión a la base de datos
require 'conn.php';
// Verificar si se recibió el ID del pedido por POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {

    // Obtener el ID del pedido desde el POST
    $pedidoID = $_POST['id'];

    // Actualizar el estado del pedido a procesado (estado = 2)
    $sqlProcesarPedido = "UPDATE tbctlpedidos SET estado = 2 WHERE id = :id";
    $stmtProcesarPedido = $pdo->prepare($sqlProcesarPedido);
    $stmtProcesarPedido->bindParam(':id', $pedidoID);
    
    // Ejecutar la consulta
    if ($stmtProcesarPedido->execute()) {
        // Devolver respuesta de éxito
        echo json_encode(array('success' => true));
    } else {
        // Devolver respuesta de error
        echo json_encode(array('success' => false));
    }
} else {
    // Devolver respuesta de error si no se recibió el ID del pedido
    echo json_encode(array('success' => false));
}
?>
