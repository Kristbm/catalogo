<?php
include 'conn.php';

// ID del producto enviado por AJAX
$productId = $_POST['productId'];

try {
    // Crear una nueva conexión PDO
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Establecer el modo de error PDO en excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para obtener los detalles del producto
    $sql = "SELECT * FROM tbadmproducto WHERE codigo = :codigo";
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    // Vincular parámetros
    $stmt->bindParam(':codigo', $productId);
    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado de la consulta como un array asociativo
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Si se encontró el producto, devolver los detalles como JSON
        echo json_encode($product);
    } else {
        // Si el producto no se encuentra, devolver un mensaje de error
        echo json_encode(array('error' => 'Producto no encontrado'));
    }
} catch (PDOException $e) {
    // Capturar cualquier excepción PDO y mostrar el mensaje de error
    echo json_encode(array('error' => 'Error al cargar detalles del producto: ' . $e->getMessage()));
}

// Cerrar la conexión PDO
$conn = null;
?>
