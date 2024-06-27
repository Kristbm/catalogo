<?php
// Incluir el archivo de conexión
include 'conn.php';

try {
    // Consulta SQL para obtener todos los productos ordenados alfabéticamente por descripción
    $sql = "SELECT descripcion, codigo, precio, preciomayor, vista 
    FROM tbadmproducto 
    WHERE vista = 1 
    ORDER BY descripcion ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejar los errores de la consulta
    die("Error al obtener productos: " . $e->getMessage());
}

// Devolver los resultados en formato JSON
header('Content-Type: application/json');
echo json_encode($results);
?>
