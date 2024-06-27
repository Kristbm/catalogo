<?php
// Incluir el archivo de conexión a la base de datos
require 'conn.php';

// Consulta para obtener todos los pedidos con estado 1 (pendiente), ordenados por fecha más reciente
$sqlPedidos = "SELECT p.*, c.nombre AS nombre_cliente, c.cedula, c.telefono 
               FROM tbctlpedidos p
               INNER JOIN tbctlclientes c ON p.idcliente = c.id
               WHERE p.estado = 1 
               ORDER BY p.fechahora DESC";
$stmtPedidos = $pdo->query($sqlPedidos);
$pedidos = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Pedidos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Administración de Pedidos</h2>
    <div class="row">
        <?php 
        // Incluir el archivo de conexión a la base de datos
        require 'conn.php';

        foreach ($pedidos as $pedido): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pedido <?php echo $pedido['numero_pedido']; ?></h5>
                        <p class="card-text">Cliente: <?php echo $pedido['nombre_cliente']; ?></p>
                        <p class="card-text">Cédula: <?php echo $pedido['cedula']; ?></p>
                        <p class="card-text">Teléfono: <?php echo $pedido['telefono']; ?></p>
                        <p class="card-text">Fecha: <span class="badge badge-secondary"><?php echo $pedido['fechahora']; ?></span> </p>
                        <p class="card-text">Estado: Pendiente</p>
                        <!-- Detalles del pedido -->
                        <h6>Detalles del Pedido:</h6>
                        <?php 
                            $pedidoID = $pedido['numero_pedido'];
                            $sqlDetallesPedido = "SELECT producto, cantidad FROM tbctldetallespedido WHERE numero_pedido = :pedidoID";
                            $stmtDetallesPedido = $pdo->prepare($sqlDetallesPedido);
                            $stmtDetallesPedido->bindParam(':pedidoID', $pedidoID);
                            $stmtDetallesPedido->execute();
                            $detallesPedido = $stmtDetallesPedido->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($detallesPedido as $detalle): ?>
                                <p>- Producto: <?php echo $detalle['producto']; ?>, Cantidad: <?php echo $detalle['cantidad']; ?></p>
                        <?php endforeach; ?>
                        <!-- Botón para cambiar el estado del pedido -->
                        <button class="btn btn-success btn-cambiar-estado" data-id="<?php echo $pedido['id']; ?>">Cambiar Estado</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

    <script>
        $(document).ready(function() {
            // Cambiar estado del pedido al hacer clic en el botón "Cambiar Estado"
            $('.btn-cambiar-estado').click(function() {
                var pedidoID = $(this).data('id');
                $.post('est_pedido.php', { id: pedidoID }, function(response) {
                    if (response.success) {
                        alert('Estado del pedido cambiado correctamente.');
                        // Recargar la página para actualizar la lista de pedidos
                        location.reload();
                    } else {
                        alert('Error al cambiar el estado del pedido.');
                    }
                }, 'json');
            });
        });
    </script>
      <script>
    // Función para mostrar notificación
    function mostrarNotificacion(numeroPedido) {
      // Verificar si el navegador soporta las notificaciones
      if (!("Notification" in window)) {
        console.log("Este navegador no soporta notificaciones.");
        return;
      }

      // Verificar si las notificaciones están permitidas
      if (Notification.permission === "granted") {
        // Crear y mostrar la notificación
        var notificacion = new Notification("¡Nuevo pedido recibido!", {
          body: "Número de pedido: " + numeroPedido
        });
      } else if (Notification.permission !== "denied") {
        // Pedir permiso para mostrar notificaciones
        Notification.requestPermission(function (permission) {
          if (permission === "granted") {
            // Si se concede el permiso, mostrar la notificación
            var notificacion = new Notification("¡Nuevo pedido recibido!", {
              body: "Número de pedido: " + numeroPedido
            });
          }
        });
      }
    }

    // Función para verificar nuevos pedidos
    function verificarNuevosPedidos() {
      // Realizar una solicitud AJAX al servidor
      var xhr = new XMLHttpRequest();
      xhr.open('GET', 'verificar_nuevos_pedidos.php', true);
      xhr.onload = function() {
        if (xhr.status === 200) {
          var respuesta = JSON.parse(xhr.responseText);
          if (respuesta.nuevosPedidos) {
            // Mostrar notificación de nuevo pedido
            mostrarNotificacion(respuesta.numeroPedido);
          }
        }
      };
      xhr.send();
    }

    // Verificar nuevos pedidos cada 10 segundos
    setInterval(verificarNuevosPedidos, 10000);
  </script>
</body>
</html>
