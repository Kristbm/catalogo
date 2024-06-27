<?php
require 'conn.php';

// Verifica si se envió el formulario de pedido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica si se recibieron los datos del cliente
    if (isset($_POST['nombre']) && isset($_POST['cedula']) && isset($_POST['telefono'])) {
        // Recupera los datos del cliente
        $nombre = $_POST['nombre'];
        $cedula = $_POST['cedula'];
        $telefono = $_POST['telefono'];

        // Verifica si el cliente ya existe en la base de datos
        $sqlClienteExistente = "SELECT id FROM tbctlclientes WHERE cedula = :cedula";
        $stmtClienteExistente = $pdo->prepare($sqlClienteExistente);
        $stmtClienteExistente->bindParam(':cedula', $cedula);
        $stmtClienteExistente->execute();
        $clienteExistente = $stmtClienteExistente->fetch(PDO::FETCH_ASSOC);

        if ($clienteExistente) {
            // Si el cliente ya existe, obtiene su ID existente
            $clienteID = $clienteExistente['id'];
        } else {
            // Si el cliente no existe, lo inserta en la tabla de clientes
            $sqlNuevoCliente = "INSERT INTO tbctlclientes (nombre, cedula, telefono) VALUES (:nombre, :cedula, :telefono)";
            $stmtNuevoCliente = $pdo->prepare($sqlNuevoCliente);
            $stmtNuevoCliente->bindParam(':nombre', $nombre);
            $stmtNuevoCliente->bindParam(':cedula', $cedula);
            $stmtNuevoCliente->bindParam(':telefono', $telefono);
            $stmtNuevoCliente->execute();

            // Obtiene el ID del nuevo cliente insertado
            $clienteID = $pdo->lastInsertId();
        }

        // Verifica si se recibieron los detalles del pedido
        if (isset($_POST['productos'])) {
            // Recupera los detalles del pedido
            $productos = json_decode($_POST['productos'], true);

            // Inicializa el total del pedido
            $totalPedido = 0;

            // Genera un número de pedido único
            $numeroPedido = generarNumeroPedido();

            // Inserta los detalles del pedido en la tabla de pedidos y tbctldetallespedido
            $sqlPedido = "INSERT INTO tbctlpedidos (numero_pedido, idcliente, fechahora, estado) 
                          VALUES (:numero_pedido, :idcliente, :fechahora, :estado)";
            $stmtPedido = $pdo->prepare($sqlPedido);
            $stmtPedido->bindParam(':numero_pedido', $numeroPedido);
            $stmtPedido->bindParam(':idcliente', $clienteID);
            // Establecer la zona horaria predeterminada
            date_default_timezone_set('America/Caracas');
            $fechaPedido = date('Y-m-d H:i:s'); // Fecha y hora actual en el formato 'Y-m-d H:i:s'
            $estado = 1; // Estado pendiente
            $stmtPedido->bindParam(':fechahora', $fechaPedido);
            $stmtPedido->bindParam(':estado', $estado);
            $stmtPedido->execute();

            // Obtiene el ID del nuevo pedido insertado
            $pedidoID = $pdo->lastInsertId();

            // Inserta los detalles del pedido en la tabla tbctldetallespedido
            foreach ($productos as $producto) {
                $nombreProducto = $producto['nombre'];
                $cantidad = $producto['cantidad'];
                $precioUnitario = $producto['precio'];
                $precioMayor = $producto['preciomayor'];
                $subtotal = $cantidad * $precioUnitario;
                $totalPedido += $subtotal;

                // Inserta los detalles del producto en la tabla tbctldetallespedido
                $sqlDetallesPedido = "INSERT INTO tbctldetallespedido (numero_pedido, producto, cantidad) 
                                      VALUES (:numero_pedido, :producto, :cantidad)";
                $stmtDetallesPedido = $pdo->prepare($sqlDetallesPedido);
                $stmtDetallesPedido->bindParam(':numero_pedido', $numeroPedido);
                $stmtDetallesPedido->bindParam(':producto', $nombreProducto);
                $stmtDetallesPedido->bindParam(':cantidad', $cantidad);
                $stmtDetallesPedido->execute();
            }

            // Guardar imagen de factura en la carpeta 'pedidos'
            $imagenFactura = generarFactura($numeroPedido, $productos, $totalPedido);

            // Envía un mensaje de éxito
            $response = array(
                'success' => true,
                'message' => 'Pedido procesado exitosamente.',
                'numero_pedido' => $numeroPedido,
                'total_pedido' => $totalPedido,
                'imagen_factura' => $imagenFactura
            );
        } else {
            // Si no se recibieron los detalles del pedido, envía un mensaje de error
            $response = array(
                'success' => false,
                'message' => 'No se recibieron los detalles del pedido.'
            );
        }
    } else {
        // Si no se recibieron los datos del cliente, envía un mensaje de error
        $response = array(
            'success' => false,
            'message' => 'Por favor, completa todos los campos requeridos.'
        );
    }
} else {
    // Si no se envió el formulario, envía un mensaje de error
    $response = array(
        'success' => false,
        'message' => 'Error al procesar el pedido. Inténtalo de nuevo más tarde.'
    );
}

// Devuelve la respuesta como JSON
echo json_encode($response);

// Función para generar un número de pedido único
function generarNumeroPedido() {
    return uniqid('PEDIDO_', true);
}

// Función para generar la imagen de la factura
function generarFactura($numeroPedido, $productos, $totalPedido) {
    // Crea el contenido de la factura
    $factura = "Número de Pedido: $numeroPedido\n\n";
    $factura .= "Detalles del Pedido:\n";
    foreach ($productos as $producto) {
        $factura .= "- Producto: {$producto['nombre']}, Cantidad: {$producto['cantidad']}, Precio Unitario: {$producto['precio']}\n";
    }
    $factura .= "\nTotal del Pedido: $totalPedido";

    // Genera un nombre único para la imagen de la factura
    $nombreImagen = uniqid('factura_', true) . '.txt';

    // Guarda la factura en la carpeta 'pedidos'
    file_put_contents('pedidos/' . $nombreImagen, $factura);

    // Devuelve el nombre de la imagen de la factura
    return $nombreImagen;
}
?>
