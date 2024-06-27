<?php
// Consulta para verificar si hay nuevos pedidos
$sqlVerificarPedidos = "SELECT numero_pedido FROM tbctlpedidos WHERE estado = 1 ORDER BY fechahora DESC LIMIT 1";
$stmtVerificarPedidos = $pdo->query($sqlVerificarPedidos);
$ultimoPedido = $stmtVerificarPedidos->fetch(PDO::FETCH_ASSOC);

if ($ultimoPedido) {
    // Si se encontró un nuevo pedido
    $ultimoNumeroPedido = $ultimoPedido['numero_pedido'];
    $ultimoNumeroPedidoEnviado = isset($_COOKIE['ultimo_numero_pedido']) ? $_COOKIE['ultimo_numero_pedido'] : null;

    if ($ultimoNumeroPedidoEnviado != $ultimoNumeroPedido) {
        // Si el último número de pedido enviado es diferente al último número de pedido encontrado
        // Establecer la cookie con el nuevo número de pedido
        setcookie('ultimo_numero_pedido', $ultimoNumeroPedido, time() + 3600, '/'); // Cookie válida por una hora
        // Devolver el número de pedido como respuesta
        $response = array(
            'nuevosPedidos' => true,
            'numeroPedido' => $ultimoNumeroPedido
        );
    } else {
        // Si el último número de pedido enviado es igual al último número de pedido encontrado
        // No hay nuevos pedidos
        $response = array(
            'nuevosPedidos' => false
        );
    }
} else {
    // Si no se encontró ningún nuevo pedido
    $response = array(
        'nuevosPedidos' => false
    );
}

// Devolver la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
