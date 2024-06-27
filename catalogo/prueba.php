<?php
// Incluir el archivo de conexión
require 'conn.php';

try {
    // Preparar la consulta SQL
    $sql = "SELECT * FROM tbadmproducto";
    $stmt = $pdo->prepare($sql);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener todos los resultados como un array asociativo
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mostrar los datos
    echo "<h2>Lista de Productos</h2>";
    echo "<ul>";
    foreach ($productos as $producto) {
        echo "<li>{$producto['descripcion']} - Precio: {$producto['precio']}</li>";
    }
    echo "</ul>";
} catch (PDOException $e) {
    // Manejar los errores de la conexión o la consulta
    echo "Error: " . $e->getMessage();
}
?>
