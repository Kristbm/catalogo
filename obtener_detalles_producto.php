<?php
// Incluir el archivo de conexión a la base de datos
require 'conn.php';

// Verificar si se recibió el ID del pedido
if(isset($_POST['numeroPedido'])) {
    $pedidoID = $_POST['numeroPedido'];

    // Consulta para obtener los detalles del pedido
    $sqlDetallesPedido = "SELECT * FROM tbctldetallespedido WHERE numero_pedido = :pedidoID";
    $stmtDetallesPedido = $pdo->prepare($sqlDetallesPedido);
    $stmtDetallesPedido->bindParam(':pedidoID', $pedidoID);
    $stmtDetallesPedido->execute();
    $detallesPedido = $stmtDetallesPedido->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si se obtuvieron detalles del pedido
    if($detallesPedido) {
        // Construir la cadena de detalles del pedido
        $detalles = "Detalles del Pedido:<br>";
        foreach($detallesPedido as $detalle) {
            $detalles .= "- Producto: {$detalle['producto']}, Cantidad: {$detalle['cantidad']}<br>";
        }

        // Enviar los detalles del pedido como respuesta
        $response = array(
            'success' => true,
            'detalles' => $detalles
        );
    } else {
        // Si no se encontraron detalles del pedido
        $response = array(
            'success' => false,
            'message' => 'No se encontraron detalles para este pedido.'
        );
    }
} else {
    // Si no se recibió el ID del pedido
    $response = array(
        'success' => false,
        'message' => 'No se recibió el ID del pedido.'
    );
}

// Devolver la respuesta como JSON
echo json_encode($response);
?>
